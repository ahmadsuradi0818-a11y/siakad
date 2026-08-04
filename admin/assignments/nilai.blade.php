@extends('layouts.app')

@section('title', 'Nilai Tugas')

@section('content')
<style>
.nilai-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.nilai-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(162, 155, 254, 0.12);
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
    background: linear-gradient(135deg, #a18cd1, #fbc2eb);
    box-shadow: 0 4px 12px rgba(162, 155, 254, 0.3);
    flex-shrink: 0;
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
.info-chip i { font-size: 12px; color: #a18cd1; }
.view-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 18px;
    border-radius: 10px;
    font-weight: 600; font-size: 13px;
    background: linear-gradient(135deg, #a18cd1, #fbc2eb);
    color: #fff; border: none;
    transition: all 0.25s;
    text-decoration: none;
}
.view-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(162, 155, 254, 0.35); color: #fff; }
#nilaiTable thead th {
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
                    <i class="fas fa-star me-2" style="color: #a18cd1;"></i>Nilai Tugas
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $assignments->total() ?? $assignments->count() }}</strong> tugas
                </p>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="input-group" style="max-width: 300px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0">
                            <i class="fas fa-search" style="color: #94a3b8;"></i>
                        </span>
                        <input type="text" id="searchNilai" class="form-control border-0 ps-2" placeholder="Cari tugas..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="nilaiTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Tugas</th>
                                <th>Mapel</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Tenggat</th>
                                <th class="text-center">Pengumpulan</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assignments as $assignment)
                            <tr class="nilai-row">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="nilai-avatar">{{ strtoupper(substr($assignment->name, 0, 2)) }}</div>
                                        <div>
                                            <div class="fw-bold" style="color: #1e293b; font-size: 15px;">{{ $assignment->name }}</div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="info-chip"><i class="fas fa-calendar-alt"></i>{{ $assignment->created_at->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($assignment->subject)
                                    <span class="info-chip"><i class="fas fa-book"></i>{{ $assignment->subject->name }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="info-chip"><i class="fas fa-users"></i>{{ $assignment->class->name }}</span></td>
                                <td><span class="info-chip"><i class="fas fa-chalkboard-teacher"></i>{{ $assignment->teacher->name }}</span></td>
                                <td>
                                    @php $isOverdue = now()->gt($assignment->due_date); @endphp
                                    <span class="info-chip" style="background: {{ $isOverdue ? '#fee2e2' : '#d1fae5' }}; border-color: {{ $isOverdue ? '#fecaca' : '#a7f3d0' }};">
                                        <i class="fas fa-clock" style="color: {{ $isOverdue ? '#dc2626' : '#047857' }};"></i>
                                        {{ $assignment->due_date->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold" style="color: #1e293b;">{{ $assignment->submissions_count }}</span>
                                    <small class="text-muted">/ {{ $assignment->class->students_count ?? '?' }}</small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.assignments.nilai-detail', $assignment) }}" class="view-btn">
                                        <i class="fas fa-eye"></i> Lihat Nilai
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 5rem; background: linear-gradient(135deg, #a18cd1, #fbc2eb); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                                            <i class="fas fa-tasks"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Tugas</h5>
                                        <p class="text-muted mb-0" style="font-size: 14px;">Belum ada data tugas untuk dinilai.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($assignments->hasPages())
            <div class="card-footer bg-transparent border-top-0 pb-4 px-4">
                <div class="d-flex justify-content-center">{{ $assignments->links() }}</div>
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
    var table = document.getElementById('nilaiTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        rows[i].style.display = rows[i].textContent.toLowerCase().indexOf(input) > -1 ? '' : 'none';
    }
}
</script>
@endpush
