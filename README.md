# ⏰ AbsenKita - Sistem Absensi Online & Geofencing Karyawan

AbsenKita adalah platform manajemen kehadiran mandiri karyawan berbasis web yang dilengkapi fitur **Geofencing GPS**, **Deteksi Keterlambatan**, **Dashboard Statistik Cerdas**, **Peta Interaktif Leaflet.js**, serta pelaporan otomatis.

---

## 🛠️ Tech Stack & Pustaka

* **Core Backend**: Laravel 13.x (PHP 8.5+)
* **Frontend**: Tailwind CSS, Blade Templates (Laravel Breeze Starter-kit)
* **Peta Digital**: Leaflet.js (Peta interaktif berbasis open-source OpenStreetMap)
* **Grafik Dashboard**: Chart.js (Stacked bar chart untuk tren kehadiran)
* **Pengujian**: Pest Testing Framework (PHPUnit wrapper)

---

## 🌟 Fitur Utama

### 1. 📍 Geofencing Pelacakan Lokasi (WFO vs WFH)
* Menghitung jarak GPS karyawan saat absen masuk/pulang dengan titik pusat koordinat kantor menggunakan rumus **Haversine**.
* Secara otomatis melabeli absensi dengan status:
  * **🏢 WFO (Di Kantor)** jika berada di dalam radius toleransi kantor (default: 100m).
  * **🏠 WFH (Luar Kantor)** jika berada di luar radius kantor.
* Tombol koordinat pada log admin memicu **Modal Peta Leaflet.js** terintegrasi secara dinamis untuk meninjau lokasi persis kehadiran staf tanpa membuka tab baru.

### ⏱️ 2. Deteksi Keterlambatan Otomatis (Late Detection)
* Sistem mencatat jam check-in karyawan dan membandingkannya dengan batas waktu absensi masuk kantor (default: `08:00:00 WIB`).
* Selisih menit keterlambatan dihitung otomatis dan ditampilkan dalam badge merah peringatan `⏱ Terlambat X m` baik di dashboard karyawan maupun panel admin.

### 📊 3. Panel Dashboard Statistik Admin (SaaS Style)
* **4 Kartu Ringkasan Harian**: Menyajikan data Hadir, Sakit/Izin, Terlambat, dan Belum Absen harian secara real-time.
* **Daftar Belum Absen**: Mengklik kartu "Belum Absen" memicu modal pop-up yang menyajikan nama & email staf yang belum check-in hari ini beserta tombol sekali klik untuk menyalin daftar nama tersebut.
* **Grafik Tren Mingguan**: Diagram batang bertumpuk interaktif menggunakan Chart.js untuk menganalisis grafik kehadiran 7 hari terakhir.
* **Filter Interaktif**: Kartu statistik atas dapat diklik untuk menyaring tabel rekapitulasi data secara instan.

### 📅 4. Sistem Filter & Ekspor CSV Berkinerja Tinggi
* Menyaring rekapitulasi kehadiran berdasarkan **Pencarian Nama Karyawan**, **Rentang Tanggal**, dan **Filter Status Cepat**.
* Ekspor data ke format `.csv` dengan mematuhi filter pencarian aktif yang memuat detail waktu check-in, check-out, keterangan sakit/izin, mode kerja (WFO/WFH), dan akumulasi menit terlambat.

### 🕒 5. Widget Jam Digital Berjalan (Real-time Live Clock)
* Menyematkan jam digital berjalan berdesain kaca (*glassmorphism*) yang berdetak setiap detiknya pada panel check-in karyawan untuk ketepatan waktu absensi.

### ⚠️ 6. Deteksi Keamanan Absen Tanpa GPS
* Jika karyawan mematikan GPS browser atau menolak izin akses lokasi, sistem mencatat absensi tetapi menandai kolom lokasi dengan indikator merah mencolok **`⚠️ Tanpa GPS`** untuk melacak kejujuran absensi.

---

## ⚙️ Konfigurasi Environment (`.env`)

Anda dapat menyesuaikan parameter geofencing dan aturan jam masuk kantor melalui file `.env`:

```env
# Koordinat Kantor Pusat (Default: Bandung/Cimahi)
OFFICE_LATITUDE=-6.873218738309585
OFFICE_LONGITUDE=107.5609385222725

# Radius Toleransi Geofencing (dalam Meter)
OFFICE_RADIUS_METERS=100

# Batas Waktu Jam Masuk Kantor
OFFICE_CHECK_IN_TIME=08:00:00

# Pengaturan Timezone Indonesia (WIB)
APP_TIMEZONE=Asia/Jakarta
```

---

## 🚀 Panduan Instalasi Lokal

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/Nabil-Fauzan/absensi.git
   cd absensi
   ```

2. **Instalasi Dependensi**:
   ```bash
   composer install
   npm install
   ```

3. **Duplikasi Konfigurasi Environment**:
   Salin berkas `.env.example` ke `.env` lalu sesuaikan kredensial koneksi database MySQL Anda.

4. **Generate App Key & Jalankan Migrasi / Seed**:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```
   *(Perintah seeder akan membuat akun admin utama secara otomatis)*.

5. **Kredensial Default Akun Admin**:
   * **Email**: `admin@gmail.com`
   * **Password**: `123456789`

6. **Kredensial Default Akun User**:
   * **Email**: `user@gmail.com`
   * **Password**: `123456789`

7. **Jalankan Aplikasi**:
   Jalankan server pengembangan Laravel dan bundler aset Tailwind:
   ```bash
   php artisan serve
   # Di tab terminal terpisah:
   npm run dev
   ```

---

## 🧪 Pengujian Sistem (Automated Tests)

Gunakan perintah di bawah ini untuk memverifikasi fungsionalitas logika sistem absensi, geofencing, cuti/sakit, dan email notification lewat Pest Test Suites:

```bash
php artisan test
```
*(Seluruh 25 unit pengujian harus menunjukkan status **PASSED**)*.
