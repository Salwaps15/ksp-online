<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSP ONLINE - Dinas Pendidikan Provinsi Jawa Timur</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .btn-masuk {
            background: #3b82f6;
            transition: all 0.3s ease;
        }
        .btn-masuk:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body class="antialiased">
    <!-- Top Header -->
    <header class="hero-gradient text-white py-4 px-6 lg:px-12 flex items-center shadow-lg">
        <div class="flex items-center gap-4">
            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/Lambang_Jawa_Timur.png" alt="Logo Jatim" class="h-12 w-auto object-contain">
            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/a/a2/Logo_Dinas_Pendidikan_Provinsi_Jawa_Timur.png/200px-Logo_Dinas_Pendidikan_Provinsi_Jawa_Timur.png" alt="Logo Disdik" class="h-12 w-auto object-contain bg-white rounded-full p-1">
            <div class="ml-2">
                <h1 class="text-lg font-bold leading-tight">Dinas Pendidikan Provinsi Jawa Timur</h1>
                <p class="text-xs opacity-90">Bidang Pembinaan PK-PLK</p>
            </div>
        </div>
    </header>

    <main class="min-h-[calc(100vh-80px)] flex items-center justify-center px-6">
        <div class="max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            <!-- Left Content -->
            <div class="space-y-8">
                <div class="flex items-center gap-4">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b2/Lambang_Jawa_Timur.png" alt="Logo KSP" class="h-20 w-auto">
                    <div>
                        <h2 class="text-4xl font-extrabold text-blue-900 tracking-tight">KSP ONLINE</h2>
                        <p class="text-gray-500 font-medium text-lg">Pengajuan Kurikulum Satuan Pendidikan</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-3xl font-bold text-gray-800 leading-tight">Selamat Datang di KSP ONLINE</h3>
                    <p class="text-gray-600 text-lg leading-relaxed max-w-lg">
                        Platform pengajuan kurikulum satuan pendidikan dengan mudah, transparan, dan terintegrasi.
                    </p>
                </div>

                <div class="pt-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-masuk text-white text-xl font-bold py-4 px-16 rounded-xl inline-block text-center shadow-md">
                            Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk text-white text-xl font-bold py-4 px-24 rounded-xl inline-block text-center shadow-md">
                            Masuk
                        </a>
                    @endauth
                </div>

                <footer class="pt-8 text-gray-400 text-sm font-medium">
                    © 2026 Bidang PK-PLK Dinas Pendidikan Prov. Jatim
                </footer>
            </div>

            <!-- Right Illustration -->
            <div class="relative hidden lg:block">
                <div class="absolute -inset-4 bg-blue-100/50 rounded-full blur-3xl opacity-30"></div>
                <img src="{{ asset('images/ksp-hero.png') }}" alt="Illustration" class="relative w-full h-auto drop-shadow-2xl">
            </div>

        </div>
    </main>
</body>
</html>
