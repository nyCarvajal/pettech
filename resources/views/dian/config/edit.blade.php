@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <p class="eyebrow">Facturación electrónica</p>
        <h1>Configuración DIAN</h1>
    </div>
</div>

<div class="card" style="padding:20px; max-width: 880px;">
    <form method="POST" action="{{ route('dian.config.update') }}" class="stack" style="gap: 12px;">
        @csrf
        @method('PUT')

        <label>Software ID
            <input type="text" name="software_id" value="{{ old('software_id', $config->software_id) }}" required>
        </label>
        <label>PIN
            <input type="text" name="pin" value="{{ old('pin', $config->pin) }}" required>
        </label>
        <label>Ruta certificado
            <input type="text" name="certificate_path" value="{{ old('certificate_path', $config->certificate_path) }}" required>
        </label>
        <label>Clave certificado
            <input type="password" name="certificate_password" value="{{ old('certificate_password', $config->certificate_password) }}" required>
        </label>
        <label>Ambiente
            <select name="environment" required>
                <option value="test" @selected(old('environment', $config->environment) === 'test')>test</option>
                <option value="prod" @selected(old('environment', $config->environment) === 'prod')>prod</option>
            </select>
        </label>
        <label>Número resolución
            <input type="text" name="resolution_number" value="{{ old('resolution_number', $config->resolution_number) }}" required>
        </label>
        <label>Prefijo
            <input type="text" name="prefix" value="{{ old('prefix', $config->prefix) }}" required>
        </label>
        <label>Rango desde
            <input type="number" name="range_from" value="{{ old('range_from', $config->range_from) }}" min="1" required>
        </label>
        <label>Rango hasta
            <input type="number" name="range_to" value="{{ old('range_to', $config->range_to) }}" min="1" required>
        </label>

        @if($errors->any())
            <div class="alert alert--danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <button type="submit" class="btn btn--primary">Guardar configuración</button>
        </div>
    </form>
</div>
@endsection
