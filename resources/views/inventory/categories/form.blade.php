<label class="form__field"><span>Nombre</span><input class="input" type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required></label>
@error('name')<p class="muted">{{ $message }}</p>@enderror
