<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::when($request->search, function ($q) use ($request) {
            $q->where(function ($q2) use ($request) {
                $q2->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('employee.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('employee.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $validated['password'] = bcrypt('password');

        Customer::create($validated);

        return redirect()->route('employee.customers.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan!');
    }

    public function show(Customer $customer)
    {
        return view('employee.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('employee.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('employee.customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Customer $customer)
    {
        // For customers, let employees delete if needed? 
        // Or keep it restricted? Usually customers aren't as "financial" as transactions.
        // I'll keep it for now as per "Manajemen Data Pelanggan" requirement.
        $customer->delete();

        return redirect()->route('employee.customers.index')
            ->with('success', 'Data pelanggan berhasil dihapus!');
    }
}
