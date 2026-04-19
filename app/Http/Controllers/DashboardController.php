<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Pengajuan;
use App\Models\Instansi;

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

        // Ambil 5 pengajuan terbaru untuk ditampilkan di tabel dashboard
        $pengajuan_terbaru = Pengajuan::with('instansi')->latest()->take(5)->get();

        return view('dashboard.admin', compact('data', 'pengajuan_terbaru'));
    }
}
