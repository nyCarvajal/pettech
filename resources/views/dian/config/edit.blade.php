@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Facturación electrónica</p>
        <h1>Configuración DIAN</h1>
        <p class="text-muted" style="margin: 6px 0 0; max-width: 760px;">
            Define la parametrización por tenant para el flujo de facturación electrónica.
            Esta pantalla guarda datos de habilitación sin acoplarse a un proveedor específico.
        </p>
    </div>
</div>

<div class="card" style="padding: 24px; max-width: 980px;">
    @if($errors->any())
        <div class="alert alert--danger" style="margin-bottom: 16px;">
            <strong>Revisa los campos del formulario:</strong>
            <ul style="margin: 8px 0 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('dian.config.update') }}">
        @csrf
        @method('PUT')

        <h3 style="margin: 0 0 10px;">Credenciales de software</h3>
        <div class="grid grid--2">
            <label class="form__field">
                <span>Software ID</span>
                <input class="input" type="text" name="software_id" value="{{ old('software_id', $config->software_id) }}" required>
                @error('software_id')<span class="form__error">{{ $message }}</span>@enderror
            </label>

            <label class="form__field">
                <span>PIN</span>
                <input class="input" type="text" name="pin" value="{{ old('pin', $config->pin) }}" required>
                @error('pin')<span class="form__error">{{ $message }}</span>@enderror
            </label>
        </div>

        <div class="grid grid--2">
            <label class="form__field">
                <span>Ruta del certificado</span>
                <input class="input" type="text" name="certificate_path" value="{{ old('certificate_path', $config->certificate_path) }}" placeholder="storage/certs/dian.p12" required>
                @error('certificate_path')<span class="form__error">{{ $message }}</span>@enderror
            </label>

            <label class="form__field">
                <span>Clave del certificado</span>
                <input class="input" type="password" name="certificate_password" value="{{ old('certificate_password', $config->certificate_password) }}" required>
                @error('certificate_password')<span class="form__error">{{ $message }}</span>@enderror
            </label>
        </div>

        <h3 style="margin: 20px 0 10px;">Resolución y numeración</h3>
        <div class="grid grid--2">
            <label class="form__field">
                <span>Ambiente</span>
                <select class="input" name="environment" required>
                    <option value="test" @selected(old('environment', $config->environment) === 'test')>Pruebas (test)</option>
                    <option value="prod" @selected(old('environment', $config->environment) === 'prod')>Producción (prod)</option>
                </select>
                @error('environment')<span class="form__error">{{ $message }}</span>@enderror
            </label>

            <label class="form__field">
                <span>Número de resolución</span>
                <input class="input" type="text" name="resolution_number" value="{{ old('resolution_number', $config->resolution_number) }}" required>
                @error('resolution_number')<span class="form__error">{{ $message }}</span>@enderror
            </label>
        </div>

        <div class="grid grid--3" style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px;">
            <label class="form__field">
                <span>Prefijo</span>
                <input class="input" type="text" name="prefix" value="{{ old('prefix', $config->prefix) }}" required>
                @error('prefix')<span class="form__error">{{ $message }}</span>@enderror
            </label>

            <label class="form__field">
                <span>Rango desde</span>
                <input class="input" type="number" name="range_from" value="{{ old('range_from', $config->range_from) }}" min="1" required>
                @error('range_from')<span class="form__error">{{ $message }}</span>@enderror
            </label>

            <label class="form__field">
                <span>Rango hasta</span>
                <input class="input" type="number" name="range_to" value="{{ old('range_to', $config->range_to) }}" min="1" required>
                @error('range_to')<span class="form__error">{{ $message }}</span>@enderror
            </label>
        </div>

        <div class="form__actions" style="margin-top: 18px;">
            <a class="btn btn--ghost" href="{{ route('dian.invoices.index') }}">Ver estados DIAN</a>
            <button type="submit" class="btn btn--primary">Guardar configuración</button>
        </div>
    </form>
</div>
@endsection
