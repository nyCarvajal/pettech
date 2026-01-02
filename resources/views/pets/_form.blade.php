@php($pet = $pet ?? null)
@csrf
<div class="grid grid--2">
    <label class="form__field">
        <span>Nombre</span>
        <input type="text" class="input" name="name" value="{{ old('name', $pet->name ?? '') }}" required>
        @error('name')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Especie</span>
        <input type="text" class="input" name="species" value="{{ old('species', $pet->species ?? '') }}">
        @error('species')<span class="form__error">{{ $message }}</span>@enderror
    </label>
</div>
<div class="grid grid--2">
    <label class="form__field">
        <span>Raza</span>
        <input type="text" class="input" name="breed" value="{{ old('breed', $pet->breed ?? '') }}">
        @error('breed')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Tamaño</span>
        <input type="text" class="input" name="size" value="{{ old('size', $pet->size ?? '') }}">
        @error('size')<span class="form__error">{{ $message }}</span>@enderror
    </label>
</div>
<div class="grid grid--2">
    <label class="form__field">
        <span>Fecha de nacimiento</span>
        <input type="date" class="input" name="birthdate" value="{{ old('birthdate', optional($pet?->birthdate)->format('Y-m-d')) }}">
        @error('birthdate')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Sexo</span>
        <input type="text" class="input" name="sex" value="{{ old('sex', $pet->sex ?? '') }}" placeholder="Hembra, macho...">
        @error('sex')<span class="form__error">{{ $message }}</span>@enderror
    </label>
</div>
<div class="grid grid--2">
    <label class="form__field">
        <span>Color</span>
        <input type="text" class="input" name="color" value="{{ old('color', $pet->color ?? '') }}">
        @error('color')<span class="form__error">{{ $message }}</span>@enderror
    </label>
    <label class="form__field">
        <span>Alergias</span>
        <textarea class="input" rows="3" name="allergies" placeholder="Medicamentos o ingredientes a evitar">{{ old('allergies', $pet->allergies ?? '') }}</textarea>
        @error('allergies')<span class="form__error">{{ $message }}</span>@enderror
    </label>
</div>
<label class="form__field">
    <span>Notas de comportamiento</span>
    <textarea class="input" rows="3" name="behavior_notes" placeholder="Reacciones, manejo recomendado">{{ old('behavior_notes', $pet->behavior_notes ?? '') }}</textarea>
    @error('behavior_notes')<span class="form__error">{{ $message }}</span>@enderror
</label>
<label class="form__field">
    <span>Preferencias de grooming</span>
    <textarea class="input" rows="3" name="grooming_preferences" placeholder="Cortes favoritos, productos preferidos">{{ old('grooming_preferences', $pet->grooming_preferences ?? '') }}</textarea>
    @error('grooming_preferences')<span class="form__error">{{ $message }}</span>@enderror
</label>
<label class="form__field form__checkbox">
    <input type="checkbox" name="active" value="1" @checked(old('active', $pet->active ?? true))>
    <span>Mascota activa</span>
</label>
<div class="form__actions">
    <a class="btn btn--ghost" href="{{ url()->previous() }}">Cancelar</a>
    <button class="btn btn--primary" type="submit">Guardar</button>
</div>
