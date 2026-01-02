@php($client = $client ?? null)
@csrf
<div class="grid grid--2">
    <label class="form__field">
        <span>Nombre</span>
        <input type="text" class="input" name="name" value="{{ old('name', $client->name ?? '') }}" required>
        @error('name')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Documento</span>
        <input type="text" class="input" name="document" value="{{ old('document', $client->document ?? '') }}">
        @error('document')<span class="form__error">{{ $message }}</span>@enderror
    </label>
</div>
<div class="grid grid--2">
    <label class="form__field">
        <span>Email</span>
        <input type="email" class="input" name="email" value="{{ old('email', $client->email ?? '') }}">
        @error('email')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Teléfono</span>
        <input type="text" class="input" name="phone" value="{{ old('phone', $client->phone ?? '') }}">
        @error('phone')<span class="form__error">{{ $message }}</span>@enderror
    </label>
</div>
<label class="form__field">
    <span>Dirección</span>
    <input type="text" class="input" name="address" value="{{ old('address', $client->address ?? '') }}">
    @error('address')<span class="form__error">{{ $message }}</span>@enderror
</label>
<label class="form__field">
    <span>Notas</span>
    <textarea class="input" rows="4" name="notes" placeholder="Preferencias, detalles importantes...">{{ old('notes', $client->notes ?? '') }}</textarea>
    @error('notes')<span class="form__error">{{ $message }}</span>@enderror
</label>
<label class="form__field form__checkbox">
    <input type="checkbox" name="active" value="1" @checked(old('active', $client->active ?? true))>
    <span>Cliente activo</span>
</label>
<div class="form__actions">
    <a class="btn btn--ghost" href="{{ route('clients.index') }}">Cancelar</a>
    <button class="btn btn--primary" type="submit">Guardar</button>
</div>
