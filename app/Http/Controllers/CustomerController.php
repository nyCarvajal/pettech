<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\Customer\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService)
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $customers = Customer::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->search($search)
            ->orderBy('last_name')
            ->paginate(12)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->customerService->create($request->validated(), $request->user());

        return redirect()->route('customers.index')->with('status', 'Tutor creado correctamente');
    }

    public function show(Customer $customer): View
    {
        $customer->load('pets');

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->update($customer, $request->validated());

        return redirect()->route('customers.show', $customer)->with('status', 'Tutor actualizado');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Tutor eliminado');
    }

    public function search(Request $request): JsonResponse
    {
        $search = $request->string('q')->toString();

        $data = Customer::query()
            ->where('tenant_id', $request->user()->tenant_id)
            ->search($search)
            ->orderBy('last_name')
            ->limit(15)
            ->get(['id', 'first_name', 'last_name', 'phone'])
            ->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'phone' => $customer->phone,
            ]);

        return response()->json($data);
    }
}
