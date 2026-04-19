<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pengajuan;
use App\Models\Verifikasi;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    public function index()
    {
        // Tampilkan semua pengajuan yang statusnya bukan Draft
        $pengajuans = Pengajuan::with('instansi')
                               ->where('status', '!=', 'Draft')
                               ->latest()
                               ->get();
        return view('verifikator.pengajuan.index', compact('pengajuans'));
    }

    public function show($id)
    {
        // Tampilkan detail pengajuan beserta dokumen dan riwayat verifikasinya
        $pengajuan = Pengajuan::with(['instansi', 'documents', 'verifikasis.user'])->findOrFail($id);
        $jenis_dokumen = ['Cover', 'BAB I', 'BAB II', 'BAB III', 'BAB IV', 'BAB V', 'Lampiran'];
        
        return view('verifikator.pengajuan.show', compact('pengajuan', 'jenis_dokumen'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $request->validate([
            'status_hasil' => 'required|in:Disetujui,Revisi',
            'catatan' => 'required|string',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        // Gunakan Transaction agar update status & insert log selalu berpasangan
        DB::transaction(function () use ($request, $pengajuan) {
            // 1. Simpan riwayat ke tabel verifikasi
            Verifikasi::create([
                'id_pengajuan' => $pengajuan->id,
                'user_id' => auth()->id(),
                'tanggal_verifikasi' => now(),
                'status_hasil' => $request->status_hasil,
                'catatan' => $request->catatan,
            ]);

            // 2. Update status di tabel pengajuan
            // Jika hasil verifikasi adalah 'Revisi', status pengajuan jadi 'Direvisi'
            // Jika 'Disetujui', status pengajuan jadi 'Disetujui'
            $newStatus = ($request->status_hasil == 'Revisi') ? 'Direvisi' : 'Disetujui';
            $pengajuan->update(['status' => $newStatus]);
        });

        return redirect()->route('verifikator.pengajuan.index')->with('success', 'Verifikasi berhasil disimpan.');
    }
}
