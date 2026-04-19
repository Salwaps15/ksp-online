<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-600">Anda login sebagai perwakilan dari <strong>{{ auth()->user()->instansi->nama_instansi }}</strong>.</p>
            </div>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 truncate">Total Pengajuan Anda</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $data['total_pengajuan'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 truncate">Pengajuan Disetujui</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $data['disetujui'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-orange-500">
                    <div class="text-sm font-medium text-gray-500 truncate">Perlu Revisi</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $data['revisi'] }}</div>
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('sekolah.pengajuan.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-lg">
                    Lihat Daftar Pengajuan Saya
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
