# Sprint 2: Master Data & Manajemen User (Role: Admin)

**Tujuan:** Membuat fitur agar `Admin` dapat mengelola data Instansi (Sekolah) dan mendaftarkan user (Sekolah/Verifikator).

## 1. Persiapan Routing (Admin)
Buka `routes/web.php` dan kelompokkan rute untuk Admin menggunakan middleware role yang sudah dibuat di Sprint 1.

```php
use App\Http\Controllers\Admin\InstansiController;
use App\Http\Controllers\Admin\UserController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('instansi', InstansiController::class);
    Route::resource('user', UserController::class);
});
```

---

## 2. CRUD Master Instansi
Buat controller:
```bash
php artisan make:controller Admin/InstansiController --resource
```

Buka `app/Http/Controllers/Admin/InstansiController.php`. Lengkapi logikanya:

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use Illuminate\Http\Request;

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
            'npsn' => 'required|string|unique:instansis,npsn',
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
            'npsn' => 'required|string|unique:instansis,npsn,' . $instansi->id,
            'alamat' => 'required|string',
            'kepala_sekolah' => 'required|string',
            'kontak' => 'required|string',
        ]);

        $instansi->update($validated);
        return redirect()->route('admin.instansi.index')->with('success', 'Data Instansi berhasil diperbarui.');
    }

    public function destroy(Instansi $instansi)
    {
        $instansi->delete(); // Ini otomatis Soft Delete karena Migrationnya pakai softDeletes()
        return redirect()->route('admin.instansi.index')->with('success', 'Data berhasil dihapus.');
    }
}
```

**Panduan View (`resources/views/admin/instansi/`):**
1. Buat folder `admin/instansi/` di dalam `resources/views/`.
2. Buat file `index.blade.php` berisi tabel yang me-looping variabel `$instansis`.
3. Buat form di `create.blade.php` dan `edit.blade.php` (method POST/PUT).

---

## 3. CRUD Manajemen User
Buat controller:
```bash
php artisan make:controller Admin/UserController --resource
```

Buka `app/Http/Controllers/Admin/UserController.php`.

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instansi;
use Illuminate\Http\Request;
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
            'id_instansi' => 'nullable|exists:instansis,id|required_if:role,sekolah',
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
            'id_instansi' => 'nullable|exists:instansis,id|required_if:role,sekolah',
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
```

**Instruksi Tambahan untuk Junior Programmer:**
- Pada form view di `resources/views/admin/user/create.blade.php`, gunakan JavaScript murni atau Alpine.js untuk menyembunyikan input select `id_instansi` jika `role` yang dipilih adalah "admin" atau "verifikator". Instansi hanya relevan untuk role "sekolah".
