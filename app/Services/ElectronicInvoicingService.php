<?php

namespace App\Services;

use App\Models\ElectronicInvoice;
use App\Models\TenantDianConfig;
use Illuminate\Support\Facades\Log;

class ElectronicInvoicingService
{
    public function processPendingElectronicInvoice(ElectronicInvoice $electronicInvoice): ElectronicInvoice
    {
        if ($electronicInvoice->dian_status !== 'pending') {
            return $electronicInvoice;
        }

        $config = TenantDianConfig::query()
            ->where('tenant_id', $electronicInvoice->tenant_id)
            ->first();

        if (! $config) {
            throw new \RuntimeException('No existe configuración DIAN para este tenant.');
        }

        $xml = $this->buildXml($electronicInvoice);
        $signedXml = $this->signXml($xml, $config->certificate_path, $config->certificate_password);
        $providerResponse = $this->sendToProvider($signedXml, $config);

        $electronicInvoice->xml_path = $providerResponse['xml_path'] ?? ('dian/invoices/' . $electronicInvoice->invoice_id . '.xml');
        $electronicInvoice->response_json = $providerResponse;
        $electronicInvoice->last_error = null;

        if (($providerResponse['status'] ?? null) === 'accepted') {
            $electronicInvoice->dian_status = 'accepted';
            $electronicInvoice->cufe = $providerResponse['cufe'] ?? null;
            $electronicInvoice->accepted_at = now();
        } elseif (($providerResponse['status'] ?? null) === 'rejected') {
            $electronicInvoice->dian_status = 'rejected';
            $electronicInvoice->last_error = $providerResponse['message'] ?? 'Documento rechazado por DIAN.';
        } else {
            $electronicInvoice->dian_status = 'sent';
        }

        $electronicInvoice->sent_at = now();
        $electronicInvoice->save();

        Log::info('Factura electrónica procesada (stub)', [
            'electronic_invoice_id' => $electronicInvoice->id,
            'invoice_id' => $electronicInvoice->invoice_id,
            'dian_status' => $electronicInvoice->dian_status,
        ]);

        return $electronicInvoice;
    }

    public function buildXml(ElectronicInvoice $electronicInvoice): string
    {
        return sprintf('<Invoice><ID>%s</ID><Tenant>%s</Tenant></Invoice>', $electronicInvoice->invoice_id, $electronicInvoice->tenant_id);
    }

    public function signXml(string $xml, string $certificatePath, string $certificatePassword): string
    {
        return $xml . sprintf('<!-- signed-stub:%s:%s -->', $certificatePath, strlen($certificatePassword));
    }

    public function sendToProvider(string $signedXml, TenantDianConfig $config): array
    {
        return [
            'provider' => 'stub',
            'environment' => $config->environment,
            'status' => 'accepted',
            'cufe' => hash('sha256', $signedXml . microtime(true)),
            'xml_path' => 'dian/stub/' . now()->format('YmdHis') . '.xml',
            'message' => 'Envío simulado exitoso.',
        ];
    }
}
