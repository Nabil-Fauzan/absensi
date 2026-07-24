<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengajuan Absen Keterangan Baru</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #059669; /* Emerald 600 */
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .content {
            padding: 24px;
            color: #374151;
            line-height: 1.5;
        }
        .detail-row {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        .value {
            font-size: 15px;
            color: #111827;
            font-weight: 500;
            margin-top: 2px;
        }
        .notes-box {
            background-color: #f0fdf4; /* Emerald 50 */
            border-left: 4px solid #059669; /* Emerald 600 */
            padding: 16px;
            border-radius: 4px;
            margin-top: 16px;
            font-style: italic;
            color: #065f46;
        }
        .footer {
            background-color: #f9fafb;
            padding: 16px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pengajuan {{ $attendance->status === 'sick' ? 'Sakit' : 'Izin' }} Baru</h1>
        </div>
        <div class="content">
            <p>Halo Admin, terdapat pengajuan absensi keterangan baru dengan detail berikut:</p>
            
            <div class="detail-row">
                <div class="label">Nama Karyawan</div>
                <div class="value">{{ $attendance->user->name }}</div>
            </div>
            
            <div class="detail-row">
                <div class="label">Email</div>
                <div class="value">{{ $attendance->user->email }}</div>
            </div>
            
            <div class="detail-row">
                <div class="label">Tanggal</div>
                <div class="value">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="label">Tipe Pengajuan</div>
                <div class="value" style="color: #059669; font-weight: bold;">
                    {{ $attendance->status === 'sick' ? 'Sakit (Sick)' : 'Izin (Leave)' }}
                </div>
            </div>
            
            <div class="notes-box">
                <strong>Alasan / Keterangan:</strong><br>
                "{{ $attendance->notes }}"
            </div>
        </div>
        <div class="footer">
            Sistem Absensi AbsenKita &copy; 2026
        </div>
    </div>
</body>
</html>
