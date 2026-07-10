@extends('layouts.app')

@section('title', 'Dashboard Karir Mahasiswa - AI-Carir')

@section('content')
<div class="grid gap-8" style="grid-template-columns: 1fr;">
    
    <!-- Profile & Upload Section -->
    <div class="card flex flex-col md-row items-center justify-between gap-6" style="flex-direction: row; flex-wrap: wrap;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 4px;">Halo, {{ Auth::user()->name }}!</h2>
            <p style="color: var(--text-secondary); font-size: 0.95rem;">
                @if($latestAnalysis)
                    Analisis CV terakhir Anda dilakukan pada: <strong style="color: white;">{{ $latestAnalysis->created_at->format('d M Y, H:i') }} WIB</strong>
                @else
                    Unggah CV Anda untuk menganalisis keahlian dan mendapatkan rekomendasi karir AI.
                @endif
            </p>
        </div>
        
        <div style="flex-grow: 1; max-width: 450px; width: 100%;">
            <form action="{{ route('student.upload') }}" method="POST" enctype="multipart/form-data" id="cvForm">
                @csrf
                <div style="display: flex; gap: 12px; align-items: center;">
                    <div style="position: relative; flex-grow: 1;">
                        <input type="file" name="cv_file" id="cv_file" accept=".pdf,.png,.jpg,.jpeg,.txt" style="position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer;" onchange="updateFileName(this)">
                        <div class="form-control flex items-center justify-between" style="cursor: pointer; padding: 10px 16px; min-height: 45px;">
                            <span id="file-label" style="color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">
                                <i data-feather="file-text" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;"></i>
                                Pilih file PDF / Gambar / TXT...
                            </span>
                            <span style="font-size: 0.8rem; background: rgba(0,0,0,0.04); padding: 4px 8px; border-radius: 6px;">Cari</span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 45px; white-space: nowrap;" id="btnSubmit">
                        <i data-feather="cpu" style="width: 16px; height: 16px;"></i>
                        Analisis CV
                    </button>
                </div>
                @error('cv_file')
                    <span style="color: var(--danger); font-size: 0.85rem; margin-top: 6px; display: block;">{{ $message }}</span>
                @enderror
            </form>
        </div>
    </div>

    @if(!$latestAnalysis)
        <!-- Empty State -->
        <div class="card" style="text-align: center; padding: 60px 40px; border-style: dashed; border-width: 2px;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i data-feather="upload-cloud" style="width: 40px; height: 40px; color: var(--primary);"></i>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 12px;">CV Belum Diunggah</h3>
            <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 24px; font-size: 0.95rem; line-height: 1.6;">
                Unggah curriculum vitae (CV) Anda dalam format PDF, PNG, JPG, atau TXT. AI kami mendukung analisis desain grafis (Canva) dan scan gambar, secara otomatis mendeteksi keahlian dan merekomendasikan karir terbaik.
            </p>
            <label for="cv_file" class="btn btn-primary" style="cursor: pointer;">
                <i data-feather="file" style="width: 18px; height: 18px;"></i>
                Pilih File CV Sekarang
            </label>
        </div>
    @else
        <!-- Dashboard Tabs -->
        <div>
            <div class="flex justify-between items-center flex-wrap gap-4" style="margin-bottom: 16px;">
                <div class="tabs-header" style="margin-bottom: 0; border-bottom: none;">
                    <button class="tab-btn active" onclick="switchTab(event, 'skills-tab')">
                        <i data-feather="award" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;"></i>
                        Analisis Keahlian
                    </button>
                    <button class="tab-btn" onclick="switchTab(event, 'careers-tab')">
                        <i data-feather="trending-up" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;"></i>
                        Rekomendasi Karir AI
                    </button>
                    <button class="tab-btn" onclick="switchTab(event, 'internships-tab')">
                        <i data-feather="briefcase" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;"></i>
                        Rekomendasi Magang
                    </button>
                </div>
                
                <a href="{{ route('student.print') }}" target="_blank" class="btn btn-secondary">
                    <i data-feather="printer" style="width: 16px; height: 16px;"></i>
                    Cetak Laporan PDF
                </a>
            </div>

            <!-- Tab Content 1: Skills -->
            <div id="skills-tab" class="tab-content active">
                <div class="grid grid-cols-12 gap-6">
                    <!-- Hard Skills -->
                    <div class="card col-span-4 flex flex-col gap-6" style="grid-column: span 6;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <span style="display:inline-block; width: 10px; height: 10px; border-radius:50%; background: var(--primary);"></span>
                            Hard Skills (Keahlian Teknis)
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: -12px;">Keahlian teknis dan alat yang berhasil terdeteksi dari CV Anda.</p>
                        
                        <div class="skill-tag-group">
                            @if(!empty($latestAnalysis->skills['hard']))
                                @foreach($latestAnalysis->skills['hard'] as $skill)
                                    <span class="skill-tag skill-tag-hard">{{ $skill }}</span>
                                @endforeach
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Tidak ada hard skill yang terdeteksi.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Soft Skills -->
                    <div class="card col-span-4 flex flex-col gap-6" style="grid-column: span 6;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <span style="display:inline-block; width: 10px; height: 10px; border-radius:50%; background: var(--secondary);"></span>
                            Soft Skills (Keahlian Interpersonal)
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: -12px;">Karakter dan kemampuan interpersonal yang dinilai dari CV Anda.</p>
                        
                        <div class="skill-tag-group">
                            @if(!empty($latestAnalysis->skills['soft']))
                                @foreach($latestAnalysis->skills['soft'] as $skill)
                                    <span class="skill-tag skill-tag-soft">{{ $skill }}</span>
                                @endforeach
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Tidak ada soft skill yang terdeteksi.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Raw Resume text -->
                    <div class="card col-span-12" style="grid-column: span 12;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <i data-feather="file-text" style="width: 18px; height: 18px; color: var(--primary);"></i>
                            Teks CV yang Diekstrak
                        </h3>
                        <div style="background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 8px; padding: 16px; max-height: 250px; overflow-y: auto; font-size: 0.85rem; line-height: 1.6; color: var(--text-secondary); font-family: monospace; white-space: pre-wrap;">{{ $latestAnalysis->raw_text }}</div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 2: Career Recommendations -->
            <div id="careers-tab" class="tab-content">
                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 1fr;">
                    
                    <!-- Career Cards -->
                    <div class="grid grid-cols-3 gap-6" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
                        @if(!empty($latestAnalysis->careers))
                            @foreach($latestAnalysis->careers as $index => $career)
                                <div class="card flex flex-col justify-between" style="position: relative; overflow: hidden; min-height: 250px;">
                                    <div style="position: absolute; top: 0; right: 0; padding: 12px 18px; background: rgba(99,102,241,0.1); border-bottom-left-radius: 16px; font-weight: 800; font-size: 1.1rem; color: var(--primary);">
                                        {{ $career['match_rate'] }}% <span style="font-size: 0.8rem; font-weight: 500;">Match</span>
                                    </div>
                                    
                                    <div style="margin-top: 10px;">
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                            <span style="font-size: 1.2rem; font-weight: 700; color: white;">#{{ $index + 1 }} {{ $career['title'] }}</span>
                                        </div>
                                        <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6; margin-bottom: 16px;">
                                            {{ $career['reason'] }}
                                        </p>
                                    </div>
                                    
                                    <div class="progress-bar-container" style="margin-bottom: 8px;">
                                        <div class="progress-bar-fill" style="width: {{ $career['match_rate'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="card col-span-3" style="text-align: center; color: var(--text-secondary); grid-column: span 3;">
                                Tidak ada rekomendasi karir.
                            </div>
                        @endif
                    </div>

                    <!-- Improvement Roadmap Card -->
                    <div class="card">
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                            <i data-feather="check-square" style="width: 18px; height: 18px; color: var(--accent);"></i>
                            Rencana Aksi Peningkatan Karir
                        </h3>
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @if(!empty($latestAnalysis->improvements))
                                @foreach($latestAnalysis->improvements as $imp)
                                    <div style="display: flex; gap: 16px; align-items: flex-start; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
                                        <div style="background: rgba(20, 184, 166, 0.1); color: var(--accent); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: bold;">
                                            <i data-feather="arrow-up-right" style="width: 16px; height: 16px;"></i>
                                        </div>
                                        <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-primary); margin-top: 4px;">{{ $imp }}</p>
                                    </div>
                                @endforeach
                            @else
                                <p style="color: var(--text-secondary); font-style: italic;">Tidak ada saran peningkatan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 3: Internship Recommendations -->
            <div id="internships-tab" class="tab-content">
                <div class="grid grid-cols-1 gap-6" style="grid-template-columns: 1fr;">
                    
                    @if($internships->isNotEmpty())
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            @foreach($internships as $internship)
                                <div class="card flex flex-col md-row justify-between gap-6" style="flex-direction: row; flex-wrap: wrap; align-items: center;">
                                    <div style="flex-grow: 1; min-width: 280px; max-width: 70%;">
                                        <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 6px;">
                                            <h4 style="font-size: 1.15rem; font-weight: 700; color: white;">{{ $internship->title }}</h4>
                                            <span style="font-size: 0.8rem; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: var(--success); padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                                                {{ $internship->company }}
                                            </span>
                                        </div>
                                        <div style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 12px; display: flex; align-items: center; gap: 4px;">
                                            <i data-feather="map-pin" style="width: 14px; height: 14px;"></i> {{ $internship->location }}
                                        </div>
                                        <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.5; margin-bottom: 16px;">
                                            {{ $internship->description }}
                                        </p>
                                        
                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-secondary);">Keahlian yang Dibutuhkan:</span>
                                            <div class="skill-tag-group">
                                                @foreach($internship->required_skills ?? [] as $reqSkill)
                                                    @php
                                                        $matched = in_array($reqSkill, $internship->matched_skills);
                                                    @endphp
                                                    <span class="skill-tag {{ $matched ? 'skill-tag-matched' : '' }}">
                                                        @if($matched)
                                                            <i data-feather="check" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 2px;"></i>
                                                        @endif
                                                        {{ $reqSkill }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div style="flex-shrink: 0; min-width: 180px; text-align: right; display: flex; flex-direction: column; gap: 12px;">
                                        <div>
                                            <div style="font-size: 1.8rem; font-weight: 800; color: {{ $internship->match_score >= 80 ? 'var(--success)' : ($internship->match_score >= 50 ? 'var(--warning)' : 'var(--danger)') }}">
                                                {{ $internship->match_score }}%
                                            </div>
                                            <div style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600;">Match Score</div>
                                        </div>
                                        
                                        <div class="progress-bar-container" style="width: 100%; margin-top: 0;">
                                            <div class="progress-bar-fill" style="width: {{ $internship->match_score }}%; background: {{ $internship->match_score >= 80 ? 'var(--success)' : 'rgba(99,102,241,1)' }}"></div>
                                        </div>

                                        <button class="btn btn-primary" onclick="alert('Pendaftaran Berhasil! Lamaran Anda ke {{ $internship->company }} untuk posisi {{ $internship->title }} telah dikirim. Terima kasih.')" style="width: 100%; margin-top: 8px;">
                                            Lamar Sekarang
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            Tidak ada lowongan magang yang tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function updateFileName(input) {
        const fileLabel = document.getElementById('file-label');
        if (input.files && input.files.length > 0) {
            fileLabel.innerHTML = `<i data-feather="file" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px; color: var(--primary);"></i> ${input.files[0].name}`;
            feather.replace();
        }
    }

    function switchTab(evt, tabId) {
        // Get all elements with class="tab-content" and hide them
        const tabContents = document.getElementsByClassName("tab-content");
        for (let i = 0; i < tabContents.length; i++) {
            tabContents[i].classList.remove("active");
        }

        // Get all elements with class="tab-btn" and remove the class "active"
        const tabBtns = document.getElementsByClassName("tab-btn");
        for (let i = 0; i < tabBtns.length; i++) {
            tabBtns[i].classList.remove("active");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabId).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
    
    // Simple visual feedback on CV form submit
    const cvForm = document.getElementById('cvForm');
    if (cvForm) {
        cvForm.addEventListener('submit', function() {
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<i data-feather="loader" class="spin" style="width: 16px; height: 16px; animation: spin 1s linear infinite;"></i> Menganalisis...`;
            feather.replace();
        });
    }
</script>
<style>
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
