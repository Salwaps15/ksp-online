# Sprint 5: Monitoring & Reporting (Dashboard)

**Tujuan:** Menyediakan antarmuka dashboard yang merangkum data secara statistik (Card KPI), dan fitur opsional untuk meng-export laporan.

## 1. Routing Dashboard Statistik
Buka `routes/web.php`. Biasakan menaruh route dashboard setelah user login (bisa digabung di middleware `auth` biasa, tanpa check role spesifik, lalu di controllernya baru dibedakan view-nya jika perlu).

```php
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
```

## 2. Membuat Dashboard Controller
```bash
php artisan make:controller DashboardController
```

Buka `app/Http/Controllers/DashboardController.php` dan buat aggregate query:

```php
namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Instansi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Jika yang login adalah sekolah, tampilkan statistik spesifik sekolahnya saja
        if ($user->role === 'sekolah') {
            $data = [
                'total_pengajuan' => Pengajuan::where('id_instansi', $user->id_instansi)->count(),
                'disetujui' => Pengajuan::where('id_instansi', $user->id_instansi)->where('status', 'Disetujui')->count(),
                'revisi' => Pengajuan::where('id_instansi', $user->id_instansi)->where('status', 'Direvisi')->count(),
            ];
            return view('dashboard.sekolah', compact('data'));
        }

        // Jika yang login adalah Admin / Verifikator, tampilkan seluruh statistik global
        $data = [
            'total_sekolah' => Instansi::count(),
            'total_pengajuan' => Pengajuan::count(),
            'menunggu_verifikasi' => Pengajuan::where('status', 'Diajukan')->count(),
            'disetujui' => Pengajuan::where('status', 'Disetujui')->count(),
            'revisi' => Pengajuan::where('status', 'Direvisi')->count(),
        ];

        // Opsional: Jika ingin menampilkan grafik 5 pengajuan terbaru
        $pengajuan_terbaru = Pengajuan::with('instansi')->latest()->take(5)->get();

        return view('dashboard.admin', compact('data', 'pengajuan_terbaru'));
    }
}
```
*Note untuk Junior:* Buat 2 file Blade (`resources/views/dashboard/sekolah.blade.php` dan `admin.blade.php`) lalu gunakan variabel `$data` untuk menampilkannya di Card UI (menggunakan Bootstrap/Tailwind).

---

## 3. Export Laporan (Opsional)
Jika diwajibkan untuk membuat export data laporan dalam format Excel.

### A. Install Laravel Excel
```bash
composer require maatwebsite/excel
```

### B. Generate Export Class
```bash
php artisan make:export PengajuanExport --model=Pengajuan
```

Buka `app/Exports/PengajuanExport.php`. Sesuaikan logikanya agar rapi (tambahkan `WithHeadings` dan `WithMapping`):
```php
namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PengajuanExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Ambil data pengajuan beserta relasi instansi
        return Pengajuan::with('instansi')->get();
    }

    // Header Kolom di Excel
    public function headings(): array
    {
        return [
            'No',
            'Nama Sekolah',
            'NPSN',
            'Semester',
            'Tahun Ajaran',
            'Status',
            'Tanggal Dibuat'
        ];
    }

    // Mapping Value tiap Row
    public function map($pengajuan): array
    {
        return [
            $pengajuan->id,
            $pengajuan->instansi->nama_instansi,
            $pengajuan->instansi->npsn,
            $pengajuan->semester,
            $pengajuan->tahun_ajaran,
            $pengajuan->status,
            $pengajuan->created_at->format('d-m-Y')
        ];
    }
}
```

### C. Tambahkan Route & Logika Export di Controller
Di `routes/web.php` untuk Admin:
```php
use App\Http\Controllers\Admin\ReportController;
Route::get('/admin/export-pengajuan', [ReportController::class, 'export'])->name('admin.export');
```

Di Controller:
```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengajuanExport;

class ReportController extends Controller
{
    public function export() 
    {
        return Excel::download(new PengajuanExport, 'laporan-ksp.xlsx');
    }
}
```

Sekarang junior dev sudah memiliki *step-by-step* yang sangat jelas mulai dari kode Migration, Relasi, Routing, Controller, hingga referensi logika View!
