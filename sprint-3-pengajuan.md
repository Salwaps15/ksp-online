# Sprint 3: Pengajuan & Upload (Role: Sekolah)

**Tujuan:** Pihak sekolah dapat membuat entri pengajuan, mengunggah berbagai dokumen KSP, serta melihat catatan revisi jika ada.

## 1. Persiapan Link Storage
Sebelum bermain dengan file upload, PASTIKAN kamu menjalankan perintah ini di terminal agar folder `storage` bisa diakses secara publik (dari `public/storage`):
```bash
php artisan storage:link
```

## 2. Routing untuk Sekolah
Buka `routes/web.php` dan buat grup rute untuk Sekolah:
```php
use App\Http\Controllers\Sekolah\PengajuanController;

Route::middleware(['auth', 'role:sekolah'])->prefix('sekolah')->name('sekolah.')->group(function () {
    Route::resource('pengajuan', PengajuanController::class);
    
    // Custom route untuk form upload dokumen
    Route::post('pengajuan/{id}/upload-dokumen', [PengajuanController::class, 'uploadDokumen'])->name('pengajuan.upload');
    
    // Custom route untuk men-submit (mengubah status Draft -> Diajukan)
    Route::post('pengajuan/{id}/submit', [PengajuanController::class, 'submitPengajuan'])->name('pengajuan.submit');
});
```

## 3. Membuat Controller
```bash
php artisan make:controller Sekolah/PengajuanController
```

Buka `app/Http/Controllers/Sekolah/PengajuanController.php`.

### A. Index & Create
```php
namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanController extends Controller
{
    public function index()
    {
        // Hanya tampilkan pengajuan milik sekolah yang sedang login
        $pengajuans = Pengajuan::where('id_instansi', auth()->user()->id_instansi)
                               ->latest()
                               ->get();
        return view('sekolah.pengajuan.index', compact('pengajuans'));
    }

    public function create()
    {
        return view('sekolah.pengajuan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'tahun_ajaran' => 'required|string',
        ]);

        Pengajuan::create([
            'id_instansi' => auth()->user()->id_instansi,
            'semester' => $request->semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'status' => 'Draft' // Status default saat pertama kali dibuat
        ]);

        return redirect()->route('sekolah.pengajuan.index')->with('success', 'Draft Pengajuan berhasil dibuat. Silakan lengkapi dokumen.');
    }
}
```

### B. Halaman Detail (Upload File) & Logika Upload
Tambahkan method `show` dan `uploadDokumen` di controller yang sama.

```php
    public function show($id)
    {
        $pengajuan = Pengajuan::with(['documents', 'verifikasis'])->findOrFail($id);
        
        // Pastikan sekolah tidak bisa mengintip pengajuan sekolah lain
        if ($pengajuan->id_instansi !== auth()->user()->id_instansi) {
            abort(403);
        }

        // Daftar jenis dokumen yang wajib
        $jenis_dokumen = ['Cover', 'BAB I', 'BAB II', 'BAB III', 'BAB IV', 'BAB V', 'Lampiran'];

        return view('sekolah.pengajuan.show', compact('pengajuan', 'jenis_dokumen'));
    }

    public function uploadDokumen(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);

        // Hanya boleh upload jika statusnya Draft atau Direvisi
        if (!in_array($pengajuan->status, ['Draft', 'Direvisi'])) {
            return back()->with('error', 'Tidak bisa mengunggah dokumen pada status pengajuan ini.');
        }

        $request->validate([
            'jenis_dokumen' => 'required|in:Cover,BAB I,BAB II,BAB III,BAB IV,BAB V,Lampiran',
            'file_dokumen' => 'required|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        // Simpan file ke storage/app/public/ksp_dokumen
        $filePath = $request->file('file_dokumen')->store('ksp_dokumen', 'public');

        // Gunakan UpdateOrCreate (Jika jenis dokumen sudah ada, update file-nya. Jika belum, buat record baru)
        $document = Document::where('id_pengajuan', $pengajuan->id)
                            ->where('jenis_dokumen', $request->jenis_dokumen)
                            ->first();

        if ($document) {
            // Hapus file lama fisik dari server
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->update(['file_path' => $filePath]);
        } else {
            Document::create([
                'id_pengajuan' => $pengajuan->id,
                'jenis_dokumen' => $request->jenis_dokumen,
                'file_path' => $filePath
            ]);
        }

        return back()->with('success', 'Dokumen ' . $request->jenis_dokumen . ' berhasil diunggah.');
    }
```

### C. Finalisasi / Submit Pengajuan
Sekolah akan menekan tombol "Ajukan" setelah semua dokumen lengkap.
```php
    public function submitPengajuan($id)
    {
        $pengajuan = Pengajuan::with('documents')->findOrFail($id);

        // Opsional: Validasi apakah semua 7 dokumen sudah diupload
        if ($pengajuan->documents->count() < 7) {
            return back()->with('error', 'Dokumen belum lengkap! Harap upload semua dokumen wajib.');
        }

        $pengajuan->update(['status' => 'Diajukan']);

        return back()->with('success', 'Pengajuan KSP berhasil dikirim ke Dinas Pendidikan.');
    }
```

## 4. Struktur View (Halaman Detail)
Di halaman `resources/views/sekolah/pengajuan/show.blade.php`, buat *looping* untuk setiap jenis dokumen:

```html
@foreach($jenis_dokumen as $jenis)
    <div class="card">
        <h3>{{ $jenis }}</h3>
        
        <!-- Cek apakah dokumen ini sudah diupload -->
        @php
            $doc = $pengajuan->documents->where('jenis_dokumen', $jenis)->first();
        @endphp

        @if($doc)
            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">Lihat Dokumen</a>
        @else
            <span class="text-danger">Belum diunggah</span>
        @endif

        <!-- Form Upload (Hanya tampil jika status Draft/Direvisi) -->
        @if(in_array($pengajuan->status, ['Draft', 'Direvisi']))
        <form action="{{ route('sekolah.pengajuan.upload', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
            <input type="file" name="file_dokumen" required>
            <button type="submit">Upload</button>
        </form>
        @endif
    </div>
@endforeach
```

*Tambahkan juga di halaman ini: tabel riwayat yang melooping `$pengajuan->verifikasis` untuk menampilkan catatan revisi dari verifikator.*
