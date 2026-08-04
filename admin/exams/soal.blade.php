@extends('layouts.app')

@section('title', 'Soal Ujian')

@section('content')
<style>
.exam-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.exam-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(161, 140, 209, 0.15);
}
.exam-row td {
    border-top: none;
    border-bottom: 1px solid #f0f0f5;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    background: transparent;
}
.exam-row:last-child td { border-bottom: none; }
.exam-row + .exam-row td { border-top: 1px solid #f0f0f5; }
.exam-avatar {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 16px; color: #fff;
    background: linear-gradient(135deg, #a18cd1, #fbc2eb);
    box-shadow: 0 4px 12px rgba(161, 140, 209, 0.3);
    flex-shrink: 0;
}
.status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
.status-upcoming { background: #10b981; box-shadow: 0 0 8px rgba(16,185,129,0.4); }
.status-today { background: #f59e0b; box-shadow: 0 0 8px rgba(245,158,11,0.4); }
.status-past { background: #9ca3af; box-shadow: 0 0 8px rgba(156,163,175,0.3); }
.info-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 13px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
}
.info-chip i { font-size: 12px; color: #a18cd1; }
.time-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 4px 12px;
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px;
    color: #334155;
}
.manage-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 20px;
    border-radius: 12px;
    font-weight: 600; font-size: 13px;
    background: linear-gradient(135deg, #a18cd1, #fbc2eb);
    color: #fff; border: none;
    transition: all 0.25s;
    text-decoration: none;
}
.manage-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(161, 140, 209, 0.4); color: #fff; }
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
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-question-circle me-2" style="color: #a18cd1;"></i>Soal Ujian
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $exams->total() ?? $exams->count() }}</strong> ujian
                </p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Ujian</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $exams->total() ?? $exams->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Total Soal</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $exams->sum(fn($e) => $e->questions_count ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Akan Datang</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $exams->filter(fn($e) => $e->date->isFuture())->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="input-group" style="max-width: 320px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0">
                            <i class="fas fa-search" style="color: #94a3b8;"></i>
                        </span>
                        <input type="text" id="searchSoal" class="form-control border-0 ps-2" placeholder="Cari ujian, mapel, kelas..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                    <select id="filterType" class="form-select form-select-sm" style="border-radius: 10px; border-color: #e2e8f0; min-width: 140px;" onchange="filterTable()">
                        <option value="">Semua Tipe</option>
                        <option value="Ulangan Harian">Ulangan Harian</option>
                        <option value="UTS">UTS</option>
                        <option value="UAS">UAS</option>
                        <option value="PAT">PAT</option>
                        <option value="Try Out">Try Out</option>
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="examTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Ujian</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Waktu</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                            @php
                                $typeLabels = ['uh' => 'Ulangan Harian', 'uts' => 'UTS', 'uas' => 'UAS', 'pat' => 'PAT', 'tryout' => 'Try Out'];
                                $typeColors = ['uh' => ['bg' => '#f1f5f9', 'txt' => '#475569'], 'uts' => ['bg' => '#dbeafe', 'txt' => '#1d4ed8'], 'uas' => ['bg' => '#e0f2fe', 'txt' => '#0369a1'], 'pat' => ['bg' => '#fef3c7', 'txt' => '#b45309'], 'tryout' => ['bg' => '#d1fae5', 'txt' => '#047857']];
                                $tc = $typeColors[$exam->type] ?? ['bg' => '#f1f5f9', 'txt' => '#475569'];
                                $isFuture = $exam->date->isFuture();
                                $isToday = $exam->date->isToday();
                                $isPast = $exam->date->isPast();
                                if ($isToday) { $statusLabel = 'Hari Ini'; $statusClass = 'today'; }
                                elseif ($isFuture) { $statusLabel = 'Akan Datang'; $statusClass = 'upcoming'; }
                                else { $statusLabel = 'Selesai'; $statusClass = 'past'; }
                            @endphp
                            <tr class="exam-row">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="exam-avatar">{{ strtoupper(substr($exam->name, 0, 2)) }}</div>
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
                                <td>
                                    @if($exam->subject)
                                    <span class="info-chip"><i class="fas fa-book"></i>{{ $exam->subject->name }}</span>
                                    @else
                                    <span class="text-muted" style="font-size: 13px;">-</span>
                                    @endif
                                </td>
                                <td><span class="info-chip"><i class="fas fa-users"></i>@foreach($exam->classes as $class){{ $class->name }}@if(!$loop->last), @endif @endforeach</span></td>
                                <td><span class="info-chip"><i class="fas fa-chalkboard-teacher"></i>{{ $exam->teacher->name }}</span></td>
                                <td>
                                    @if($exam->start_time && $exam->end_time)
                                    <div class="time-block">
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
                                <td class="text-center">
                                    <span class="d-inline-flex align-items-center px-3 py-1 rounded-pill fw-semibold" style="font-size: 12px; background: {{ $isToday ? 'rgba(245,158,11,0.12)' : ($isFuture ? 'rgba(16,185,129,0.12)' : 'rgba(156,163,175,0.12)') }}; color: {{ $isToday ? '#b45309' : ($isFuture ? '#047857' : '#6b7280') }};">
                                        <span class="status-dot status-{{ $statusClass }}"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.exams.questions.index', $exam) }}" class="manage-btn">
                                            <i class="fas fa-list"></i> Kelola Soal
                                        </a>
                                        <button type="button"
                                            class="manage-btn"
                                            style="background: linear-gradient(135deg, #34d399, #059669);"
                                            title="Duplikasi Ujian"
                                            data-bs-toggle="modal"
                                            data-bs-target="#duplicateModal"
                                            data-duplicate-url="{{ route('admin.exams.duplicate', $exam) }}"
                                            data-exam-name="{{ $exam->name }}">
                                            <i class="fas fa-copy"></i> Duplikasi
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 5rem; background: linear-gradient(135deg, #a18cd1, #fbc2eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Ujian</h5>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Belum ada data ujian untuk dikelola soalnya.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($exams, 'links') && $exams->hasPages())
            <div class="card-footer bg-transparent border-top-0 pb-4 px-4">
                <div class="d-flex justify-content-center">{{ $exams->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>
<!-- Modal Konfirmasi Duplikasi -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center" style="border-radius: 20px; border: none;">
            <div class="modal-body py-5">
                <div class="mb-3" style="font-size: 4rem; color: #059669;">
                    <i class="fas fa-copy"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #1e293b;">Duplikasi Ujian</h5>
                <p class="text-muted mb-0 small" id="duplicateModalMessage">Yakin ingin menduplikasi ujian ini beserta semua soalnya?</p>
            </div>
            <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-4" style="border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 12px; font-weight: 600;" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnConfirmDuplicate" class="btn btn-success px-4 shadow-sm" style="border-radius: 12px; font-weight: 700;">
                    <i class="fas fa-copy me-1"></i> Ya, Duplikasi
                </button>
            </div>
        </div>
    </div>
</div>

<form id="duplicateForm" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@push('scripts')
<script>
function filterTable() {
    var input = document.getElementById('searchSoal').value.toLowerCase();
    var typeFilter = document.getElementById('filterType').value.toLowerCase();
    var table = document.getElementById('examTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var text = rows[i].textContent.toLowerCase();
        var typeCell = rows[i].querySelector('td:nth-child(7)');
        var typeText = typeCell ? typeCell.textContent.trim().toLowerCase() : '';
        var matchSearch = text.indexOf(input) > -1;
        var matchType = !typeFilter || typeText.indexOf(typeFilter) > -1;
        rows[i].style.display = (matchSearch && matchType) ? '' : 'none';
    }
}

 document.addEventListener('DOMContentLoaded', function () {
    var _duplicateUrl = '';
    var modal = document.getElementById('duplicateModal');
    var form = document.getElementById('duplicateForm');

    modal.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        _duplicateUrl = btn.getAttribute('data-duplicate-url');
        var examName = btn.getAttribute('data-exam-name');
        document.getElementById('duplicateModalMessage').textContent =
            'Yakin ingin menduplikasi ujian "' + examName + '" beserta semua soalnya?';
    });

    document.getElementById('btnConfirmDuplicate').addEventListener('click', function () {
        form.action = _duplicateUrl;
        form.submit();
    });
});
</script>
@endpush
