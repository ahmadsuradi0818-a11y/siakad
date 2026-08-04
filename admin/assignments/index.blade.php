@extends('layouts.app')

@section('title', 'Daftar Tugas')

@section('content')
<style>
.edit-btn i { pointer-events: none; }
.delete-btn i { pointer-events: none; }
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
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-tasks me-2" style="color: #fa709a;"></i>Daftar Tugas
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $assignments->total() ?? $assignments->count() }}</strong> tugas
                </p>
            </div>
            <a href="{{ route('admin.assignments.create') }}" class="btn" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff; border-radius: 12px; font-weight: 600; border: none;">
                <i class="fas fa-plus me-1"></i> Tambah Tugas
            </a>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table mb-0" id="assignmentTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Tugas</th>
                                <th>Kelas</th>
                                <th>Guru</th>
                                <th>Mapel</th>
                                <th>Tenggat</th>
                                <th>File</th>
                                <th class="text-center" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                            <tr class="align-middle" style="background: #fff; border-radius: 12px;">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; color: #fff; background: linear-gradient(135deg, #a18cd1, #fbc2eb); flex-shrink: 0;">
                                            {{ strtoupper(substr($assignment->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold" style="color: #1e293b;">{{ $assignment->name }}</span>
                                            <div class="text-muted small">
                                                <i class="fas fa-clock me-1"></i>{{ $assignment->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #f1f5f9; color: #475569;"><i class="fas fa-users me-1"></i>{{ $assignment->class->name }}</span></td>
                                <td><span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #dbeafe; color: #1d4ed8;"><i class="fas fa-chalkboard-teacher me-1"></i>{{ $assignment->teacher->name }}</span></td>
                                <td><span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #e0f2fe; color: #0369a1;"><i class="fas fa-book me-1"></i>{{ $assignment->subject->name }}</span></td>
                                <td>
                                    @php
                                        $isOverdue = now()->gt($assignment->due_date);
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: {{ $isOverdue ? '#fee2e2' : '#d1fae5' }}; color: {{ $isOverdue ? '#dc2626' : '#047857' }};">
                                        <i class="fas fa-clock me-1"></i>{{ $assignment->due_date->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    @if($assignment->file_path)
                                    <a href="{{ asset('storage/'.$assignment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-download me-1"></i> Unduh
                                    </a>
                                    @else
                                    <span class="text-muted"><i class="fas fa-times"></i></span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-warning rounded-circle" style="width: 34px; height: 34px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle delete-btn" style="width: 34px; height: 34px;" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.assignments.destroy', $assignment) }}"
                                            data-name="{{ $assignment->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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
                                        <p class="text-muted mb-3" style="font-size: 14px;">Belum ada tugas yang dibuat.</p>
                                        <a href="{{ route('admin.assignments.create') }}" class="btn" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff; border-radius: 12px; font-weight: 600; border: none;">
                                            <i class="fas fa-plus me-1"></i> Tambah Tugas
                                        </a>
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

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center">
            <div class="modal-body py-5">
                <div class="mb-3" style="font-size: 4rem; color: #f5576c;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="fw-bold mb-1">Hapus Tugas</h5>
                <p class="text-muted mb-0" id="deleteMessage">Yakin ingin menghapus tugas ini?</p>
            </div>
            <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnConfirmDelete" class="btn btn-danger px-4 shadow-sm">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var _deleteUrl = '';
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            _deleteUrl = btn.getAttribute('data-url');
            var name = btn.getAttribute('data-name');
            document.getElementById('deleteMessage').textContent = 'Yakin ingin menghapus "' + name + '"?';
        });
        document.getElementById('btnConfirmDelete').addEventListener('click', function () {
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = 'Menghapus...';
            var fd = new FormData();
            fd.append('_token', csrf);
            fd.append('_method', 'DELETE');
            fetch(_deleteUrl, { method: 'POST', body: fd })
                .then(function () { window.location.reload(); })
                .catch(function () { window.location.reload(); });
        });
    }
});
</script>
@endpush
