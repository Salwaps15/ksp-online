<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Pengajuan KSP') }} - {{ $pengajuan->instansi->nama_instansi }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Dokumen Sekolah -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Daftar Dokumen KSP</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($jenis_dokumen as $jenis)
                            @php
                                $doc = $pengajuan->documents->where('jenis_dokumen', $jenis)->first();
                            @endphp
                            <div class="p-4 border rounded-lg {{ $doc ? 'bg-white' : 'bg-gray-50' }}">
                                <h4 class="font-bold text-sm mb-2 text-gray-700">{{ $jenis }}</h4>
                                @if($doc)
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5 5m0 0l5-5m-5 5V3"></path></svg>
                                        Buka/Download File
                                    </a>
                                @else
                                    <span class="text-xs text-red-400 italic font-medium italic">Dokumen belum ada</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Form Verifikasi -->
            @if($pengajuan->status == 'Diajukan')
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 border-l-4 border-indigo-500">
                    <h3 class="text-lg font-bold mb-4 text-indigo-800">Proses Keputusan Verifikasi</h3>
                    <form action="{{ route('verifikator.pengajuan.proses', $pengajuan->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="status_hasil" :value="__('Hasil Verifikasi')" />
                            <select id="status_hasil" name="status_hasil" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Disetujui">Setujui (Selesai)</option>
                                <option value="Revisi">Minta Revisi (Kembali ke Sekolah)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="catatan" :value="__('Catatan / Alasan')" />
                            <textarea id="catatan" name="catatan" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" placeholder="Tuliskan catatan detail untuk sekolah jika perlu revisi..." required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Simpan Keputusan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg mb-8">
                <p class="text-blue-800 font-medium">
                    <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Status pengajuan ini adalah <strong>{{ $pengajuan->status }}</strong>. Verifikasi telah dilakukan.
                </p>
            </div>
            @endif

            <!-- Log Riwayat Verifikasi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Riwayat Keputusan</h3>
                    <div class="space-y-4">
                        @forelse($pengajuan->verifikasis as $v)
                            <div class="p-4 border rounded-lg {{ $v->status_hasil == 'Revisi' ? 'border-orange-200 bg-orange-50' : 'border-green-200 bg-green-50' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-bold {{ $v->status_hasil == 'Revisi' ? 'text-orange-800' : 'text-green-800' }}">
                                        {{ $v->status_hasil }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $v->created_at->format('d M Y H:i') }} - Oleh: {{ $v->user->name }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $v->catatan }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-4">Belum ada riwayat verifikasi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
