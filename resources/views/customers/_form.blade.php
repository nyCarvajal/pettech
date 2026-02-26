@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Nombre</label>
        <input name="first_name" value="{{ old('first_name', $customer->first_name ?? '') }}" class="mt-1 w-full rounded border-gray-300" required>
    </div>
    <div>
        <label class="block text-sm font-medium">Apellido</label>
        <input name="last_name" value="{{ old('last_name', $customer->last_name ?? '') }}" class="mt-1 w-full rounded border-gray-300" required>
    </div>
    <div>
        <label class="block text-sm font-medium">Teléfono</label>
        <input name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-1 w-full rounded border-gray-300" required>
    </div>
    <div>
        <label class="block text-sm font-medium">Email</label>
        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Documento</label>
        <input name="document" value="{{ old('document', $customer->document ?? '') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Dirección</label>
        <input name="address" value="{{ old('address', $customer->address ?? '') }}" class="mt-1 w-full rounded border-gray-300">
    </div>
</div>
<div class="mt-4">
    <label class="block text-sm font-medium">Notas</label>
    <textarea name="notes" class="mt-1 w-full rounded border-gray-300" rows="4">{{ old('notes', $customer->notes ?? '') }}</textarea>
</div>
<div class="mt-4 flex gap-2">
    <button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button>
    <a href="{{ route('customers.index') }}" class="rounded border px-4 py-2">Cancelar</a>
</div>
