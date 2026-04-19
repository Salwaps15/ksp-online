<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Instansi;

class InstansiController extends Controller
{
    public function index()
    {
        $instansis = Instansi::latest()->get();
        return view('admin.instansi.index', compact('instansis'));
    }

    public function create()
    {
        return view('admin.instansi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'npsn' => 'required|string|unique:instansi,npsn',
            'alamat' => 'required|string',
            'kepala_sekolah' => 'required|string',
            'kontak' => 'required|string',
        ]);

        Instansi::create($validated);
        return redirect()->route('admin.instansi.index')->with('success', 'Data Instansi berhasil ditambahkan.');
    }

    public function edit(Instansi $instansi)
    {
        return view('admin.instansi.edit', compact('instansi'));
    }

    public function update(Request $request, Instansi $instansi)
    {
        $validated = $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'npsn' => 'required|string|unique:instansi,npsn,' . $instansi->id,
            'alamat' => 'required|string',
            'kepala_sekolah' => 'required|string',
            'kontak' => 'required|string',
        ]);

        $instansi->update($validated);
        return redirect()->route('admin.instansi.index')->with('success', 'Data Instansi berhasil diperbarui.');
    }

    public function destroy(Instansi $instansi)
    {
        $instansi->delete();
        return redirect()->route('admin.instansi.index')->with('success', 'Data berhasil dihapus.');
    }
}
