<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AbsenKita') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/absenkita-logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;450;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="bg-gray-50 dark:bg-gray-950 antialiased min-h-screen flex flex-col md:flex-row">
        <!-- Left Banner Panel (Emerald Brand Accent) -->
        <div class="hidden md:flex md:w-1/2 bg-emerald-700 dark:bg-emerald-900 text-white flex-col justify-between p-12 relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(16,185,129,0.15),transparent)] pointer-events-none"></div>
            
            <div class="flex items-center gap-3 z-10">
                <div class="p-2.5 bg-white/10 rounded-xl text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-white">AbsenKita</span>
            </div>

            <div class="space-y-6 z-10 max-w-md">
                <h2 class="text-4xl font-extrabold leading-tight">Sistem Absensi Cerdas & Geofencing Presisi.</h2>
                <p class="text-emerald-100 text-sm leading-relaxed opacity-90">
                    Mencatat kehadiran masuk, pulang, serta pengajuan cuti & sakit dalam satu portal terpadu dengan deteksi jarak fisik kantor secara presisi.
                </p>
            </div>

            <div class="text-xs text-emerald-300 z-10 opacity-75">
                &copy; 2026 AbsenKita. Hak Cipta Dilindungi.
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 md:px-12 bg-gray-50 dark:bg-gray-950 transition-colors">
            <!-- Mobile Brand Header -->
            <div class="flex md:hidden items-center gap-2.5 mb-8">
                <div class="p-2.5 bg-emerald-600 rounded-xl text-white shadow-md shadow-emerald-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">AbsenKita</span>
            </div>

            <div class="w-full sm:max-w-md bg-white dark:bg-gray-900 p-8 md:p-10 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-gray-800 transition-colors relative pt-16">
                <!-- Back to Landing Page Button -->
                <a href="/" class="absolute top-6 left-8 md:left-10 flex items-center gap-1.5 text-xs font-semibold text-gray-400 dark:text-gray-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 transform group-hover:-translate-x-0.5 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Kembali ke Beranda
                </a>

                <div class="mb-6 text-center md:text-left">
                    <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                        {{ $title ?? 'Portal AbsenKita' }}
                    </h3>
                    <p class="text-xs text-gray-405 dark:text-gray-500 mt-1">
                        {{ $subtitle ?? 'Sistem pencatatan kehadiran mandiri' }}
                    </p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
