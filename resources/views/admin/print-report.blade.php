<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap_Absensi_{{ date('Ymd_His') }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;750;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background-color: #ffffff;
            margin: 0;
            padding: 30px;
            font-size: 11px;
            line-height: 1.5;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .logo-section h1 {
            font-size: 20px;
            font-weight: 800;
            color: #059669;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .logo-section p {
            font-size: 10px;
            color: #6b7280;
            margin: 2px 0 0 0;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta h2 {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .report-meta p {
            font-size: 9px;
            color: #4b5563;
            margin: 4px 0 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .stat-card .label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            color: #6b7280;
        }

        .stat-card .value {
            font-size: 16px;
            font-weight: 800;
            color: #111827;
            margin-top: 4px;
        }

        .stat-card.suspicious {
            border-color: #fecaca;
            background-color: #fef2f2;
        }
        .stat-card.suspicious .value {
            color: #dc2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
        }

        .badge-present { background-color: #d1fae5; color: #065f46; }
        .badge-sick { background-color: #fef3c7; color: #92400e; }
        .badge-leave { background-color: #dbeafe; color: #1e40af; }
        
        .badge-wfo { background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
        .badge-wfh { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        .text-suspicious {
            color: #b91c1c;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .footer-signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-box .title {
            font-size: 10px;
            color: #4b5563;
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-weight: 750;
            color: #111827;
            border-bottom: 1px solid #4b5563;
            padding-bottom: 2px;
            display: inline-block;
            width: 150px;
        }

        .signature-box .role {
            font-size: 9px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Print Specific Styling */
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
        }

        .no-print-bar {
            background-color: #111827;
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .btn-print {
            background-color: #059669;
            color: #ffffff;
            border: none;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
        }
        .btn-print:hover {
            background-color: #047857;
        }
    </style>
</head>
<body>

    <!-- Print Control Bar (Visible on screen only) -->
    <div class="no-print-bar no-print">
        <span>Pratinjau Cetak Laporan Kehadiran Karyawan</span>
        <button onclick="window.print()" class="btn-print">
            <i class="bi bi-printer-fill"></i> Cetak Laporan / Simpan PDF
        </button>
    </div>

    <!-- Header -->
    <div class="header-container">
        <div class="logo-section">
            <h1>{{ config('app.name', 'AbsenKita') }}</h1>
            <p>Sistem Informasi & Manajemen Absensi Geofencing</p>
        </div>
        <div class="report-meta">
            <h2>LAPORAN REKAPITULASI ABSENSI</h2>
            <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</p>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Total Records</div>
            <div class="value">{{ $attendances->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Hadir (Present)</div>
            <div class="value">{{ $attendances->where('status', 'present')->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Sakit / Izin</div>
            <div class="value">{{ $attendances->whereIn('status', ['sick', 'leave'])->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Terlambat</div>
            <div class="value">{{ $attendances->where('status', 'present')->where('minutes_late', '>', 0)->count() }}</div>
        </div>
        <div class="stat-card suspicious">
            <div class="label">Mencurigakan</div>
            <div class="value">{{ $attendances->where('is_suspicious', true)->count() }}</div>
        </div>
    </div>

    <!-- Main Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Karyawan</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 8%;">Jam Masuk</th>
                <th style="width: 8%;">Jam Pulang</th>
                <th style="width: 10%;">Mode Kerja</th>
                <th style="width: 12%;">Keterlambatan</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $att)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-size: 11px; color: #111827;">{{ $att->user->name }}</strong>
                        <div style="font-size: 9px; color: #6b7280;">{{ $att->user->email }}</div>
                    </td>
                    <td style="text-align: center;" class="font-mono">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d-m-Y') }}</td>
                    <td style="text-align: center;">
                        @if($att->status === 'present')
                            <span class="badge badge-present">Hadir</span>
                        @elseif($att->status === 'sick')
                            <span class="badge badge-sick">Sakit</span>
                        @else
                            <span class="badge badge-leave">Izin</span>
                        @endif
                        
                        @if($att->is_suspicious)
                            <div class="text-suspicious" style="font-size: 7px; margin-top: 2px;">
                                <i class="bi bi-exclamation-triangle-fill"></i> SPUF
                            </div>
                        @endif
                    </td>
                    <td style="text-align: center;" class="font-mono">{{ $att->check_in ?? '-' }}</td>
                    <td style="text-align: center;" class="font-mono">{{ $att->check_out ?? '-' }}</td>
                    <td style="text-align: center;">
                        @if($att->status === 'present')
                            <span class="badge {{ $att->work_mode === 'wfo' ? 'badge-wfo' : 'badge-wfh' }}">
                                {{ $att->work_mode === 'wfo' ? 'WFO' : 'WFH' }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($att->status === 'present' && $att->minutes_late > 0)
                            <span style="color: #dc2626; font-weight: 600;">Terlambat {{ $att->minutes_late }} m</span>
                        @elseif($att->status === 'present')
                            <span style="color: #059669;">Tepat Waktu</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="font-style: italic; color: #4b5563;">
                        {{ $att->notes ?? '-' }}
                        @if($att->is_suspicious && $att->spoof_reason)
                            <div style="color: #dc2626; font-size: 8px; font-weight: bold; margin-top: 2px;">
                                Warning: {{ $att->spoof_reason }}
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #9ca3af; padding: 20px;">
                        Tidak ada log data kehadiran yang sesuai filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature layout -->
    <div class="footer-signatures">
        <div class="signature-box">
            <div class="title">Dibuat Oleh,</div>
            <div class="name">____________________</div>
            <div class="role">Staff Administrasi / HRD</div>
        </div>
        <div class="signature-box">
            <div class="title">Mengetahui & Menyetujui,</div>
            <div class="name">____________________</div>
            <div class="role">Kepala Cabang / Direktur</div>
        </div>
    </div>

</body>
</html>
