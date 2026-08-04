@extends('layouts.app')

@section('title', 'Data Ujian')

@section('content')
<style>
.exam-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.exam-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}
.exam-row td {
    border-top: none;
    border-bottom: 1px solid #f0f0f5;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    background: transparent;
}
.exam-row:last-child td {
    border-bottom: none;
}
.exam-row + .exam-row td {
    border-top: 1px solid #f0f0f5;
}
.exam-avatar {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 16px; color: #fff;
    background: linear-gradient(135deg, #667eea, #764ba2);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}
.status-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}
.status-upcoming { background: #10b981; box-shadow: 0 0 8px rgba(16,185,129,0.4); }
.status-today { background: #f59e0b; box-shadow: 0 0 8px rgba(245,158,11,0.4); }
.status-past { background: #9ca3af; box-shadow: 0 0 8px rgba(156,163,175,0.3); }
.type-badge {
    display: inline-flex; align-items: center;
    padding: 5px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.3px;
}
.bg-uh { background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #475569; }
.bg-uts { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1d4ed8; }
.bg-uas { background: linear-gradient(135deg, #e0f2fe, #bae6fd); color: #0369a1; }
.bg-pat { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; }
.bg-tryout { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857; }
.time-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 6px 14px;
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 13px;
    color: #334155;
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
.info-chip i { font-size: 12px; color: #667eea; }
.action-btn {
    width: 36px; height: 36px;
    border-radius: 10px !important;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    transition: all 0.2s;
    text-decoration: none;
}
.action-btn:hover { transform: translateY(-2px); }
.action-btn.edit:hover { background: #fef3c7; border-color: #f59e0b; color: #b45309; }
.action-btn.delete:hover { background: #fee2e2; border-color: #ef4444; color: #dc2626; }
.empty-state-icon { font-size: 5rem; background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

/* Table header styling */
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

/* Stats card styling */
.stat-card {
    border-radius: 16px;
    border: none;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    transition: all 0.3s;
    padding: 1.25rem;
}
.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}
.stat-card .stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-file-alt me-2" style="color: #667eea;"></i>Data Ujian
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $exams->total() ?? $exams->count() }}</strong> ujian
                </p>
            </div>
            <button type="button" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-2"></i>Tambah Ujian
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
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
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857;">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Akan Datang</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $exams->filter(fn($e) => $e->date->isFuture())->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Hari Ini</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $exams->filter(fn($e) => $e->date->isToday())->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #e2e8f0, #cbd5e1); color: #475569;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Selesai</div>
                        <div class="fw-bold" style="font-size: 24px; color: #1e293b;">{{ $exams->filter(fn($e) => $e->date->isPast())->count() }}</div>
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
                        <input type="text" id="searchUjian" class="form-control border-0 ps-2" placeholder="Cari ujian, mapel, kelas..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                    <div class="d-flex gap-2">
                        <select id="filterType" class="form-select form-select-sm" style="border-radius: 10px; border-color: #e2e8f0; min-width: 140px;" onchange="filterTable()">
                            <option value="">Semua Tipe</option>
                            <option value="Ulangan Harian">Ulangan Harian</option>
                            <option value="UTS">UTS</option>
                            <option value="UAS">UAS</option>
                            <option value="PAT">PAT</option>
                            <option value="Try Out">Try Out</option>
                        </select>
                        <select id="filterStatus" class="form-select form-select-sm" style="border-radius: 10px; border-color: #e2e8f0; min-width: 120px;" onchange="filterTable()">
                            <option value="">Semua Status</option>
                            <option value="Akan Datang">Akan Datang</option>
                            <option value="Hari Ini">Hari Ini</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
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
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                            @php
                                $typeLabels = ['uh' => 'Ulangan Harian', 'uts' => 'UTS', 'uas' => 'UAS', 'pat' => 'PAT', 'tryout' => 'Try Out'];
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
                                        <div class="exam-avatar">
                                            {{ strtoupper(substr($exam->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color: #1e293b; font-size: 15px;">{{ $exam->name }}</div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="info-chip">
                                                    <i class="fas fa-calendar-alt"></i>{{ $exam->date->format('d M Y') }}
                                                </span>
                                                @if($exam->start_time && $exam->end_time)
                                                <span class="info-chip">
                                                    <i class="fas fa-hourglass-half"></i>{{ substr($exam->start_time, 0, 5) }} - {{ substr($exam->end_time, 0, 5) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($exam->subject)
                                    <span class="info-chip">
                                        <i class="fas fa-book"></i>{{ $exam->subject->name }}
                                    </span>
                                    @else
                                    <span class="text-muted" style="font-size: 13px;">-</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($exam->classes as $class)
                                    <span class="info-chip">
                                        <i class="fas fa-users"></i>{{ $class->name }}
                                    </span>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="info-chip">
                                        <i class="fas fa-chalkboard-teacher"></i>{{ $exam->teacher->name }}
                                    </span>
                                </td>
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
                                    <span class="type-badge {{ 'bg-' . $exam->type }}">
                                        @php $typeIcons = ['uh' => 'fa-clipboard-list', 'uts' => 'fa-thermometer-half', 'uas' => 'fa-graduation-cap', 'pat' => 'fa-trophy', 'tryout' => 'fa-flask']; @endphp
                                        <i class="fas {{ $typeIcons[$exam->type] ?? 'fa-file-alt' }} me-2"></i>
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
                                        <a href="{{ route('admin.exams.edit', $exam) }}" class="action-btn edit" title="Edit Ujian">
                                            <i class="fas fa-pen" style="font-size: 13px;"></i>
                                        </a>
                                        <button type="button" class="action-btn delete btn btn-delete" title="Hapus Ujian"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.exams.destroy', $exam) }}"
                                            data-name="{{ $exam->name }}" style="border: none; cursor: pointer;">
                                            <i class="fas fa-trash" style="font-size: 13px; pointer-events: none;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div class="empty-state-icon mb-3">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Ujian</h5>
                                        <p class="text-muted mb-4" style="font-size: 14px;">Mulai dengan menambahkan ujian baru untuk kelas.</p>
                                        <button type="button" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;" data-bs-toggle="modal" data-bs-target="#createModal">
                                            <i class="fas fa-plus me-2"></i>Tambah Ujian
                                        </button>
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
                <div class="d-flex justify-content-center">
                    {{ $exams->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-font text-primary me-1"></i> Nama Ujian</label>
                    <input type="text" id="crName" class="form-control" placeholder="Cth: Ulangan Harian 1" required style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-book text-info me-1"></i> Mata Pelajaran</label>
                        <select id="crSubject" class="form-select" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-users text-success me-1"></i> Kelas <span class="text-muted fw-normal small">(bisa pilih lebih dari satu)</span></label>
                        <div style="background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; padding: 8px 12px; max-height: 160px; overflow-y: auto;">
                            @foreach($classes as $class)
                            <label class="d-flex align-items-center gap-2 py-1" style="cursor: pointer;">
                                <input type="checkbox" class="cr-class" value="{{ $class->id }}" style="width: 16px; height: 16px; accent-color: #667eea;">
                                <span style="font-size: 14px;">{{ $class->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-chalkboard-teacher text-warning me-1"></i> Guru</label>
                        <select id="crTeacher" class="form-select" required style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-tag text-secondary me-1"></i> Tipe Ujian</label>
                        <select id="crType" class="form-select" required style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                            <option value="uh">Ulangan Harian</option>
                            <option value="uts">UTS</option>
                            <option value="uas">UAS</option>
                            <option value="pat">PAT</option>
                            <option value="tryout">Try Out</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-calendar-alt text-danger me-1"></i> Tanggal</label>
                    <input type="date" id="crDate" class="form-control" required style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-play-circle text-success me-1"></i> Jam Mulai</label>
                        <input type="time" id="crStart" class="form-control" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;"><i class="fas fa-stop-circle text-danger me-1"></i> Jam Selesai</label>
                        <input type="time" id="crEnd" class="form-control" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                    </div>
                </div>
                <div class="mt-3">
                    <div class="form-check form-switch p-0 d-flex align-items-center gap-3" style="min-height: auto;">
                        <input type="checkbox" id="crAntiCurang" class="form-check-input m-0" role="switch" style="width: 44px; height: 24px; cursor: pointer;">
                        <label class="form-check-label fw-semibold small" for="crAntiCurang" style="cursor: pointer;">
                            <i class="fas fa-shield-alt text-danger me-1"></i> <span style="color: #dc2626;">Anti-Cheating</span> <span class="text-muted fw-normal small">(layar terkunci, pantau tab)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 pb-4 pt-3 border-0" style="background: #f8fafc;">
                <button type="button" class="btn px-4" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnSave" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-body py-5 px-4">
                <div class="mb-3">
                    <div style="font-size: 4rem; color: #f5576c;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1" style="color: #1e293b;">Hapus Ujian</h5>
                <p class="text-muted mb-0" id="deleteMessage">Yakin ingin menghapus ujian ini?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn px-4" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnConfirmDelete" class="btn btn-danger px-4 shadow-sm" style="border-radius: 12px;">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    var input = document.getElementById('searchUjian').value.toLowerCase();
    var typeFilter = document.getElementById('filterType').value.toLowerCase();
    var statusFilter = document.getElementById('filterStatus').value.toLowerCase();
    var table = document.getElementById('examTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var text = rows[i].textContent.toLowerCase();
        var typeCell = rows[i].querySelector('td:nth-child(7)');
        var statusCell = rows[i].querySelector('td:nth-child(8)');
        var typeText = typeCell ? typeCell.textContent.trim().toLowerCase() : '';
        var statusText = statusCell ? statusCell.textContent.trim().toLowerCase() : '';
        var matchSearch = text.indexOf(input) > -1;
        var matchType = !typeFilter || typeText.indexOf(typeFilter) > -1;
        var matchStatus = !statusFilter || statusText.indexOf(statusFilter) > -1;
        rows[i].style.display = (matchSearch && matchType && matchStatus) ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    document.getElementById('btnSave').addEventListener('click', function () {
        var btn = this;
        var checkedClasses = document.querySelectorAll('.cr-class:checked');
        if (!document.getElementById('crName').value || checkedClasses.length === 0 || !document.getElementById('crTeacher').value || !document.getElementById('crDate').value) {
            alert('Harap isi semua field yang wajib (termasuk pilih minimal 1 kelas).');
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('name', document.getElementById('crName').value);
        fd.append('subject_id', document.getElementById('crSubject').value || '');
        checkedClasses.forEach(function (cb) { fd.append('class_ids[]', cb.value); });
        fd.append('teacher_id', document.getElementById('crTeacher').value);
        fd.append('type', document.getElementById('crType').value);
        fd.append('date', document.getElementById('crDate').value);
        fd.append('start_time', document.getElementById('crStart').value || '');
        fd.append('end_time', document.getElementById('crEnd').value || '');
        fd.append('anti_curang', document.getElementById('crAntiCurang').checked ? '1' : '0');
        fetch('{{ route('admin.exams.store') }}', { method: 'POST', body: fd })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });

    var _deleteUrl = '';
    document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        _deleteUrl = btn.getAttribute('data-url');
        var name = btn.getAttribute('data-name');
        document.getElementById('deleteMessage').textContent = 'Yakin ingin menghapus "' + name + '"?';
    });
    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menghapus...';
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('_method', 'DELETE');
        fetch(_deleteUrl, { method: 'POST', body: fd })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });
});
</script>
@endpush
