@extends('layouts.app')

@section('title', 'Nilai: ' . $assignment->name)

@section('content')
<style>
.student-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.student-row:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(162, 155, 254, 0.1); }
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
    background: linear-gradient(135deg, #a18cd1, #fbc2eb);
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
.score-pill {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 48px; padding: 4px 16px;
    border-radius: 12px;
    font-weight: 700; font-size: 16px;
}
.score-input {
    width: 80px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 6px 10px;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
}
.score-input:focus {
    outline: none;
    border-color: #a18cd1;
    box-shadow: 0 0 0 3px rgba(162, 155, 254, 0.15);
}
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
                <a href="{{ route('admin.assignments.nilai') }}" class="btn px-3" style="border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">
                        <i class="fas fa-star me-2" style="color: #a18cd1;"></i>Nilai: {{ $assignment->name }}
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1" style="background: #f1f5f9; color: #475569;">
                            <i class="fas fa-users me-1"></i>{{ $assignment->class->name }}
                        </span>
                        @if($assignment->subject)
                        <span class="badge rounded-pill px-3 py-1" style="background: #e0f2fe; color: #0369a1;">
                            <i class="fas fa-book me-1"></i>{{ $assignment->subject->name }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @php
            $submitted = $submissions->filter(fn($s) => $s->submitted_at !== null)->count();
            $sudahDinilai = $submissions->filter(fn($s) => $s->score !== null)->count();
            $belumDinilai = $students->count() - $sudahDinilai;
            $avgScore = $submissions->filter(fn($s) => $s->score !== null)->avg('score');
        @endphp

        <div class="row g-3 mb-4">
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
                    <div class="icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #047857;"><i class="fas fa-upload"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Sudah Mengumpul</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $submitted }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-detail-card d-flex align-items-center gap-3">
                    <div class="icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309;"><i class="fas fa-check"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Sudah Dinilai</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $sudahDinilai }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-detail-card d-flex align-items-center gap-3">
                    <div class="icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); color: #4338ca;"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Rata-rata</div>
                        <div class="fw-bold" style="font-size: 28px; color: #1e293b;">{{ $avgScore ? number_format($avgScore, 1) : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0" style="color: #1e293b;">
                        <i class="fas fa-users me-2" style="color: #a18cd1;"></i>Daftar Nilai Siswa
                    </h5>
                    <div class="input-group" style="max-width: 280px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0"><i class="fas fa-search" style="color: #94a3b8;"></i></span>
                        <input type="text" id="searchSiswa" class="form-control border-0 ps-2" placeholder="Cari siswa..." onkeyup="filterSiswa()" style="box-shadow: none;">
                    </div>
                </div>

                <form action="{{ route('admin.assignments.store-nilai', $assignment) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table mb-0" id="studentTable" style="border-collapse: separate; border-spacing: 0 6px;">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                @php
                                    $submission = $submissions->get($student->id);
                                @endphp
                                <tr class="student-row">
                                    <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                                            <div>
                                                <div class="fw-semibold" style="color: #1e293b;">{{ $student->user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $student->nis }}</td>
                                    <td class="text-center">
                                        @if($submission && $submission->submitted_at)
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #d1fae5; color: #047857;">
                                            <i class="fas fa-check me-1"></i> Mengumpul
                                        </span>
                                        @else
                                        <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #fee2e2; color: #dc2626;">
                                            <i class="fas fa-times me-1"></i> Belum
                                        </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <input type="number" name="scores[{{ $student->id }}]" class="score-input"
                                            value="{{ $submission ? $submission->score : '' }}"
                                            min="0" max="100" placeholder="-">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5" style="border: none;">
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

                    @if($students->count() > 0)
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn px-4" style="background: linear-gradient(135deg, #a18cd1, #fbc2eb); color: #fff; font-weight: 600; border: none; border-radius: 10px;">
                            <i class="fas fa-save me-1"></i> Simpan Nilai
                        </button>
                    </div>
                    @endif
                </form>
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
        rows[i].style.display = rows[i].textContent.toLowerCase().indexOf(input) > -1 ? '' : 'none';
    }
}
</script>
@endpush
