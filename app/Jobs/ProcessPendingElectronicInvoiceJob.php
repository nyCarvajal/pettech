<?php

namespace App\Jobs;

use App\Models\ElectronicInvoice;
use App\Services\ElectronicInvoicingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPendingElectronicInvoiceJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 180, 600];

    public string $queue = 'dian';

    public function __construct(public int $electronicInvoiceId)
    {
    }

    public function handle(ElectronicInvoicingService $service): void
    {
        $electronicInvoice = ElectronicInvoice::query()->find($this->electronicInvoiceId);

        if (! $electronicInvoice) {
            return;
        }

        try {
            $service->processPendingElectronicInvoice($electronicInvoice);
        } catch (\Throwable $exception) {
            $electronicInvoice->update([
                'dian_status' => 'error',
                'last_error' => $exception->getMessage(),
            ]);

            Log::error('Error procesando factura electrónica', [
                'electronic_invoice_id' => $this->electronicInvoiceId,
                'attempt' => $this->attempts(),
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(?\Throwable $exception): void
    {
        Log::critical('Falló definitivamente el job DIAN', [
            'electronic_invoice_id' => $this->electronicInvoiceId,
            'error' => $exception?->getMessage(),
        ]);
    }
}
