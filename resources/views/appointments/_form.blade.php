@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <label class="block">
        <span class="text-sm font-medium">Código</span>
        <input type="text" name="code" class="mt-1 block w-full rounded border-gray-300" value="{{ old('code', $appointment->code ?? '') }}" placeholder="Autogenerado si vacío">
    </label>

    <label class="block">
        <span class="text-sm font-medium">Tutor</span>
        <select name="customer_id" class="mt-1 block w-full rounded border-gray-300" required>
            <option value="">Selecciona</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $appointment->customer_id ?? '') == $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Paciente</span>
        <select name="pet_id" class="mt-1 block w-full rounded border-gray-300" required>
            <option value="">Selecciona</option>
            @foreach($pets as $pet)
                <option value="{{ $pet->id }}" @selected(old('pet_id', $appointment->pet_id ?? '') == $pet->id)>{{ $pet->name }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Servicio</span>
        <select name="service_type" class="mt-1 block w-full rounded border-gray-300" required>
            @foreach($serviceTypes as $serviceType)
                <option value="{{ $serviceType }}" @selected(old('service_type', $appointment->service_type ?? '') === $serviceType)>{{ ucfirst($serviceType) }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Inicio</span>
        <input type="datetime-local" name="start_at" class="mt-1 block w-full rounded border-gray-300" value="{{ old('start_at', isset($appointment) ? $appointment->start_at?->format('Y-m-d\\TH:i') : '') }}" required>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Fin</span>
        <input type="datetime-local" name="end_at" class="mt-1 block w-full rounded border-gray-300" value="{{ old('end_at', isset($appointment) ? $appointment->end_at?->format('Y-m-d\\TH:i') : '') }}" required>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Peluquero asignado</span>
        <select name="assigned_to_user_id" class="mt-1 block w-full rounded border-gray-300">
            <option value="">Sin asignar</option>
            @foreach($groomers as $groomer)
                <option value="{{ $groomer->id }}" @selected(old('assigned_to_user_id', $appointment->assigned_to_user_id ?? '') == $groomer->id)>{{ $groomer->name }}</option>
            @endforeach
        </select>
    </label>

    <label class="block">
        <span class="text-sm font-medium">Estado</span>
        <select name="status" class="mt-1 block w-full rounded border-gray-300">
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $appointment->status ?? 'scheduled') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
        </select>
    </label>

    <label class="block md:col-span-2">
        <span class="text-sm font-medium">Notas</span>
        <textarea name="notes" rows="3" class="mt-1 block w-full rounded border-gray-300">{{ old('notes', $appointment->notes ?? '') }}</textarea>
    </label>
</div>

@if($errors->any())
    <div class="mt-4 rounded bg-red-100 text-red-700 p-3">
        <ul class="list-disc ml-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-6 flex gap-2">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Guardar</button>
    <a href="{{ route('appointments.day') }}" class="px-4 py-2 border rounded">Cancelar</a>
</div>
