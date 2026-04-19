<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Instansi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.instansi.store') }}">
                        @csrf

                        <!-- Nama Instansi -->
                        <div>
                            <x-input-label for="nama_instansi" :value="__('Nama Instansi')" />
                            <x-text-input id="nama_instansi" class="block mt-1 w-full" type="text" name="nama_instansi" :value="old('nama_instansi')" required autofocus />
                            <x-input-error :messages="$errors->get('nama_instansi')" class="mt-2" />
                        </div>

                        <!-- NPSN -->
                        <div class="mt-4">
                            <x-input-label for="npsn" :value="__('NPSN')" />
                            <x-text-input id="npsn" class="block mt-1 w-full" type="text" name="npsn" :value="old('npsn')" required />
                            <x-input-error :messages="$errors->get('npsn')" class="mt-2" />
                        </div>

                        <!-- Alamat -->
                        <div class="mt-4">
                            <x-input-label for="alamat" :value="__('Alamat')" />
                            <textarea id="alamat" name="alamat" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('alamat') }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        <!-- Kepala Sekolah -->
                        <div class="mt-4">
                            <x-input-label for="kepala_sekolah" :value="__('Nama Kepala Sekolah')" />
                            <x-text-input id="kepala_sekolah" class="block mt-1 w-full" type="text" name="kepala_sekolah" :value="old('kepala_sekolah')" required />
                            <x-input-error :messages="$errors->get('kepala_sekolah')" class="mt-2" />
                        </div>

                        <!-- Kontak -->
                        <div class="mt-4">
                            <x-input-label for="kontak" :value="__('Nomor Kontak / WA')" />
                            <x-text-input id="kontak" class="block mt-1 w-full" type="text" name="kontak" :value="old('kontak')" required />
                            <x-input-error :messages="$errors->get('kontak')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.instansi.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Simpan Instansi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
