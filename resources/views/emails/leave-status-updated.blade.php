<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Pengajuan Absen Keterangan</title>
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
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header.approved {
            background-color: #059669; /* Emerald 600 */
        }
        .header.rejected {
            background-color: #dc2626; /* Red 600 */
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
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-badge.approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-badge.rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .notes-box {
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
            font-size: 14px;
        }
        .notes-box.approved {
            background-color: #f0fdf4; /* Emerald 50 */
            border-left: 4px solid #059669; /* Emerald 600 */
            color: #065f46;
        }
        .notes-box.rejected {
            background-color: #fef2f2; /* Red 50 */
            border-left: 4px solid #dc2626; /* Red 600 */
            color: #991b1b;
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
        @php
            $isApproved = $attendance->approval_status === 'approved';
            $headerClass = $isApproved ? 'approved' : 'rejected';
            $statusText = $isApproved ? 'Disetujui' : 'Ditolak';
            $typeLabel = $attendance->status === 'sick' ? 'Sakit' : 'Izin';
        @endphp
        
        <div class="header {{ $headerClass }}">
            <h1>Pengajuan {{ $typeLabel }} {{ $statusText }}</h1>
        </div>
        <div class="content">
            <p>Halo {{ $attendance->user->name }},</p>
            <p>Pengajuan absensi keterangan Anda telah diproses oleh Admin dengan hasil berikut:</p>
            
            <div class="detail-row">
                <div class="label">Tanggal Pengajuan</div>
                <div class="value">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}</div>
            </div>
            
            <div class="detail-row">
                <div class="label">Jenis Pengajuan</div>
                <div class="value">{{ $typeLabel }}</div>
            </div>
            
            <div class="detail-row">
                <div class="label">Alasan Anda</div>
                <div class="value">"{{ $attendance->notes }}"</div>
            </div>

            <div class="detail-row">
                <div class="label">Status Keputusan</div>
                <div>
                    <span class="status-badge {{ $headerClass }}">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
            
            @if(!$isApproved && $attendance->rejection_reason)
                <div class="notes-box rejected">
                    <strong>Catatan Admin (Alasan Penolakan):</strong><br>
                    "{{ $attendance->rejection_reason }}"
                </div>
            @elseif($isApproved)
                <div class="notes-box approved">
                    Terima kasih atas pemberitahuannya. Pengajuan Anda telah tercatat secara sah di sistem.
                </div>
            @endif
        </div>
        <div class="footer">
            Sistem Absensi {{ config('app.name', 'AbsenKita') }} &copy; {{ date('Y') }}
        </div>
    </div>
</body>
</html>
