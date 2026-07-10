@extends('layouts.app')

@section('title', 'Dashboard Admin - AI-Carir')

@section('content')
<div class="grid gap-8" style="grid-template-columns: 1fr;">
    
    <!-- Header Title -->
    <div>
        <h1 class="gradient-text" style="font-size: 2rem; font-weight: 800; margin-bottom: 4px;">Dashboard Administrasi</h1>
        <p style="color: var(--text-secondary); font-size: 0.95rem;">Pantau statistik mahasiswa, hasil analisis CV, dan kelola lowongan magang sistem.</p>
    </div>

    <!-- Stat Widgets -->
    <div class="grid grid-cols-3 gap-6">
        <!-- Students Stat -->
        <div class="card stat-widget">
            <div class="stat-icon">
                <i data-feather="users" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $studentsCount }}</div>
                <div class="stat-label">Total Mahasiswa</div>
            </div>
        </div>

        <!-- CV Analyses Stat -->
        <div class="card stat-widget">
            <div class="stat-icon" style="background: rgba(168, 85, 247, 0.1); color: var(--secondary);">
                <i data-feather="file-text" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $cvAnalysesCount }}</div>
                <div class="stat-label">CV Dianalisis</div>
            </div>
        </div>

        <!-- Internships Stat -->
        <div class="card stat-widget">
            <div class="stat-icon" style="background: rgba(20, 184, 166, 0.1); color: var(--accent);">
                <i data-feather="briefcase" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <div class="stat-value">{{ $internshipsCount }}</div>
                <div class="stat-label">Lowongan Magang</div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="grid grid-cols-2 gap-6" style="grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));">
        
        <!-- Skill Distribution Chart/Metrics -->
        <div class="card">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i data-feather="bar-chart-2" style="width: 18px; height: 18px; color: var(--primary);"></i>
                Sebaran Skill Terbanyak (Top 5)
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @if(count($skillLabels) > 0)
                    @foreach($skillLabels as $index => $label)
                        @php
                            $value = $skillValues[$index];
                            $maxVal = max($skillValues) ?: 1;
                            $percentage = ($value / $maxVal) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between" style="font-size: 0.9rem; font-weight: 600; margin-bottom: 4px;">
                                <span>{{ $label }}</span>
                                <span style="color: var(--primary);">{{ $value }} Mahasiswa</span>
                            </div>
                            <div class="progress-bar-container" style="height: 6px; margin-top: 0;">
                                <div class="progress-bar-fill" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="color: var(--text-secondary); text-align: center; padding: 20px; font-style: italic;">Belum ada data skill yang dianalisis.</p>
                @endif
            </div>
        </div>

        <!-- Top Career Recommendations -->
        <div class="card">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i data-feather="pie-chart" style="width: 18px; height: 18px; color: var(--secondary);"></i>
                Rekomendasi Karir Terpopuler (Top 5)
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 14px;">
                @if(count($topCareers) > 0)
                    @php $rank = 1; @endphp
                    @foreach($topCareers as $careerTitle => $count)
                        <div class="flex items-center justify-between" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 10px; padding: 10px 16px;">
                            <div class="flex items-center gap-3">
                                <span style="background: rgba(168,85,247,0.1); color: var(--secondary); font-weight: 800; font-size: 0.9rem; width: 24px; height: 24px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                    {{ $rank++ }}
                                </span>
                                <span style="font-weight: 600; font-size: 0.95rem;">{{ $careerTitle }}</span>
                            </div>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary);">
                                {{ $count }} kali disarankan
                            </span>
                        </div>
                    @endforeach
                @else
                    <p style="color: var(--text-secondary); text-align: center; padding: 20px; font-style: italic;">Belum ada data rekomendasi karir.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Student List / Management -->
    <div>
        <div class="flex justify-between items-center" style="margin-bottom: 16px;">
            <h2 style="font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-feather="users" style="width: 20px; height: 20px; color: var(--primary);"></i>
                Daftar Mahasiswa & Hasil Analisis
            </h2>
        </div>

        <div class="card table-responsive" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>CV Status</th>
                        <th>Top Skills Terdeteksi</th>
                        <th>Jalur Karir Utama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php $analysis = $student->latestCvAnalysis; @endphp
                        <tr>
                            <td><strong style="color: white;">{{ $student->name }}</strong></td>
                            <td>{{ $student->email }}</td>
                            <td>
                                @if($analysis)
                                    <span style="color: var(--success); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i data-feather="check-circle" style="width: 14px; height: 14px;"></i> Dianalisis
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Belum Upload</span>
                                @endif
                            </td>
                            <td>
                                @if($analysis && !empty($analysis->skills['hard']))
                                    <div class="skill-tag-group" style="max-width: 250px;">
                                        @foreach(array_slice($analysis->skills['hard'], 0, 3) as $skill)
                                            <span class="skill-tag skill-tag-hard" style="font-size: 0.75rem; padding: 3px 6px;">{{ $skill }}</span>
                                        @endforeach
                                        @if(count($analysis->skills['hard']) > 3)
                                            <span style="color: var(--text-muted); font-size: 0.75rem;">+{{ count($analysis->skills['hard']) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($analysis && !empty($analysis->careers))
                                    <strong style="color: white; font-size: 0.9rem;">{{ $analysis->careers[0]['title'] }}</strong>
                                    <span style="color: var(--primary); font-size: 0.8rem; font-weight: 700; margin-left: 4px;">
                                        ({{ $analysis->careers[0]['match_rate'] }}%)
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($analysis)
                                    <a href="{{ route('admin.students.analysis', $student->id) }}" target="_blank" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex;">
                                        <i data-feather="file-text" style="width: 14px; height: 14px;"></i> Detail AI
                                    </a>
                                @else
                                    <button class="btn btn-secondary" disabled style="padding: 6px 12px; font-size: 0.8rem; opacity: 0.4;">
                                        No Data
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">Belum ada mahasiswa terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Internship Management -->
    <div>
        <div class="flex justify-between items-center" style="margin-bottom: 16px;">
            <h2 style="font-size: 1.3rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i data-feather="briefcase" style="width: 20px; height: 20px; color: var(--accent);"></i>
                Kelola Lowongan Magang
            </h2>
            <button class="btn btn-primary" onclick="openAddModal()">
                <i data-feather="plus" style="width: 16px; height: 16px;"></i> Tambah Lowongan
            </button>
        </div>

        <div class="card table-responsive" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Posisi</th>
                        <th>Perusahaan</th>
                        <th>Lokasi</th>
                        <th>Keahlian yang Dibutuhkan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($internships as $internship)
                        <tr>
                            <td><strong style="color: white;">{{ $internship->title }}</strong></td>
                            <td>{{ $internship->company }}</td>
                            <td>
                                <span style="display:inline-flex; align-items:center; gap: 4px;">
                                    <i data-feather="map-pin" style="width: 13px; height: 13px; color: var(--text-secondary);"></i> {{ $internship->location }}
                                </span>
                            </td>
                            <td>
                                <div class="skill-tag-group">
                                    @foreach($internship->required_skills ?? [] as $skill)
                                        <span class="skill-tag" style="font-size: 0.75rem; padding: 3px 6px;">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex;" 
                                            onclick="openEditModal({{ json_encode($internship) }})">
                                        <i data-feather="edit" style="width: 14px; height: 14px;"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.internships.destroy', $internship->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan magang ini?');" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.8rem; display: inline-flex;">
                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 30px;">Belum ada lowongan magang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Internship -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeAddModal()">&times;</button>
        <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 24px; color: white;">
            Tambah Lowongan Magang Baru
        </h3>
        
        <form action="{{ route('admin.internships.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="add_title" class="form-label">Judul Posisi</label>
                <input type="text" id="add_title" name="title" class="form-control" placeholder="Contoh: Laravel Developer Intern" required>
            </div>

            <div class="form-group">
                <label for="add_company" class="form-label">Nama Perusahaan</label>
                <input type="text" id="add_company" name="company" class="form-control" placeholder="Contoh: PT Techno Inovasi" required>
            </div>

            <div class="form-group">
                <label for="add_location" class="form-label">Lokasi</label>
                <input type="text" id="add_location" name="location" class="form-control" placeholder="Contoh: Jakarta (Hybrid)" required>
            </div>

            <div class="form-group">
                <label for="add_required_skills" class="form-label">Keahlian yang Dibutuhkan (Pisahkan dengan koma)</label>
                <input type="text" id="add_required_skills" name="required_skills" class="form-control" placeholder="Contoh: PHP, Laravel, MySQL, Git" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="add_description" class="form-label">Deskripsi Pekerjaan</label>
                <textarea id="add_description" name="description" class="form-control" style="min-height: 100px; resize: vertical;" placeholder="Tuliskan detail pekerjaan dan persyaratan magang..." required></textarea>
            </div>

            <div class="flex justify-end gap-3" style="justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Lowongan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Internship -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeEditModal()">&times;</button>
        <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 24px; color: white;">
            Edit Lowongan Magang
        </h3>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="edit_title" class="form-label">Judul Posisi</label>
                <input type="text" id="edit_title" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_company" class="form-label">Nama Perusahaan</label>
                <input type="text" id="edit_company" name="company" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_location" class="form-label">Lokasi</label>
                <input type="text" id="edit_location" name="location" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="edit_required_skills" class="form-label">Keahlian yang Dibutuhkan (Pisahkan dengan koma)</label>
                <input type="text" id="edit_required_skills" name="required_skills" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="edit_description" class="form-label">Deskripsi Pekerjaan</label>
                <textarea id="edit_description" name="description" class="form-control" style="min-height: 100px; resize: vertical;" required></textarea>
            </div>

            <div class="flex justify-end gap-3" style="justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Perbarui Lowongan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal Selectors
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');

    // Add Modal functions
    function openAddModal() {
        addModal.classList.add('active');
    }
    function closeAddModal() {
        addModal.classList.remove('active');
    }

    // Edit Modal functions
    function openEditModal(internship) {
        // Populate fields
        document.getElementById('edit_title').value = internship.title;
        document.getElementById('edit_company').value = internship.company;
        document.getElementById('edit_location').value = internship.location;
        document.getElementById('edit_description').value = internship.description;
        
        // Convert skills array back to comma separated string
        const skillsString = (internship.required_skills || []).join(', ');
        document.getElementById('edit_required_skills').value = skillsString;
        
        // Update Form Action
        editForm.action = `/admin/internships/${internship.id}`;
        
        editModal.classList.add('active');
    }
    function closeEditModal() {
        editModal.classList.remove('active');
    }

    // Close modals on clicking background
    window.onclick = function(event) {
        if (event.target == addModal) {
            closeAddModal();
        }
        if (event.target == editModal) {
            closeEditModal();
        }
    }
</script>
@endsection
