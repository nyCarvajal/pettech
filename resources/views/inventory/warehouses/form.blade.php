<label class="form__field"><span>Nombre</span><input class="input" type="text" name="name" value="{{ old('name', $warehouse->name ?? '') }}" required></label>
<label class="form__field"><span>Código</span><input class="input" type="text" name="code" value="{{ old('code', $warehouse->code ?? '') }}" required></label>
<label class="form__field"><span>Ubicación</span><input class="input" type="text" name="location" value="{{ old('location', $warehouse->location ?? '') }}"></label>
<label><input type="checkbox" name="is_main" value="1" @checked(old('is_main', $warehouse->is_main ?? false))> Principal</label>
