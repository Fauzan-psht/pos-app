<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;

class CustomerController extends Controller implements HasMiddleware
{
    // Admin & Kasir boleh akses, tapi HANYA admin yang boleh hapus
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Customer::withCount('sales');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $customers = $query->latest()->paginate(10)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,11}$/', 'unique:customers,phone'],
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
        ], [
            'phone.regex' => 'Format nomor HP tidak valid (harus diawali 08 dan 10-13 digit).',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['sales' => function($query) {
            $query->latest()->take(10); // Tampilkan 10 transaksi terakhir
        }]);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.form', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^08[0-9]{8,11}$/', Rule::unique('customers')->ignore($customer->id)],
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
        ], [
            'phone.regex' => 'Format nomor HP tidak valid (harus diawali 08 dan 10-13 digit).',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete(); // Relasi sales akan menjadi NULL (nullOnDelete)
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}