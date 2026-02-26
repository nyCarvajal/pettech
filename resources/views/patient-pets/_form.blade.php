@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div><label class="block text-sm">Nombre</label><input name="name" value="{{ old('name', $pet->name ?? '') }}" class="mt-1 w-full rounded border-gray-300" required></div>
    <div><label class="block text-sm">Especie</label><input name="species" value="{{ old('species', $pet->species ?? '') }}" class="mt-1 w-full rounded border-gray-300" required></div>
    <div><label class="block text-sm">Raza</label><input name="breed" value="{{ old('breed', $pet->breed ?? '') }}" class="mt-1 w-full rounded border-gray-300"></div>
    <div><label class="block text-sm">Sexo</label>
        <select name="sex" class="mt-1 w-full rounded border-gray-300" required>
            @foreach(['male' => 'Macho', 'female' => 'Hembra', 'unknown' => 'Sin definir'] as $val => $label)
                <option value="{{ $val }}" @selected(old('sex', $pet->sex ?? 'unknown') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div><label class="block text-sm">Nacimiento</label><input type="date" name="birthdate" value="{{ old('birthdate', isset($pet)?optional($pet->birthdate)->format('Y-m-d'):'') }}" class="mt-1 w-full rounded border-gray-300"></div>
    <div><label class="block text-sm">Color</label><input name="color" value="{{ old('color', $pet->color ?? '') }}" class="mt-1 w-full rounded border-gray-300"></div>
</div>
<div class="mt-4"><label class="block text-sm">Notas</label><textarea name="notes" rows="3" class="mt-1 w-full rounded border-gray-300">{{ old('notes', $pet->notes ?? '') }}</textarea></div>

<div class="mt-6 rounded border p-4">
    <h3 class="font-semibold">Tutores asociados</h3>
    <input type="text" id="customerFilter" placeholder="Buscar tutor por nombre o teléfono" class="mt-2 w-full rounded border-gray-300">
    <div id="customerList" class="mt-3 space-y-2 max-h-64 overflow-y-auto">
        @php
            $selected = collect(old('customer_links', isset($pet) ? $pet->customers->map(fn($c) => ['customer_id' => $c->id, 'relationship' => $c->pivot->relationship, 'is_primary' => (bool) $c->pivot->is_primary])->values()->all() : []))->keyBy('customer_id');
        @endphp
        @foreach($customers as $customer)
            @php $row = $selected[$customer->id] ?? null; @endphp
            <div class="customer-item rounded border p-2" data-search="{{ strtolower($customer->full_name.' '.$customer->phone) }}">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="customer_links[{{ $loop->index }}][customer_id]" value="{{ $customer->id }}" @checked($row !== null)>
                    <span>{{ $customer->full_name }} <small class="text-gray-500">{{ $customer->phone }}</small></span>
                </label>
                <div class="mt-2 grid grid-cols-2 gap-2 pl-6">
                    <select name="customer_links[{{ $loop->index }}][relationship]" class="rounded border-gray-300 text-sm">
                        <option value="owner" @selected(($row['relationship'] ?? 'owner') === 'owner')>Titular</option>
                        <option value="other" @selected(($row['relationship'] ?? '') === 'other')>Otro</option>
                    </select>
                    <label class="flex items-center gap-1 text-sm">
                        <input type="checkbox" name="customer_links[{{ $loop->index }}][is_primary]" value="1" @checked((bool)($row['is_primary'] ?? false))>
                        Tutor principal
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mt-4 flex gap-2"><button class="rounded bg-blue-600 px-4 py-2 text-white">Guardar</button><a href="{{ route('patient-pets.index') }}" class="rounded border px-4 py-2">Cancelar</a></div>

<script>
    document.getElementById('customerFilter')?.addEventListener('input', function (e) {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll('.customer-item').forEach((item) => {
            item.style.display = item.dataset.search.includes(term) ? 'block' : 'none';
        });
    });
</script>
