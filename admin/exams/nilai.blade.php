@extends('layouts.app')

@section('title', 'Nilai Ujian')

@section('content')
<style>
.nilai-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.nilai-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(250, 112, 154, 0.12);
}
.nilai-row td {
    border-top: none;
    border-bottom: 1px solid #f0f0f5;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    background: transparent;
}
.nilai-row:last-child td { border-bottom: none; }
.nilai-row + .nilai-row td { border-top: 1px solid #f0f0f5; }
.nilai-avatar {
    width: 46px; height: 46px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 15px; color: #fff;
    background: linear-gradient(135deg, #fa709a, #fee140);
    box-shadow: 0 4px 12px rgba(250, 112, 154, 0.3);
    flex-shrink: 0;
}
.stat-card {
    border-radius: 16px;
    border: none;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    transition: all 0.3s;
    padding: 1.25rem;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
.stat-card .stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.info-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 13px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
}
.info-chip i { font-size: 12px; color: #fa709a; }
.type-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
}
.view-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 18px;
    border-radius: 10px;
    font-weight: 600; font-size: 13px;
    background: linear-gradient(135deg, #fa709a, #fee140);
    color: #fff; border: none;
    transition: all 0.25s;
    text-decoration: none;
}
.view-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(250, 112, 154, 0.35); color: #fff; }
#examTable thead th {
    border-bottom: 2px solid #e2e8f0;
    color: #64748b;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 0.75rem;
    font-weight: 700;
    background: transparent;
}
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-star me-2" style="color: #fa709a;"></i>Nilai Ujian
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $exams->total() ?? $exams->count() }}</strong> ujian
                </p>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="input-group" style="max-width: 300px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0">
                            <i class="fas fa-search" style="color: #94a3b8;"></i>
                        </span>
                        <input type="text" id="searchNilai" class="form-control border-0 ps-2" placeholder="Cari ujian..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                    <form action="{{ route('admin.exams.nilai-download') }}" method="GET" class="d-flex align-items-center gap-2 ms-auto" target="_blank">
                        <select name="class_id" class="form-select form-select-sm" style="width: auto; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px;">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <select name="subject_id" class="form-select form-select-sm" style="width: auto; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px;">
                            <option value="">Semua Mapel</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <select name="type" class="form-select form-select-sm" style="width: auto; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 13px;">
                            <option value="">Semua Tipe</option>
                            <option value="uh" {{ request('type') == 'uh' ? 'selected' : '' }}>Ulangan Harian</option>
                            <option value="uts" {{ request('type') == 'uts' ? 'selected' : '' }}>UTS</option>
                            <option value="uas" {{ request('type') == 'uas' ? 'selected' : '' }}>UAS</option>
                            <option value="pat" {{ request('type') == 'pat' ? 'selected' : '' }}>PAT</option>
                            <option value="tryout" {{ request('type') == 'tryout' ? 'selected' : '' }}>Try Out</option>
                        </select>
                        <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border-radius: 10px; font-weight: 600; font-size: 13px; padding: 6px 16px; border: none;">
                            <i class="fas fa-download me-1"></i> Download Excel
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="examTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Ujian</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($exams as $exam)
                            @php
                                $typeLabels = ['uh' => 'Ulangan Harian', 'uts' => 'UTS', 'uas' => 'UAS', 'pat' => 'PAT', 'tryout' => 'Try Out'];
                                $typeColors = ['uh' => ['bg' => '#f1f5f9', 'txt' => '#475569'], 'uts' => ['bg' => '#dbeafe', 'txt' => '#1d4ed8'], 'uas' => ['bg' => '#e0f2fe', 'txt' => '#0369a1'], 'pat' => ['bg' => '#fef3c7', 'txt' => '#b45309'], 'tryout' => ['bg' => '#d1fae5', 'txt' => '#047857']];
                                $tc = $typeColors[$exam->type] ?? ['bg' => '#f1f5f9', 'txt' => '#475569'];
                            @endphp
                            <tr class="nilai-row">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nilai-avatar">{{ strtoupper(substr($exam->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="fw-bold" style="color: #1e293b; font-size: 15px;">{{ $exam->name }}</div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="info-chip"><i class="fas fa-calendar-alt"></i>{{ $exam->date instanceof \Carbon\Carbon ? $exam->date->format('d M Y') : $exam->date }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($exam->subject)
                                    <span class="info-chip"><i class="fas fa-book"></i>{{ $exam->subject->name }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="info-chip"><i class="fas fa-users"></i>{{ $exam->classes->pluck('name')->join(', ') }}</span></td>
                                <td><span class="info-chip"><i class="fas fa-chalkboard-teacher"></i>{{ $exam->teacher->name }}</span></td>
                                <td><span class="info-chip"><i class="fas fa-calendar-alt"></i>{{ $exam->date instanceof \Carbon\Carbon ? $exam->date->format('d/m/Y') : $exam->date }}</span></td>
                                <td>
                                    @if($exam->start_time && $exam->end_time)
                                    <div class="d-flex align-items-center gap-1" style="font-size: 13px; color: #334155;">
                                        <i class="fas fa-play-circle" style="color: #10b981; font-size: 11px;"></i>
                                        <span class="fw-semibold">{{ substr($exam->start_time, 0, 5) }}</span>
                                        <span style="color: #94a3b8;">—</span>
                                        <i class="fas fa-stop-circle" style="color: #ef4444; font-size: 11px;"></i>
                                        <span class="fw-semibold">{{ substr($exam->end_time, 0, 5) }}</span>
                                    </div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.exams.nilai-detail', $exam) }}" class="view-btn">
                                        <i class="fas fa-eye"></i> Lihat Nilai
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 5rem; background: linear-gradient(135deg, #fa709a, #fee140); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Ujian</h5>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Belum ada data ujian untuk dilihat nilainya.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($exams->hasPages())
            <div class="card-footer bg-transparent border-top-0 pb-4 px-4">
                <div class="d-flex justify-content-center">{{ $exams->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    var input = document.getElementById('searchNilai').value.toLowerCase();
    var table = document.getElementById('examTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        rows[i].style.display = rows[i].textContent.toLowerCase().indexOf(input) > -1 ? '' : 'none';
    }
}
</script>
@endpush
