@extends('layouts.app')

@section('title', 'Monitoring: ' . $exam->name)

@section('content')
<style>
.student-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.student-row:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79, 172, 254, 0.1); }
.student-row td {
    border-top: none;
    border-bottom: 1px solid #f0f0f5;
    padding: 0.9rem 0.75rem;
    vertical-align: middle;
    background: transparent;
}
.student-row:last-child td { border-bottom: none; }
.student-row + .student-row td { border-top: 1px solid #f0f0f5; }
.student-avatar {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px; color: #fff;
    background: linear-gradient(135deg, #4facfe, #00f2fe);
    flex-shrink: 0;
}
.stat-detail-card {
    border-radius: 16px;
    border: none;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    transition: all 0.3s;
    padding: 1.25rem;
}
.stat-detail-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
.stat-detail-card .icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
}
.progress-thin {
    height: 8px;
    border-radius: 20px;
    background: #e8edf2;
    overflow: hidden;
}
.progress-thin .bar {
    height: 100%;
    border-radius: 20px;
    transition: width 0.8s ease;
}
.progress-thin .bar.done { background: linear-gradient(90deg, #10b981, #34d399); }
.progress-thin .bar.ongoing { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
#studentTable thead th {
    border-bottom: 2px solid #e2e8f0;
    color: #64748b;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 0.75rem;
    font-weight: 700;
    background: transparent;
}
.action-block-btn {
    padding: 6px 16px;
    border-radius: 10px;
    font-weight: 600; font-size: 12px;
    border: none;
    transition: all 0.2s;
}
.action-block-btn:hover { transform: translateY(-1px); }
</style>

<div class="row mb-4">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 14px; border: none; box-shadow: 0 4px 16px rgba(16,185,129,0.15);">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.exams.monitoring') }}" class="btn px-3" style="border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">
                        <i class="fas fa-desktop me-2" style="color: #4facfe;"></i>Monitoring: {{ $exam->name }}
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1" style="background: #e0f2fe; color: #0369a1;">
                            <i class="fas fa-users me-1"></i>{{ $exam->classes->pluck('name')->join(', ') }}
                        </span>
                        @if($exam->subject)
                        <span class="badge rounded-pill px-3 py-1" style="background: #f1f5f9; color: #475569;">
                            <i class="fas fa-book me-1"></i>{{ $exam->subject->name }}
                        </span>
                        @endif
                        <span class="badge rounded-pill px-3 py-1" style="background: #fef3c7; color: #b45309;">
                            <i class="fas fa-list me-1"></i>{{ $totalQuestions }} Soal
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @php
                $selesai = $results->filter(fn($r) => $r->score !== null)->count();
                $mengerjakan = $results->filter(fn($r) => $r->score === null && !$r->is_blocked && $r->started_at)->count();
                $diblokir = $results->filter(fn($r) => $r->is_blocked)->count();
                $belum = $students->count() - $selesai - $mengerjakan - $diblokir;
            @endphp
            <div class="col-md-3">
                <div class="stat-detail-card d-flex align-items-center gap-3">
                    <div class="icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8;"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Total Siswa</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $students->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-detail-card d-flex align-items-center gap-3">
                    <div class="icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857;"><i class="fas fa-check"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Selesai</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $selesai }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-detail-card d-flex align-items-center gap-3">
                    <div class="icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309;"><i class="fas fa-spinner"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Mengerjakan</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $mengerjakan }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-detail-card d-flex align-items-center gap-3">
                    <div class="icon" style="background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626;"><i class="fas fa-ban"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Diblokir</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $diblokir }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0" style="color: #1e293b;">
                        <i class="fas fa-users me-2" style="color: #4facfe;"></i>Daftar Peserta Ujian
                    </h5>
                    <div class="input-group" style="max-width: 280px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0"><i class="fas fa-search" style="color: #94a3b8;"></i></span>
                        <input type="text" id="searchSiswa" class="form-control border-0 ps-2" placeholder="Cari siswa..." onkeyup="filterSiswa()" style="box-shadow: none;">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" id="studentTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Soal Dijawab</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            @php
                                $result = $results->get($student->id);
                                $answered = $answerCounts->get($student->id, 0);
                                if ($result && $result->score !== null) {
                                    $statusLabel = 'Selesai'; $statusClass = 'done';
                                    $rowBg = 'rgba(16,185,129,0.04)';
                                } elseif ($result && $result->is_blocked) {
                                    $statusLabel = 'Diblokir'; $statusClass = 'blocked';
                                    $rowBg = 'rgba(239,68,68,0.04)';
                                } elseif ($result && $result->started_at) {
                                    $statusLabel = 'Mengerjakan'; $statusClass = 'working';
                                    $rowBg = 'rgba(245,158,11,0.04)';
                                } else {
                                    $statusLabel = 'Belum Mulai'; $statusClass = 'pending';
                                    $rowBg = 'rgba(156,163,175,0.04)';
                                }
                                $statusColors = [
                                    'done' => ['bg' => '#d1fae5', 'txt' => '#047857', 'icon' => 'fa-check-circle'],
                                    'blocked' => ['bg' => '#fee2e2', 'txt' => '#dc2626', 'icon' => 'fa-ban'],
                                    'working' => ['bg' => '#fef3c7', 'txt' => '#b45309', 'icon' => 'fa-spinner fa-spin'],
                                    'pending' => ['bg' => '#f1f5f9', 'txt' => '#6b7280', 'icon' => 'fa-hourglass'],
                                ];
                                $sc = $statusColors[$statusClass] ?? $statusColors['pending'];
                                $progressPct = $totalQuestions > 0 ? round(($answered / $totalQuestions) * 100) : 0;
                            @endphp
                            <tr class="student-row">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1e293b;">{{ $student->user->name }}</div>
                                            <div class="text-muted small"><i class="fas fa-id-card me-1"></i>{{ $student->nis }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $student->nis }}</td>
                                <td class="text-center">
                                    <span class="status-pill" style="background: {{ $sc['bg'] }}; color: {{ $sc['txt'] }};">
                                        <i class="fas {{ $sc['icon'] }}"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center" style="min-width: 140px;">
                                    @if($totalQuestions > 0)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress-thin flex-grow-1">
                                            <div class="bar {{ $answered == $totalQuestions ? 'done' : 'ongoing' }}" style="width: {{ $progressPct }}%"></div>
                                        </div>
                                        <small class="fw-semibold" style="color: #1e293b; min-width: 50px;">{{ $answered }}/{{ $totalQuestions }}</small>
                                    </div>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($result && $result->score !== null)
                                    <span class="fw-bold" style="font-size: 16px; color: #047857;">{{ $result->score }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('admin.exams.toggle-block', [$exam, $student]) }}" class="d-inline" onsubmit="return confirm('{{ $result && $result->is_blocked ? 'Yakin ingin membuka blokir siswa ini?' : 'Yakin ingin memblokir siswa ini?' }}')">
                                        @csrf
                                        <button type="submit" class="action-block-btn btn {{ $result && $result->is_blocked ? 'btn-success' : 'btn-danger' }}">
                                            <i class="fas {{ $result && $result->is_blocked ? 'fa-unlock' : 'fa-ban' }} me-1"></i>
                                            {{ $result && $result->is_blocked ? 'Buka Blokir' : 'Blokir' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 4rem; opacity: 0.3;"><i class="fas fa-users"></i></div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Tidak Ada Siswa</h5>
                                        <p class="text-muted mb-0">Tidak ada siswa di kelas ini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterSiswa() {
    var input = document.getElementById('searchSiswa').value.toLowerCase();
    var table = document.getElementById('studentTable');
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
