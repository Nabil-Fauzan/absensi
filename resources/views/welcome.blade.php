<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AbsenKita - Sistem Absensi Karyawan Online</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;450;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 transition-colors duration-300 min-h-screen flex flex-col justify-between">
        
        <!-- Header / Navigation -->
        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-indigo-600 rounded-xl text-white shadow-md shadow-indigo-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-indigo-600 dark:text-indigo-400">AbsenKita</span>
            </div>

            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            Log In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 active:scale-95 text-gray-800 dark:text-white text-sm font-bold rounded-xl shadow-sm transition-all">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Main Content -->
        <main class="flex-grow flex items-center">
            <div class="max-w-7xl mx-auto px-6 py-12 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Hero Panel -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-full text-xs font-bold uppercase tracking-wider">
                        <span>✨ Absensi Online Karyawan</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">
                        Kelola Kehadiran Karyawan Secara <span class="text-indigo-600 dark:text-indigo-400">Real-Time & Praktis</span>
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto lg:mx-0">
                        Sistem absensi mandiri karyawan terintegrasi. Memudahkan pencatatan jam masuk, jam pulang, serta pengajuan cuti atau keterangan sakit/izin dalam satu sistem terpadu.
                    </p>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl hover:scale-[1.02] active:scale-95 transition-all">
                                Masuk ke Dashboard Absensi
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-xl hover:scale-[1.02] active:scale-95 transition-all">
                                Mulai Absensi Sekarang
                            </a>
                            <a href="#fitur" class="px-6 py-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 text-gray-800 dark:text-white font-bold rounded-2xl transition-all">
                                Lihat Fitur
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Visual Panel (Modern Geometric Cards) -->
                <div class="lg:col-span-5 relative flex items-center justify-center">
                    <!-- Decorative Gradients -->
                    <div class="absolute -inset-4 bg-indigo-600 rounded-full blur-3xl opacity-10 dark:opacity-20 animate-pulse"></div>
                    
                    <div class="relative w-full max-w-md bg-white dark:bg-gray-900 p-8 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-500">Live Preview Dashboard</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                        </div>
                        
                        <!-- Check In Card Mockup -->
                        <div class="p-6 bg-indigo-600 rounded-2xl text-white shadow-lg space-y-4">
                            <div>
                                <h4 class="text-xs font-semibold opacity-75 uppercase tracking-wider">Status Hari Ini</h4>
                                <p class="text-lg font-bold mt-1">Senin, 24 Juli 2026</p>
                            </div>
                            <div class="py-2 border-y border-white/10 flex justify-between items-center text-sm">
                                <span>Check-in Masuk:</span>
                                <span class="font-mono font-bold">08:00:15 WIB</span>
                            </div>
                            <button class="w-full py-3 bg-white text-indigo-800 font-bold rounded-xl shadow-md hover:bg-indigo-50 transition-all text-sm">
                                👉 Absen Masuk (Check-In)
                            </button>
                        </div>
                        
                        <!-- Recents List Mockup -->
                        <div class="space-y-3">
                            <h5 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Aktivitas Terakhir</h5>
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-950 rounded-xl text-xs border border-gray-100 dark:border-gray-900">
                                <div class="flex items-center gap-2">
                                    <span class="p-1.5 bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 rounded-lg">✔️</span>
                                    <div>
                                        <div class="font-bold">Nabil Fauzan</div>
                                        <div class="text-[10px] text-gray-400">Karyawan</div>
                                    </div>
                                </div>
                                <span class="font-mono text-gray-500">08:00:15 WIB</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- Features Section -->
        <section id="fitur" class="py-16 bg-white dark:bg-gray-900 border-y border-gray-100 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-2xl mx-auto mb-12 space-y-3">
                    <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">Alur Kerja yang Modern & Cepat</h2>
                    <p class="text-gray-600 dark:text-gray-400">Didukung dengan arsitektur modern untuk memastikan absensi tercatat dengan instan dan aman.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-900 hover:border-indigo-500/30 transition-all space-y-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 rounded-xl flex items-center justify-center text-2xl font-bold">
                            📲
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Absensi Mandiri</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            Karyawan dapat mencatat kehadiran masuk dan pulang secara real-time langsung melalui dashboard dengan satu ketukan tombol.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-900 hover:border-indigo-500/30 transition-all space-y-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 rounded-xl flex items-center justify-center text-2xl font-bold">
                            🩺
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengajuan Sakit & Izin</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            Tidak masuk kerja karena sakit atau kepentingan mendesak? Kirimkan surat keterangan dan alasan Anda secara online tanpa repot.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-950 rounded-2xl border border-gray-100 dark:border-gray-900 hover:border-indigo-500/30 transition-all space-y-4">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 rounded-xl flex items-center justify-center text-2xl font-bold">
                            📊
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rekapitulasi HRD</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            Admin dapat memantau tingkat kehadiran staf harian dan melihat riwayat absensi bulanan secara instan dalam rekapitulasi data.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="w-full border-t border-gray-100 dark:border-gray-900 py-8 bg-gray-50 dark:bg-gray-950">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500 dark:text-gray-500">
                <p>&copy; 2026 AbsenKita. Hak Cipta Dilindungi.</p>
                <div class="flex gap-4">
                    <a href="https://github.com/Nabil-Fauzan/absensi" target="_blank" class="hover:text-indigo-600 transition-colors">GitHub Repository</a>
                </div>
            </div>
        </footer>

    </body>
</html>
