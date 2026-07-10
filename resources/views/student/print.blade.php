<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Karir - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 40px;
            background: #ffffff;
        }

        .header {
            border-bottom: 3px double #e5e7eb;
            padding-bottom: 24px;
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-title h1 {
            margin: 0;
            font-size: 2.2rem;
            color: #1e3a8a;
            letter-spacing: -0.5px;
        }

        .logo-title p {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .meta-info {
            text-align: right;
            font-size: 0.9rem;
            color: #4b5563;
        }

        .candidate-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 32px;
        }

        .candidate-card h2 {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 1.3rem;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 120px 1fr;
            row-gap: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #4b5563;
        }

        .info-value {
            color: #1e293b;
        }

        .section-title {
            font-size: 1.4rem;
            color: #1e3a8a;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            margin-top: 32px;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .skills-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .skills-list-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
        }

        .skills-list-box h3 {
            margin-top: 0;
            margin-bottom: 12px;
            font-size: 1.1rem;
            color: #334155;
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .career-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            background: #ffffff;
            position: relative;
        }

        .career-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .career-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .match-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: bold;
            font-size: 0.85rem;
            border: 1px solid #a7f3d0;
        }

        .career-reason {
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .improvement-list {
            padding-left: 20px;
            margin: 0;
        }

        .improvement-list li {
            margin-bottom: 12px;
            font-size: 0.95rem;
            color: #334155;
        }

        /* Floating action panel - hidden in print */
        .action-panel {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(17, 24, 39, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 24px;
            border-radius: 9999px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
            display: flex;
            gap: 16px;
            z-index: 10000;
        }

        .btn-panel {
            font-family: inherit;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 20px;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-panel-primary {
            background: #6366f1;
            color: white;
        }
        .btn-panel-primary:hover {
            background: #4f46e5;
        }

        .btn-panel-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-panel-secondary:hover {
            background: rgba(255,255,255,0.15);
        }

        @media print {
            .action-panel {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Panel for Browser UI -->
    <div class="action-panel">
        <button onclick="window.print()" class="btn-panel btn-panel-primary">
            Cetak Laporan (PDF)
        </button>
        <button onclick="window.close()" class="btn-panel btn-panel-secondary">
            Tutup Halaman
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="logo-title">
            <h1>AI-Carir</h1>
            <p>Sistem Guidance & Analisis Karir Mahasiswa berbasis AI</p>
        </div>
        <div class="meta-info">
            <strong>LAPORAN RESUME & KARIR</strong><br>
            Tanggal: {{ date('d F Y') }}<br>
            ID Laporan: AIC-{{ str_pad($latestAnalysis->id, 5, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <!-- Candidate Card -->
    <div class="candidate-card">
        <h2>Data Mahasiswa</h2>
        <div class="info-grid">
            <div class="info-label">Nama:</div>
            <div class="info-value">{{ $user->name }}</div>
            
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $user->email }}</div>

            <div class="info-label">Status CV:</div>
            <div class="info-value">Dianalisis otomatis pada {{ $latestAnalysis->created_at->format('d M Y, H:i') }} WIB</div>
        </div>
    </div>

    <!-- Skills Section -->
    <div class="section-title">Pemetaan Keahlian (Skills Mapping)</div>
    <div class="skills-container">
        <div class="skills-list-box">
            <h3>Keahlian Teknis (Hard Skills)</h3>
            <div class="tag-list">
                @if(!empty($latestAnalysis->skills['hard']))
                    @foreach($latestAnalysis->skills['hard'] as $skill)
                        <span class="tag">{{ $skill }}</span>
                    @endforeach
                @else
                    <span style="color: #6b7280; font-style: italic;">Tidak terdeteksi</span>
                @endif
            </div>
        </div>

        <div class="skills-list-box">
            <h3>Keahlian Interpersonal (Soft Skills)</h3>
            <div class="tag-list">
                @if(!empty($latestAnalysis->skills['soft']))
                    @foreach($latestAnalysis->skills['soft'] as $skill)
                        <span class="tag">{{ $skill }}</span>
                    @endforeach
                @else
                    <span style="color: #6b7280; font-style: italic;">Tidak terdeteksi</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Career Recommendations -->
    <div class="section-title">Rekomendasi Jalur Karir AI</div>
    <div>
        @if(!empty($latestAnalysis->careers))
            @foreach($latestAnalysis->careers as $career)
                <div class="career-item">
                    <div class="career-item-header">
                        <span class="career-title">{{ $career['title'] }}</span>
                        <span class="match-badge">{{ $career['match_rate'] }}% Cocok</span>
                    </div>
                    <p class="career-reason">{{ $career['reason'] }}</p>
                </div>
            @endforeach
        @else
            <p style="color: #6b7280; font-style: italic;">Tidak ada rekomendasi karir.</p>
        @endif
    </div>

    <!-- Career Improvement Roadmap -->
    <div class="section-title">Rencana Aksi & Rekomendasi Peningkatan Skill</div>
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
        <ul class="improvement-list">
            @if(!empty($latestAnalysis->improvements))
                @foreach($latestAnalysis->improvements as $imp)
                    <li>{{ $imp }}</li>
                @endforeach
            @else
                <li>Terus tingkatkan keahlian Anda dan perbarui CV Anda secara berkala.</li>
            @endif
        </ul>
    </div>

    <div style="margin-top: 60px; text-align: center; font-size: 0.8rem; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 16px;">
        Laporan ini dihasilkan secara otomatis oleh mesin AI-Carir menggunakan data kurikulum vitae yang diunggah oleh mahasiswa.
    </div>

</body>
</html>
