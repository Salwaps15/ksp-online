<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan KSP') }} - {{ $pengajuan->tahun_ajaran }} ({{ $pengajuan->semester }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Gagal mengunggah dokumen:</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Progress Bar -->
            <div class="mb-8">
                @php
                    $steps = [
                        ['status' => 'Draft', 'label' => 'Persiapan Dokumen', 'icon' => '1'],
                        ['status' => 'Diajukan', 'label' => 'Verifikasi Dinas', 'icon' => '2'],
                        ['status' => 'Disetujui', 'label' => 'Selesai', 'icon' => '3'],
                    ];
                    
                    $currentStatus = $pengajuan->status == 'Direvisi' ? 'Draft' : $pengajuan->status;
                    $reachedIdx = 0;
                    foreach($steps as $idx => $s) {
                        if($currentStatus == $s['status']) $reachedIdx = $idx;
                    }
                @endphp

                <div class="relative flex items-center justify-between w-full">
                    <div class="absolute left-0 top-1/2 w-full h-0.5 bg-gray-200 -translate-y-1/2"></div>
                    <div class="absolute left-0 top-1/2 h-0.5 bg-blue-500 -translate-y-1/2 transition-all duration-500" 
                         style="width: {{ $reachedIdx * 50 }}%"></div>
                    
                    @foreach($steps as $idx => $step)
                        <div class="relative flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 z-10 
                                {{ $reachedIdx >= $idx ? 'bg-blue-500 border-blue-500 text-white' : 'bg-white border-gray-300 text-gray-400' }}">
                                @if($reachedIdx > $idx)
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                @else
                                    {{ $step['icon'] }}
                                @endif
                            </div>
                            <span class="mt-2 text-xs font-bold {{ $reachedIdx >= $idx ? 'text-blue-600' : 'text-gray-500' }}">{{ $step['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Banner -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-500 uppercase font-bold">Status Saat Ini:</span>
                        <span class="ml-2 px-3 py-1 rounded-full text-sm font-bold 
                            {{ $pengajuan->status == 'Draft' ? 'bg-gray-100 text-gray-800' : 
                               ($pengajuan->status == 'Diajukan' ? 'bg-blue-100 text-blue-800' : 
                               ($pengajuan->status == 'Direvisi' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800')) }}">
                            {{ $pengajuan->status }}
                        </span>
                    </div>

                    @if(in_array($pengajuan->status, ['Draft', 'Direvisi']))
                        <form action="{{ route('sekolah.pengajuan.submit', $pengajuan->id) }}" method="POST">
                            @csrf
                            <x-primary-button onclick="return confirm('Apakah Anda yakin dokumen sudah lengkap dan siap diajukan?')">
                                {{ __('Kirim Pengajuan ke Dinas') }}
                            </x-primary-button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Upload Dokumen Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($jenis_dokumen as $jenis)
                    @php
                        $doc = $pengajuan->documents->where('jenis_dokumen', $jenis)->first();
                    @endphp
                    <div class="bg-white p-6 rounded-lg shadow-sm border {{ $doc ? 'border-green-200' : 'border-gray-200' }}">
                        <h3 class="font-bold text-gray-800 mb-2">{{ $jenis }}</h3>
                        
                        @if($doc)
                            <div class="mb-4 text-sm text-green-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Sudah Diunggah
                            </div>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline text-sm font-medium">Lihat Dokumen</a>
                        @else
                            <div class="mb-4 text-sm text-red-500 italic">Belum ada file</div>
                        @endif

                        @if(in_array($pengajuan->status, ['Draft', 'Direvisi']))
                            <form action="{{ route('sekolah.pengajuan.upload', $pengajuan->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-gray-100">
                                @csrf
                                <input type="hidden" name="jenis_dokumen" value="{{ $jenis }}">
                                <input type="file" name="file_dokumen" accept=".pdf,.doc,.docx" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                <p class="mt-1 text-[10px] text-gray-400">* PDF/Word, Maks 5MB</p>
                                <button type="submit" class="mt-2 w-full bg-gray-800 text-white text-xs py-2 rounded hover:bg-gray-700 transition">
                                    {{ $doc ? 'Ganti File' : 'Unggah File' }}
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- History Verifikasi / Catatan Revisi -->
            @if($pengajuan->verifikasis->count() > 0)
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-bold text-lg mb-4">Catatan Verifikasi</h3>
                        <div class="space-y-4">
                            @foreach($pengajuan->verifikasis as $v)
                                <div class="p-4 rounded-lg {{ $v->status_hasil == 'Revisi' ? 'bg-orange-50 border border-orange-200' : 'bg-green-50 border border-green-200' }}">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-bold {{ $v->status_hasil == 'Revisi' ? 'text-orange-700' : 'text-green-700' }}">
                                            Status: {{ $v->status_hasil }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $v->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 italic">"{{ $v->catatan }}"</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
