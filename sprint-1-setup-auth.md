# Sprint 1: Setup Database, Model, & Autentikasi

**Tujuan:** Membangun fondasi database, mendefinisikan relasi antar model, dan menyusun sistem Autentikasi berdasarkan Role.

## 1. Konfigurasi Database
Buka file `.env` di root folder project. Pastikan konfigurasi ini sesuai dengan MySQL kamu:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_ksp_online  # Sesuaikan nama DB
DB_USERNAME=root
DB_PASSWORD=
```
*(Jangan lupa buat database `db_ksp_online` di phpMyAdmin / TablePlus)*

---

## 2. Generate Migration & Model
Buka terminal, jalankan perintah ini secara berurutan untuk membuat Model dan file Migration:
```bash
php artisan make:model Instansi -m
php artisan make:model Pengajuan -m
php artisan make:model Document -m
php artisan make:model Verifikasi -m
```

Buka folder `database/migrations/`. Edit masing-masing file migration yang baru saja dibuat.

### A. Tabel Instansi (`create_instansi_table.php`)
```php
public function up()
{
    Schema::create('instansi', function (Blueprint $table) {
        $table->id();
        $table->string('nama_instansi');
        $table->text('alamat');
        $table->string('npsn')->unique();
        $table->string('kepala_sekolah');
        $table->string('kontak');
        $table->timestamps();
        $table->softDeletes(); // Untuk soft delete agar histori data tidak hilang
    });
}
```

### B. Tabel Users (`create_users_table.php`)
Cari file migration users bawaan Laravel, tambahkan 2 kolom ini setelah kolom password:
```php
$table->enum('role', ['admin', 'sekolah', 'verifikator'])->default('sekolah');
$table->foreignId('id_instansi')->nullable()->constrained('instansi')->nullOnDelete();
```

### C. Tabel Pengajuan (`create_pengajuan_table.php`)
```php
public function up()
{
    Schema::create('pengajuan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_instansi')->constrained('instansi')->cascadeOnDelete();
        $table->string('semester');
        $table->string('tahun_ajaran');
        $table->enum('status', ['Draft', 'Diajukan', 'Direvisi', 'Disetujui'])->default('Draft');
        $table->timestamps();
    });
}
```

### D. Tabel Document (`create_documents_table.php`)
```php
public function up()
{
    Schema::create('documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_pengajuan')->constrained('pengajuan')->cascadeOnDelete();
        $table->enum('jenis_dokumen', ['Cover', 'BAB I', 'BAB II', 'BAB III', 'BAB IV', 'BAB V', 'Lampiran']);
        $table->string('file_path');
        $table->timestamps();
    });
}
```

### E. Tabel Verifikasi (`create_verifikasi_table.php`)
```php
public function up()
{
    Schema::create('verifikasi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_pengajuan')->constrained('pengajuan')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Verifikator
        $table->date('tanggal_verifikasi');
        $table->enum('status_hasil', ['Disetujui', 'Revisi']);
        $table->text('catatan')->nullable();
        $table->timestamps();
    });
}
```

Setelah semua migration ditulis, jalankan:
```bash
php artisan migrate
```

---

## 3. Konfigurasi Model & Relasi
Buka folder `app/Models/` dan tambahkan relasi. Jangan lupa tambahkan `protected $guarded = ['id'];` di semua model agar mass assignment diizinkan.

**`app/Models/Instansi.php`**
```php
protected $guarded = ['id'];

public function pengajuan() {
    return $this->hasMany(Pengajuan::class, 'id_instansi');
}
public function users() {
    return $this->hasMany(User::class, 'id_instansi');
}
```

**`app/Models/User.php`**
```php
// Tambahkan relasi di bawah method yang sudah ada
public function instansi() {
    return $this->belongsTo(Instansi::class, 'id_instansi');
}
```

**`app/Models/Pengajuan.php`**
```php
protected $guarded = ['id'];

public function instansi() {
    return $this->belongsTo(Instansi::class, 'id_instansi');
}
public function documents() {
    return $this->hasMany(Document::class, 'id_pengajuan');
}
public function verifikasis() {
    return $this->hasMany(Verifikasi::class, 'id_pengajuan');
}
```

**`app/Models/Document.php`** dan **`app/Models/Verifikasi.php`**
```php
protected $guarded = ['id'];

public function pengajuan() {
    return $this->belongsTo(Pengajuan::class, 'id_pengajuan');
}
```

---

## 4. Setup Authentication (Laravel Breeze) & Role Middleware
Kita akan menggunakan Laravel Breeze untuk UI Login & Register bawaan.
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install
npm run build
```

**Membuat Middleware untuk Role**
```bash
php artisan make:middleware RoleMiddleware
```

Buka `app/Http/Middleware/RoleMiddleware.php`:
```php
public function handle(Request $request, Closure $next, string $role): Response
{
    if (!auth()->check()) {
        return redirect('/login');
    }

    if (auth()->user()->role !== $role) {
        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request);
}
```

**Registrasi Middleware (Laravel 12 / `bootstrap/app.php`)**
Buka `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

## 5. Pembuatan Seeder Awal (Opsional tapi Penting)
Buat seeder untuk akun Admin agar kamu bisa langsung login.
```bash
php artisan make:seeder DatabaseSeeder
```
Buka `database/seeders/DatabaseSeeder.php`:
```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

public function run(): void
{
    User::create([
        'name' => 'Super Admin',
        'email' => 'admin@ksp.com',
        'password' => Hash::make('password123'),
        'role' => 'admin'
    ]);
}
```
Jalankan seeder:
```bash
php artisan db:seed
```
Sekarang fondasi project sudah selesai! Lanjut ke Sprint 2.
