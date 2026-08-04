<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Premium Profile Header Card -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute -right-16 -top-16 w-32 h-32 bg-emerald-600/5 rounded-full pointer-events-none"></div>
                
                <div class="flex flex-col sm:flex-row items-center gap-5 z-10">
                    <!-- Avatar Initials -->
                    <div class="w-20 h-20 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-2xl shadow-lg shadow-emerald-500/20 border-4 border-white dark:border-gray-800">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="text-center sm:text-left space-y-1">
                        <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $user->email }}</p>
                        
                        <!-- Role Badge -->
                        <div class="pt-1">
                            @if($user->isAdmin())
                                <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/60 rounded-full text-[10px] font-bold">
                                    🛡️ Administrator
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-900/60 rounded-full text-[10px] font-bold">
                                    💼 Karyawan Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quick Attendance Statistics -->
                <div class="w-full md:w-auto flex justify-around md:justify-end items-center gap-6 border-t md:border-t-0 border-gray-100 dark:border-gray-700 pt-6 md:pt-0 z-10">
                    <div class="text-center">
                        <span class="block text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ \App\Models\Attendance::where('user_id', $user->id)->where('status', 'present')->whereMonth('date', now()->month)->count() }}
                        </span>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Hadir Bulan Ini</span>
                    </div>
                    <div class="w-px h-10 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="text-center">
                        <span class="block text-2xl font-black text-blue-600 dark:text-blue-400">
                            {{ \App\Models\Attendance::where('user_id', $user->id)->whereIn('status', ['sick', 'leave'])->whereMonth('date', now()->month)->count() }}
                        </span>
                        <span class="text-[10px] uppercase tracking-wider font-bold text-gray-400 dark:text-gray-500">Izin / Sakit</span>
                    </div>
                </div>
            </div>

            <!-- Two-Column Form Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Forms Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Profile Info Form Card -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Form Card -->
                    <div class="p-6 sm:p-8 bg-white dark:bg-gray-800 shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Right Side Information & Danger Zone -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Info Card -->
                    <div class="p-6 bg-white dark:bg-gray-800 shadow-xl rounded-3xl border border-gray-100 dark:border-gray-700 space-y-4">
                        <h4 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span><i class="bi bi-info-circle-fill text-emerald-600 mr-1"></i> Detail Informasi Akun</span>
                        </h4>
                        <div class="space-y-3 text-xs text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="font-semibold">Tanggal Daftar</span>
                                <span class="font-mono text-gray-500">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="font-semibold">Status Email</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="font-semibold">Geofencing Jarak</span>
                                <span class="text-gray-500 font-bold">Aktif ({{ env('OFFICE_RADIUS_METERS', 100) }} Meter)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone Card -->
                    <div class="p-6 bg-rose-50/50 dark:bg-rose-950/10 shadow-xl rounded-3xl border border-rose-200 dark:border-rose-900/60">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
