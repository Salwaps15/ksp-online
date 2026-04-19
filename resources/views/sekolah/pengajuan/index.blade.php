<x-app-layout>
    <x-slot name="header">
        {{ __('Daftar Pengajuan KSP') }}
    </x-slot>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Pengajuan Sekolah</h3>
            <a href="{{ route('sekolah.pengajuan.create') }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold transition-all shadow-md hover:shadow-blue-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Pengajuan Baru
            </a>
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
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Semester</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Dibuat Pada</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($pengajuans as $p)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ $p->tahun_ajaran }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ $p->semester }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full uppercase tracking-wider
                                    {{ $p->status == 'Draft' ? 'bg-gray-100 text-gray-700' : 
                                       ($p->status == 'Diajukan' ? 'bg-blue-100 text-blue-700' : 
                                       ($p->status == 'Direvisi' ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700')) }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-medium">
                                {{ $p->created_at->format('d M Y') }}
                                <span class="text-[10px] block opacity-60">{{ $p->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center gap-3">
                                <a href="{{ route('sekolah.pengajuan.show', $p->id) }}" 
                                   class="inline-flex items-center gap-2 {{ in_array($p->status, ['Draft', 'Direvisi']) ? 'bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-600 hover:text-white' }} px-4 py-2 rounded-xl transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    {{ in_array($p->status, ['Draft', 'Direvisi']) ? 'Lengkapi Dokumen' : 'Lihat Detail' }}
                                </a>

                                @if($p->status == 'Draft')
                                    <form action="{{ route('sekolah.pengajuan.destroy', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" 
                                                onclick="return confirm('Hapus draft pengajuan ini?')" 
                                                title="Hapus Draft">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pengajuans->isEmpty())
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <p class="text-gray-400 italic font-medium">Belum ada riwayat pengajuan Kurikulum.</p>
                <a href="{{ route('sekolah.pengajuan.create') }}" class="mt-4 inline-block text-blue-600 font-bold hover:underline">Klik di sini untuk membuat pengajuan pertama.</a>
            </div>
        @endif
    </div>
</x-app-layout>
