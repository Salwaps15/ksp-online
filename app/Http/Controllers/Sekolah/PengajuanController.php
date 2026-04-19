<?php

namespace App\Http\Controllers\Sekolah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pengajuan;
use App\Models\Document;
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
        ], [
            'file_dokumen.mimes' => 'Format file harus PDF, DOC, atau DOCX.',
            'file_dokumen.max' => 'Ukuran file maksimal adalah 5 MB.',
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

    public function destroy($id)
    {
        $pengajuan = Pengajuan::with('documents')->findOrFail($id);

        // Pastikan hanya pengajuan sekolah sendiri
        if ($pengajuan->id_instansi !== auth()->user()->id_instansi) {
            abort(403);
        }

        // Hanya boleh hapus jika statusnya Draft
        if ($pengajuan->status !== 'Draft') {
            return back()->with('error', 'Hanya pengajuan berstatus Draft yang dapat dihapus.');
        }

        // Hapus file fisik dari storage
        foreach ($pengajuan->documents as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }

        $pengajuan->delete();

        return redirect()->route('sekolah.pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
