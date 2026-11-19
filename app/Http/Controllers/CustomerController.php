<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Cek apakah request dari AJAX DataTables
        if ($request->ajax()) {
            // Eager load relasi 'sales' buat ngitung total transaksi (sales_count)
            $data = Customer::withCount('sales')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('phone', function($row){
                    return $row->phone ?? '-';
                })
                ->addColumn('email', function($row){
                    return $row->email ?? '-';
                })
                ->addColumn('sales_badge', function($row){
                    return '<span class="badge bg-info text-dark">' . $row->sales_count . ' Kali</span>';
                })
                ->addColumn('action', function($row){
                    // Tombol Riwayat
                    $btn = '<a href="'.route('customers.show', $row->id).'" class="btn btn-sm btn-info text-white" title="Riwayat Belanja"><i class="bi bi-clock-history"></i></a> ';
                    
                    // Tombol Edit (memicu modal)
                    $btn .= '<button type="button" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editCustomerModal'.$row->id.'" title="Edit"><i class="bi bi-pencil-square"></i></button> ';
                    
                    // Tombol Hapus
                    $btn .= '<form action="'.route('customers.destroy', $row->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin hapus pelanggan ini?\')">'
                          . csrf_field() . method_field('DELETE') 
                          . '<button class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>';
                    
                    return $btn;
                })
                ->rawColumns(['sales_badge', 'action'])
                ->make(true);
        }

        // Kalau request biasa, tampilin view
        return view('customers.index');
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        // Ambil riwayat transaksi pelanggan ini
        $sales = $customer->sales()->latest()->paginate(10);
        return view('customers.show', compact('customer', 'sales'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        // Cek apakah pelanggan punya riwayat transaksi
        if ($customer->sales()->count() > 0) {
            return redirect()->route('customers.index')->with('error', 'Pelanggan sudah memiliki riwayat transaksi, tidak bisa dihapus.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}