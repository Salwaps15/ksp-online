<x-app-layout>
    <x-slot name="header">
        {{ __('Daftar Pengajuan Masuk') }}
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Semua Pengajuan Sekolah</h3>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Memantau Pengajuan Baru</span>
            </div>
        </div>

        @if (session('success'))
            <div class="m-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-white">
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Sekolah</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Masuk</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($pengajuans as $p)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $p->instansi->nama_instansi }}</div>
                                <div class="text-[10px] text-gray-400 font-medium tracking-tight">NPSN: {{ $p->instansi->npsn }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $p->tahun_ajaran }}
                                <span class="text-[10px] text-gray-400 block">{{ $p->semester }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full uppercase tracking-wider
                                    {{ $p->status == 'Diajukan' ? 'bg-blue-100 text-blue-700' : 
                                       ($p->status == 'Direvisi' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-bold text-gray-700">{{ $p->updated_at->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400 font-medium tracking-tighter">{{ $p->updated_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('verifikator.pengajuan.show', $p->id) }}" 
                                   class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-xl font-bold transition-all transform hover:scale-105 active:scale-95 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Review Dokumen
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pengajuans->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p class="text-gray-400 italic font-medium">Belum ada dokumen yang perlu diverifikasi saat ini.</p>
            </div>
        @endif
    </div>
</x-app-layout>
