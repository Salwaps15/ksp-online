# Sprint 4: Verifikasi & Review (Role: Verifikator)

**Tujuan:** Verifikator dari dinas dapat memantau pengajuan masuk, melihat dokumen, mendownloadnya, dan memberikan keputusan verifikasi beserta catatannya.

## 1. Routing untuk Verifikator
Buka `routes/web.php` dan kelompokkan rute untuk role `verifikator`:

```php
use App\Http\Controllers\Verifikator\VerifikasiController;

Route::middleware(['auth', 'role:verifikator'])->prefix('verifikator')->name('verifikator.')->group(function () {
    // List pengajuan masuk
    Route::get('pengajuan', [VerifikasiController::class, 'index'])->name('pengajuan.index');
    
    // Detail pengajuan (review dokumen)
    Route::get('pengajuan/{id}', [VerifikasiController::class, 'show'])->name('pengajuan.show');
    
    // Action menyetujui / merevisi
    Route::post('pengajuan/{id}/proses', [VerifikasiController::class, 'prosesVerifikasi'])->name('pengajuan.proses');
});
```

## 2. Membuat Controller Verifikasi
```bash
php artisan make:controller Verifikator/VerifikasiController
```

Buka `app/Http/Controllers/Verifikator/VerifikasiController.php`.

### A. List Pengajuan Masuk (Index)
```php
namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Verifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Query builder dengan Eager Loading relasi Instansi
        // Hanya tampilkan yang BUKAN status 'Draft'
        $query = Pengajuan::with('instansi')->where('status', '!=', 'Draft')->latest();

        // Fitur Filter Sederhana (Jika ada parameter ?status=Diajukan di URL)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $pengajuans = $query->paginate(10); // Gunakan Pagination agar tidak berat

        return view('verifikator.pengajuan.index', compact('pengajuans'));
    }
```

### B. Detail Pengajuan (Review & Download File)
```php
    public function show($id)
    {
        // Load data pengajuan lengkap dengan instansi, documents, dan histori verifikasi
        $pengajuan = Pengajuan::with(['instansi', 'documents', 'verifikasis.user'])->findOrFail($id);

        return view('verifikator.pengajuan.show', compact('pengajuan'));
    }
```
*Note untuk Junior Dev:* Di view Blade (`show.blade.php`), cukup looping tabel `documents` dan sediakan tombol link yang memanggil `<a href="{{ asset('storage/'.$doc->file_path) }}" download>Download</a>` agar Verifikator bisa mengunduh file.

### C. Proses Validasi & Input Catatan
Di sinilah logika inti untuk merubah status dan menyimpan log verifikasi. Karena kita mengubah dua tabel (`pengajuans` dan `verifikasis`), kita sebaiknya menggunakan `DB::transaction()` agar aman.

```php
    public function prosesVerifikasi(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        $request->validate([
            'status_hasil' => 'required|in:Disetujui,Revisi',
            'catatan' => 'nullable|string', // Wajib diisi di frontend jika statusnya Revisi
        ]);

        // Gunakan Transaction agar jika ada gagal simpan, semuanya di-rollback
        DB::transaction(function () use ($request, $pengajuan) {
            
            // 1. Catat ke tabel tb_verifikasi (histori log)
            Verifikasi::create([
                'id_pengajuan' => $pengajuan->id,
                'user_id' => auth()->user()->id, // Siapa verifikatornya
                'tanggal_verifikasi' => now(), // Tanggal hari ini
                'status_hasil' => $request->status_hasil,
                'catatan' => $request->catatan
            ]);

            // 2. Update status pengajuan utama (Karena database enum-nya 'Direvisi' bukan 'Revisi')
            $status_pengajuan = ($request->status_hasil === 'Revisi') ? 'Direvisi' : 'Disetujui';
            
            $pengajuan->update([
                'status' => $status_pengajuan
            ]);

        });

        return back()->with('success', 'Status pengajuan berhasil diperbarui menjadi ' . $request->status_hasil);
    }
}
```

## 3. Struktur View untuk Proses Verifikasi
Pada `resources/views/verifikator/pengajuan/show.blade.php`, tambahkan form untuk menyetujui / merevisi:

```html
<!-- Hanya tampilkan form jika statusnya masih Diajukan atau Direvisi -->
@if(in_array($pengajuan->status, ['Diajukan', 'Direvisi']))
<form action="{{ route('verifikator.pengajuan.proses', $pengajuan->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label>Keputusan Verifikasi</label>
        <select name="status_hasil" class="form-control" required>
            <option value="Disetujui">Setujui Pengajuan</option>
            <option value="Revisi">Kembalikan untuk Revisi</option>
        </select>
    </div>

    <div class="form-group mt-3">
        <label>Catatan Revisi / Pesan (Opsional)</label>
        <textarea name="catatan" class="form-control" rows="4"></textarea>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Submit Keputusan</button>
</form>
@endif
```
