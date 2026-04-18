saya akan membuat aplikasi ksp online, ksp online adalah website untuk mengajuan kurikulum satuan pendidikan yang dikirimkan oleh sekolah" ke dinas pendidikan khususnya bidang pk-plk sebagai verifikator. fitur"nya sebagai berikut . Fitur untuk Sekolah (User Pengaju)

Fitur utama untuk pihak sekolah:

✏️ Pengajuan KSP
Input data sekolah (atau pilih jika sudah ada)
Input semester & tahun ajaran
Submit pengajuan KSP
📤 Upload Dokumen KSP
Upload:
Cover
BAB I – V
Lampiran
Bisa upload bertahap (tidak harus sekaligus)
Replace / update file
📊 Status Pengajuan
Melihat status:
Draft
Diajukan
Direvisi
Disetujui
📝 Lihat Catatan Revisi
Menampilkan komentar dari verifikator
Bisa jadi acuan perbaikan
🔄 Revisi & Upload Ulang
Upload ulang dokumen setelah revisi
Riwayat revisi tersimpan
🔹 2. Fitur untuk Verifikator (Admin/Dinas)

Ini yang bikin sistemmu “hidup” 👇

🔍 Dashboard Verifikasi
List pengajuan masuk
Filter:
Kab/Kota
Status
Semester
📂 Review Dokumen
Preview dokumen langsung di web
Download dokumen
📝 Input Catatan
Isi catatan revisi
Bisa umum atau per dokumen
✅ Validasi KSP
Tombol:
Setujui
Revisi
📜 Riwayat Verifikasi
Melihat histori pengecekan
Tracking perubahan
🔹 3. Fitur Admin Sistem

Kalau kamu mau lebih “niat” lagi:

👥 Manajemen User
Tambah user:
Sekolah
Verifikator
Atur role
🏫 Data Master Sekolah
Input & edit data sekolah
Import data (opsional, misal Excel)
🗂️ Manajemen Kab/Kota
Master wilayah
🔹 4. Fitur Monitoring & Reporting (Ini nilai PLUS 💯)
📊 Dashboard Statistik
Jumlah pengajuan
Jumlah disetujui
Jumlah revisi
Grafik per kab/kota
📥 Export Data
Export ke Excel / PDF
🔎 Pencarian & Filter
Berdasarkan:
Nama sekolah
Kab/Kota
Status
selanjutnya rancangan database 
1. Tabel tb_instansi

Tabel ini digunakan untuk menyimpan data master instansi, dalam hal ini sekolah sebagai pihak pengaju KSP.

Data yang disimpan meliputi:

Identitas sekolah seperti nama instansi, alamat, dan NPSN
Data pimpinan (kepala sekolah) beserta nomor kontak
Informasi waktu pembuatan, pembaruan, dan penghapusan data (soft delete)

Tabel ini berperan sebagai sumber utama data sekolah sehingga tidak perlu dilakukan input berulang setiap kali melakukan pengajuan.

🔹 2. Tabel tb_pengajuan

Tabel ini merupakan inti dari sistem yang digunakan untuk mencatat setiap pengajuan KSP.

Setiap pengajuan:

Terhubung dengan satu instansi/sekolah melalui id_sekolah
Memiliki informasi periode seperti semester dan tahun ajaran
Menjadi penghubung antara data sekolah dan dokumen yang diunggah

Dengan adanya tabel ini, sistem dapat mencatat riwayat pengajuan KSP dari setiap sekolah secara terstruktur.

🔹 3. Tabel tb_document

Tabel ini digunakan untuk menyimpan dokumen KSP yang diunggah oleh sekolah.

Setiap dokumen:

Terhubung dengan pengajuan tertentu melalui id_pengajuan
Memiliki jenis dokumen seperti cover, BAB I–V, dan lampiran
Menyimpan lokasi file (file path) di server

Pemisahan tabel dokumen ini bertujuan agar sistem lebih fleksibel dalam menangani banyak file dan memungkinkan pengelolaan dokumen per bagian KSP.

🔹 4. Tabel tb_verifikasi

Tabel ini digunakan untuk mencatat proses verifikasi yang dilakukan oleh verifikator dari dinas pendidikan.

Data yang disimpan meliputi:

Pengajuan yang diverifikasi (id_pengajuan)
Petugas yang melakukan verifikasi (user_id)
Tanggal verifikasi
Status hasil verifikasi (misalnya: disetujui atau perlu revisi)

Tabel ini memungkinkan sistem untuk melakukan tracking terhadap proses evaluasi KSP secara transparan dan terdokumentasi.

🔹 5. Tabel tb_user

Tabel ini menyimpan data pengguna sistem, baik dari pihak sekolah maupun verifikator.

Informasi yang disimpan meliputi:

Nama, email, dan password pengguna
Role atau peran pengguna (admin, sekolah, verifikator)
Keterkaitan dengan instansi melalui id_instansi

Dengan adanya tabel ini, sistem dapat menerapkan mekanisme autentikasi dan pembagian hak akses sesuai peran masing-masing pengguna.

🔗 Relasi Antar Tabel
tb_instansi → tb_pengajuan
Satu instansi dapat memiliki banyak pengajuan
tb_pengajuan → tb_document
Satu pengajuan dapat memiliki banyak dokumen
tb_pengajuan → tb_verifikasi
Satu pengajuan dapat diverifikasi oleh satu atau lebih verifikator
tb_user → tb_verifikasi
Satu user (verifikator) dapat melakukan banyak verifikasi
saya sudah mengintal project laravel 12 di folder ini database yang saya gunakan adalah mysql 
buatkan sprin untuk pengerjaan fitur" tersebut lalu tuangkan dalam file .MD secara terpisah
tuliskan dalam low level instruction, anggaplah sprin tersebut akan digunakan sebagai panduan oleh junior programmer.