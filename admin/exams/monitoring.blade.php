@extends('layouts.app')

@section('title', 'Monitoring Ujian')

@section('content')
<style>
.monitor-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.monitor-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.12);
}
.monitor-row td {
    border-top: none;
    border-bottom: 1px solid #f0f0f5;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    background: transparent;
}
.monitor-row:last-child td { border-bottom: none; }
.monitor-row + .monitor-row td { border-top: 1px solid #f0f0f5; }
.monitor-avatar {
    width: 46px; height: 46px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 15px; color: #fff;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
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
.progress-gradient {
    height: 10px;
    border-radius: 20px;
    background: #e8edf2;
    overflow: hidden;
}
.progress-gradient .bar {
    height: 100%;
    border-radius: 20px;
    transition: width 0.8s ease;
    background: linear-gradient(90deg, #4facfe, #00f2fe);
}
.progress-gradient .bar.high { background: linear-gradient(90deg, #10b981, #34d399); }
.progress-gradient .bar.mid { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.progress-gradient .bar.low { background: linear-gradient(90deg, #ef4444, #f87171); }
.monitor-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 18px;
    border-radius: 10px;
    font-weight: 600; font-size: 13px;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    color: #fff; border: none;
    transition: all 0.25s;
    text-decoration: none;
}
.monitor-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79, 172, 254, 0.35); color: #fff; }
.info-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 13px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
}
.info-chip i { font-size: 12px; color: #4facfe; }
.type-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
}
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
.filter-group {
    display: flex; align-items: center; gap: 8px;
    flex-wrap: wrap;
}
.filter-group input, .filter-group select {
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 6px 12px;
    font-size: 13px;
}
.filter-group .btn-filter {
    border-radius: 10px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 600;
}
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-desktop me-2" style="color: #4facfe;"></i>Monitoring Ujian
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Pantau progres ujian secara real-time
                </p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Ujian</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $totalExams }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Sudah Dinilai</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $totalGraded }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Belum Dinilai</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $totalExams - $totalGraded }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Siswa</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $totalStudents }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="input-group" style="max-width: 300px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0">
                            <i class="fas fa-search" style="color: #94a3b8;"></i>
                        </span>
                        <input type="text" id="searchMonitor" class="form-control border-0 ps-2" placeholder="Cari ujian..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                    <form method="GET" class="filter-group">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai">
                        <select name="type">
                            <option value="">Semua Tipe</option>
                            <option value="uh" {{ request('type') == 'uh' ? 'selected' : '' }}>Ulangan Harian</option>
                            <option value="uts" {{ request('type') == 'uts' ? 'selected' : '' }}>UTS</option>
                            <option value="uas" {{ request('type') == 'uas' ? 'selected' : '' }}>UAS</option>
                            <option value="pat" {{ request('type') == 'pat' ? 'selected' : '' }}>PAT</option>
                            <option value="tryout" {{ request('type') == 'tryout' ? 'selected' : '' }}>Try Out</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-filter shadow-sm" style="background: linear-gradient(135deg, #4facfe, #00f2fe); border: none;">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.exams.monitoring') }}" class="btn btn-outline-secondary btn-filter">
                            <i class="fas fa-sync me-1"></i> Reset
                        </a>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="examTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Ujian</th>
                                <th>Kelas</th>
                                <th>Waktu</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Siswa</th>
                                <th class="text-center">Dinilai</th>
                                <th style="min-width: 180px;">Progress</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                            @php
                                $total = $exam->student_count ?? 0;
                                $graded = $exam->graded_count ?? 0;
                                $ungraded = $total - $graded;
                                $progress = $total > 0 ? round(($graded / $total) * 100) : 0;
                                $barClass = $progress >= 75 ? 'high' : ($progress >= 50 ? 'mid' : 'low');
                                $typeLabels = ['uh' => 'Ulangan Harian', 'uts' => 'UTS', 'uas' => 'UAS', 'pat' => 'PAT', 'tryout' => 'Try Out'];
                                $typeColors = ['uh' => ['bg' => '#f1f5f9', 'txt' => '#475569'], 'uts' => ['bg' => '#dbeafe', 'txt' => '#1d4ed8'], 'uas' => ['bg' => '#e0f2fe', 'txt' => '#0369a1'], 'pat' => ['bg' => '#fef3c7', 'txt' => '#b45309'], 'tryout' => ['bg' => '#d1fae5', 'txt' => '#047857']];
                                $tc = $typeColors[$exam->type] ?? ['bg' => '#f1f5f9', 'txt' => '#475569'];
                                $isFuture = $exam->date->isFuture();
                                $isToday = $exam->date->isToday();
                                if ($isToday) $statusLabel = 'Hari Ini';
                                elseif ($isFuture) $statusLabel = 'Akan Datang';
                                else $statusLabel = 'Selesai';
                            @endphp
                            <tr class="monitor-row">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="monitor-avatar">{{ strtoupper(substr($exam->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="fw-bold" style="color: #1e293b; font-size: 15px;">{{ $exam->name }}</div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="info-chip"><i class="fas fa-calendar-alt"></i>{{ $exam->date->format('d M Y') }}</span>
                                                @if($exam->start_time && $exam->end_time)
                                                <span class="info-chip"><i class="fas fa-hourglass-half"></i>{{ substr($exam->start_time, 0, 5) }} - {{ substr($exam->end_time, 0, 5) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="info-chip"><i class="fas fa-users"></i>{{ $exam->classes->pluck('name')->join(', ') ?: '-' }}</span></td>
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
                                    <span class="text-muted" style="font-size: 13px;">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="type-badge" style="background: {{ $tc['bg'] }}; color: {{ $tc['txt'] }};">
                                        @php $typeIcons = ['uh' => 'fa-clipboard-list', 'uts' => 'fa-thermometer-half', 'uas' => 'fa-graduation-cap', 'pat' => 'fa-trophy', 'tryout' => 'fa-flask']; @endphp
                                        <i class="fas {{ $typeIcons[$exam->type] ?? 'fa-file-alt' }}"></i>
                                        {{ $typeLabels[$exam->type] ?? strtoupper($exam->type) }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold" style="color: #1e293b;">{{ $total }}</td>
                                <td class="text-center fw-bold" style="color: {{ $graded == $total ? '#10b981' : ($graded > 0 ? '#f59e0b' : '#94a3b8') }};">{{ $graded }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-gradient flex-grow-1">
                                                <div class="bar {{ $barClass }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="fw-bold" style="font-size: 14px; color: #1e293b; min-width: 40px;">{{ $progress }}%</span>
                                        </div>
                                        <small class="text-muted">
                                            @if($ungraded > 0)
                                            <i class="fas fa-hourglass-half me-1" style="color: #f59e0b;"></i>{{ $ungraded }} siswa belum dinilai
                                            @elseif($total > 0)
                                            <i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Semua sudah dinilai
                                            @else
                                            <i class="fas fa-info-circle me-1"></i>Belum ada siswa
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.exams.monitoring-detail', $exam) }}" class="monitor-btn">
                                        <i class="fas fa-eye"></i> Monitor
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 5rem; background: linear-gradient(135deg, #4facfe, #00f2fe); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                                            <i class="fas fa-desktop"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Ujian</h5>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Tidak ada data ujian yang sesuai filter.</p>
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
                <div class="d-flex justify-content-center">{{ $exams->links('vendor.pagination.bootstrap-5') }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    var input = document.getElementById('searchMonitor').value.toLowerCase();
    var table = document.getElementById('examTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var text = rows[i].textContent.toLowerCase();
        rows[i].style.display = text.indexOf(input) > -1 ? '' : 'none';
    }
}
</script>
@endpush
