<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Presensi Bulanan - {{ $user->name }}</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        body {
            background-color: #f3f4f6;
            color: #1f2937;
            margin: 0;
            padding: 2rem;
            display: flex;
            justify-content: center;
        }
        .slip-card {
            background: white;
            width: 100%;
            max-width: 850px;
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid #e5e7eb;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-b: 2px solid #e5e7eb;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .brand-title {
            color: #059669;
            font-weight: 800;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0 0 0.25rem 0;
        }
        .brand-subtitle {
            font-size: 0.75rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
            font-weight: 700;
        }
        .employee-profile {
            font-size: 0.8rem;
            color: #4b5563;
        }
        .employee-profile table {
            border-collapse: collapse;
        }
        .employee-profile td {
            padding: 0.25rem 0.5rem;
        }
        .employee-profile td.label {
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            font-size: 0.7rem;
            padding-left: 0;
        }
        .metrics-grid {
            display: grid;
            grid-cols: 1;
            grid-template-columns: repeat(6, 1fr);
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        .metric-card {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 16px;
            padding: 0.75rem;
            text-align: center;
        }
        .metric-card.present { border-color: #d1fae5; background-color: #f0fdf4; }
        .metric-card.sick { border-color: #fef3c7; background-color: #fffbeb; }
        .metric-card.leave { border-color: #dbeafe; background-color: #eff6ff; }
        .metric-card.birthday { border-color: #fce7f3; background-color: #fdf2f8; }
        .metric-card.alfa { border-color: #fee2e2; background-color: #fef2f2; }
        
        .metric-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }
        .metric-value {
            font-size: 1.25rem;
            font-weight: 800;
        }
        .metric-card.present .metric-value { color: #059669; }
        .metric-card.sick .metric-value { color: #d97706; }
        .metric-card.leave .metric-value { color: #2563eb; }
        .metric-card.birthday .metric-value { color: #db2777; }
        .metric-card.alfa .metric-value { color: #dc2626; }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            margin-bottom: 2.5rem;
        }
        .attendance-table th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 0.05em;
            padding: 0.75rem;
            border-bottom: 2px solid #e5e7eb;
            text-align: left;
        }
        .attendance-table td {
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .attendance-table tr:hover {
            background-color: #f9fafb/50;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 8px;
            text-transform: uppercase;
        }
        .badge.hadir { background-color: #d1fae5; color: #065f46; }
        .badge.sakit { background-color: #fef3c7; color: #92400e; }
        .badge.cuti { background-color: #dbeafe; color: #1e40af; }
        .badge.cuti-ultah { background-color: #fce7f3; color: #9d174d; }
        .badge.weekend { background-color: #f3f4f6; color: #4b5563; }
        .badge.libur { background-color: #e0f2fe; color: #0369a1; }
        .badge.alfa { background-color: #fee2e2; color: #991b1b; }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding: 0 1.5rem;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 5rem;
        }
        .signature-line {
            border-bottom: 1px solid #1f2937;
            margin-bottom: 0.25rem;
        }
        .signature-name {
            font-size: 0.8rem;
            font-weight: 700;
            color: #1f2937;
        }
        .signature-date {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        .print-btn-container {
            max-width: 850px;
            width: 100%;
            margin: 0 auto 1.5rem auto;
            display: flex;
            justify-content: flex-end;
        }
        .btn-print {
            background-color: #059669;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
            transition: all 150ms;
        }
        .btn-print:hover {
            background-color: #047857;
            transform: translateY(-1px);
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
                color: black;
            }
            .slip-card {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .print-btn-container {
                display: none;
            }
            .attendance-table th {
                border-bottom: 2px solid black !important;
                background-color: #f9fafb !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .attendance-table td {
                border-bottom: 1px solid #e5e7eb !important;
            }
            .badge {
                border: 0.5px solid #d1d5db !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .metric-card {
                border: 1px solid #d1d5db !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div style="display: flex; flex-direction: column; width: 100%; align-items: center;">
        
        <!-- Print Button Header (Hidden in Print) -->
        <div class="print-btn-container">
            <button onclick="window.print()" class="btn-print">
                <i class="bi bi-printer-fill"></i> Cetak Slip Gaji / Presensi
            </button>
        </div>

        <!-- Main Slip Container -->
        <div class="slip-card">
            
            <!-- Header -->
            <div class="header-section">
                <div>
                    <h1 class="brand-title">
                        <i class="bi bi-alarm"></i> AbsenKita
                    </h1>
                    <p class="brand-subtitle">Kartu Presensi Bulanan Karyawan</p>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 1.15rem; font-weight: 800; color: #1f2937;">{{ $monthName }}</div>
                    <div style="font-size: 0.7rem; color: #9ca3af; font-weight: bold; text-transform: uppercase; margin-top: 0.25rem;">Presensi Resmi</div>
                </div>
            </div>

            <!-- Employee Info Grid -->
            <div class="employee-profile" style="margin-bottom: 2rem;">
                <table style="width: 100%;">
                    <tr>
                        <td class="label" style="width: 15%;">Nama Karyawan</td>
                        <td style="width: 35%; font-weight: 700; color: #111827;">: {{ $user->name }}</td>
                        <td class="label" style="width: 15%;">Cabang Kantor</td>
                        <td style="width: 35%; font-weight: 700; color: #111827;">: {{ $user->branch->name ?? 'Kantor Pusat' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat Email</td>
                        <td class="font-mono">: {{ $user->email }}</td>
                        <td class="label">Shift Kerja</td>
                        <td style="font-weight: 700; color: #111827;">: {{ $user->shift->name ?? 'Default (08:00)' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Lahir</td>
                        <td style="font-weight: 700; color: #111827;">: {{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->translatedFormat('d F Y') : '-' }}</td>
                        <td class="label">Dokumen ID</td>
                        <td class="font-mono">: SLIP/{{ $user->id }}/{{ \Carbon\Carbon::now()->format('Ymd') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Summary Metrics Cards -->
            <div class="metrics-grid">
                <div class="metric-card present">
                    <div class="metric-title">Hadir</div>
                    <div class="metric-value">{{ $stats['hadir'] }}</div>
                </div>
                <div class="metric-card sick">
                    <div class="metric-title">Sakit</div>
                    <div class="metric-value">{{ $stats['sakit'] }}</div>
                </div>
                <div class="metric-card leave">
                    <div class="metric-title">Cuti Tahunan</div>
                    <div class="metric-value">{{ $stats['izin_tahunan'] }}</div>
                </div>
                <div class="metric-card birthday">
                    <div class="metric-title">Cuti Ultah</div>
                    <div class="metric-value">{{ $stats['izin_ultah'] }}</div>
                </div>
                <div class="metric-card alfa">
                    <div class="metric-title">Mangkir (Alfa)</div>
                    <div class="metric-value">{{ $stats['alfa'] }}</div>
                </div>
                <div class="metric-card" style="border-color: #e5e7eb;">
                    <div class="metric-title">Terlambat</div>
                    <div class="metric-value" style="color: #6b7280;">{{ $stats['terlambat'] }}<span style="font-size: 0.65rem; font-weight: normal;">m</span></div>
                </div>
            </div>

            <!-- Daily Attendance Grid Table -->
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 25%;">Hari / Tanggal</th>
                        <th style="width: 20%;">Status</th>
                        <th style="width: 10%; text-align: center;">Masuk</th>
                        <th style="width: 10%; text-align: center;">Pulang</th>
                        <th style="width: 10%; text-align: center;">Mode</th>
                        <th style="width: 10%; text-align: center;">Terlambat</th>
                        <th style="width: 10%;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daysGrid as $index => $day)
                        @php
                            $badgeClass = match(true) {
                                str_contains($day['status'], 'Hadir') => 'hadir',
                                str_contains($day['status'], 'Sakit') => 'sakit',
                                str_contains($day['status'], 'Cuti Ulang') => 'cuti-ultah',
                                str_contains($day['status'], 'Cuti') => 'cuti',
                                str_contains($day['status'], 'Akhir Pekan') => 'weekend',
                                str_contains($day['status'], 'Hari Libur') => 'libur',
                                str_contains($day['status'], 'Alfa') => 'alfa',
                                default => ''
                            };
                        @endphp
                        <tr>
                            <td class="text-center font-mono" style="color: #9ca3af; text-align: center;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600;">
                                {{ $day['day_name'] }}, {{ $day['date']->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                @if($badgeClass)
                                    <span class="badge {{ $badgeClass }}">{{ $day['status'] }}</span>
                                @else
                                    {{ $day['status'] }}
                                @endif
                            </td>
                            <td class="text-center font-mono" style="text-align: center;">{{ $day['check_in'] }}</td>
                            <td class="text-center font-mono" style="text-align: center;">{{ $day['check_out'] }}</td>
                            <td class="text-center font-semibold" style="text-align: center; color: #4b5563;">{{ $day['work_mode'] }}</td>
                            <td class="text-center font-mono" style="text-align: center; color: {{ $day['lateness'] > 0 ? '#dc2626' : '#9ca3af' }};">
                                {{ $day['lateness'] > 0 ? $day['lateness'] . ' m' : '-' }}
                            </td>
                            <td style="color: #9ca3af; font-size: 0.65rem;">{{ $day['notes'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Signature block -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title">Karyawan Bersangkutan</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $user->name }}</div>
                    <div class="signature-date">Tanggal: ___________________</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title">HRD & General Manager</div>
                    <div class="signature-line"></div>
                    <div class="signature-name">Administrator</div>
                    <div class="signature-date">Tanggal: ___________________</div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
