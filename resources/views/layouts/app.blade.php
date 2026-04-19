<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KSP ONLINE') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#f8fafc]">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: true }">
        
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="bg-blue-900 text-white transition-all duration-300 flex flex-col fixed inset-y-0 z-50">
            
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center px-4 border-b border-blue-800 shrink-0 overflow-hidden">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/Lambang_Jawa_Timur.png" alt="Logo" class="h-8 w-auto">
                <span x-show="sidebarOpen" x-cloak class="ml-3 font-bold text-lg whitespace-nowrap">KSP ONLINE</span>
            </div>

            <!-- Sidebar Nav -->
            <nav class="flex-1 py-4 px-3 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center p-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span x-show="sidebarOpen" x-cloak class="ml-3 font-medium">Dashboard</span>
                </a>

                <!-- Admin Menus -->
                @if(auth()->user()->role === 'admin')
                    <div x-show="sidebarOpen" x-cloak class="pt-4 pb-2 px-3 text-[10px] uppercase font-bold text-blue-400 tracking-widest">Master Data</div>
                    <a href="{{ route('admin.instansi.index') }}" 
                       class="flex items-center p-3 rounded-xl transition-colors {{ request()->routeIs('admin.instansi.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span x-show="sidebarOpen" x-cloak class="ml-3 font-medium">Instansi</span>
                    </a>
                    <a href="{{ route('admin.user.index') }}" 
                       class="flex items-center p-3 rounded-xl transition-colors {{ request()->routeIs('admin.user.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span x-show="sidebarOpen" x-cloak class="ml-3 font-medium">Pengguna</span>
                    </a>
                @endif

                <!-- Sekolah Menus -->
                @if(auth()->user()->role === 'sekolah')
                    <div x-show="sidebarOpen" x-cloak class="pt-4 pb-2 px-3 text-[10px] uppercase font-bold text-blue-400 tracking-widest">Layanan</div>
                    <a href="{{ route('sekolah.pengajuan.index') }}" 
                       class="flex items-center p-3 rounded-xl transition-colors {{ request()->routeIs('sekolah.pengajuan.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-show="sidebarOpen" x-cloak class="ml-3 font-medium">Pengajuan KSP</span>
                    </a>
                @endif

                <!-- Verifikator Menus -->
                @if(auth()->user()->role === 'verifikator')
                    <div x-show="sidebarOpen" x-cloak class="pt-4 pb-2 px-3 text-[10px] uppercase font-bold text-blue-400 tracking-widest">Verifikasi</div>
                    <a href="{{ route('verifikator.pengajuan.index') }}" 
                       class="flex items-center p-3 rounded-xl transition-colors {{ request()->routeIs('verifikator.pengajuan.*') ? 'bg-blue-800 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span x-show="sidebarOpen" x-cloak class="ml-3 font-medium">Verifikasi Masuk</span>
                    </a>
                @endif
            </nav>

            <!-- Sidebar Footer (Logout) -->
            <div class="p-4 border-t border-blue-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full p-3 rounded-xl text-red-300 hover:bg-red-500/10 transition-colors">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span x-show="sidebarOpen" x-cloak class="ml-3 font-medium">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div 
            :class="sidebarOpen ? 'ml-64' : 'ml-20'" 
            class="flex-1 flex flex-col transition-all duration-300 min-h-screen">
            
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    
                    @isset($header)
                        <div class="text-xl font-bold text-gray-800">
                            {{ $header }}
                        </div>
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-gray-800 leading-none">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">{{ Auth::user()->role }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border-2 border-white shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 lg:p-10">
                {{ $slot }}
            </main>

            <!-- Page Footer -->
            <footer class="p-6 text-center text-sm text-gray-500 border-t border-gray-100">
                © 2026 Dinas Pendidikan Provinsi Jawa Timur - Bidang Pembinaan PK-PLK
            </footer>
        </div>
    </div>
</body>
</html>
