<form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 bg-white p-4 rounded shadow">
    <input type="date" name="date" value="{{ $date }}" class="rounded border-gray-300">
    <select name="groomer_id" class="rounded border-gray-300">
        <option value="">Groomer</option>
        @foreach($groomers as $groomer)
            <option value="{{ $groomer->id }}" @selected(($filters['groomer_id'] ?? '') == $groomer->id)>{{ $groomer->name }}</option>
        @endforeach
    </select>
    <select name="status" class="rounded border-gray-300">
        <option value="">Estado</option>
        @foreach($statuses as $status)
            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
        @endforeach
    </select>
    <select name="service_type" class="rounded border-gray-300">
        <option value="">Tipo</option>
        @foreach($serviceTypes as $serviceType)
            <option value="{{ $serviceType }}" @selected(($filters['service_type'] ?? '') === $serviceType)>{{ ucfirst($serviceType) }}</option>
        @endforeach
    </select>
    <button class="px-4 py-2 bg-gray-900 text-white rounded">Filtrar</button>
</form>
