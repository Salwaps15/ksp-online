<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Instansi;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Panggil user beserta relasi instansinya
        $users = User::with('instansi')->latest()->get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $instansis = Instansi::all(); // Untuk dropdown pilihan instansi
        return view('admin.user.create', compact('instansis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,sekolah,verifikator',
            'id_instansi' => 'nullable|exists:instansi,id|required_if:role,sekolah',
        ], [
            'id_instansi.required_if' => 'Instansi wajib diisi jika role adalah sekolah.'
        ]);

        // Enkripsi password
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $instansis = Instansi::all();
        return view('admin.user.edit', compact('user', 'instansis'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,sekolah,verifikator',
            'id_instansi' => 'nullable|exists:instansi,id|required_if:role,sekolah',
        ];

        // Jika password diisi, berarti ingin ganti password
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // Hapus key password agar tidak terupdate menjadi null
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('admin.user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User dihapus.');
    }
}
