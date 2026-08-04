@extends('layouts.app')

@section('title', 'Tahun Ajaran')

@section('content')
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
                    <i class="fas fa-calendar-alt me-2" style="color: #f59e0b;"></i>Tahun Ajaran
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $years->total() }}</strong> tahun ajaran
                </p>
            </div>
            <button type="button" class="btn" style="background: linear-gradient(135deg, #f59e0b, #f97316); color: #fff; border-radius: 12px; font-weight: 600; border: none;" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus me-1"></i> Tambah
            </button>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table mb-0" id="yearTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Tahun Ajaran</th>
                                <th>Semester</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($years as $year)
                            <tr class="align-middle" style="background: #fff; border-radius: 12px;">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-semibold" style="color: #1e293b;">{{ $year->year }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 fw-normal {{ $year->semester == 1 ? 'bg-info' : 'bg-primary' }}">
                                        Semester {{ $year->semester == 1 ? 'Ganjil' : 'Genap' }}
                                    </span>
                                </td>
                                <td><span class="text-muted"><i class="fas fa-calendar me-1"></i>{{ $year->start_date?->format('d/m/Y') ?? '-' }}</span></td>
                                <td><span class="text-muted"><i class="fas fa-calendar me-1"></i>{{ $year->end_date?->format('d/m/Y') ?? '-' }}</span></td>
                                <td class="text-center">
                                    @if($year->is_active)
                                    <span class="badge rounded-pill px-3 py-2" style="background: #d1fae5; color: #065f46;">
                                        <i class="fas fa-check-circle me-1"></i>Aktif
                                    </span>
                                    @else
                                    <span class="badge rounded-pill px-3 py-2" style="background: #f1f5f9; color: #64748b;">
                                        <i class="fas fa-circle me-1" style="color: #cbd5e1;"></i>Tidak Aktif
                                    </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        @if(!$year->is_active)
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-circle" style="width: 34px; height: 34px;" title="Aktifkan"
                                            data-bs-toggle="modal" data-bs-target="#activateModal"
                                            data-url="{{ route('admin.academic-years.set-active', $year) }}"
                                            data-name="{{ $year->year }} Semester {{ $year->semester == 1 ? 'Ganjil' : 'Genap' }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <a href="{{ route('admin.academic-years.edit', $year) }}" class="btn btn-sm btn-outline-warning rounded-circle" style="width: 34px; height: 34px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle delete-btn" style="width: 34px; height: 34px;" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.academic-years.destroy', $year) }}"
                                            data-name="{{ $year->year }} Semester {{ $year->semester == 1 ? 'Ganjil' : 'Genap' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 5rem; background: linear-gradient(135deg, #f59e0b, #f97316); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Tahun Ajaran</h5>
                                        <p class="text-muted mb-3" style="font-size: 14px;">Tambahkan tahun ajaran baru untuk memulai.</p>
                                        <button type="button" class="btn" style="background: linear-gradient(135deg, #f59e0b, #f97316); color: #fff; border-radius: 12px; font-weight: 600; border: none;" data-bs-toggle="modal" data-bs-target="#createModal">
                                            <i class="fas fa-plus me-1"></i> Tambah Tahun Ajaran
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($years->hasPages())
            <div class="card-footer bg-transparent border-top-0 pb-4 px-4">
                <div class="d-flex justify-content-center">{{ $years->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle me-2"></i>Tambah Tahun Ajaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.academic-years.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar text-primary me-1"></i> Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="year" class="form-control" placeholder="Contoh: 2025/2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-layer-group text-primary me-1"></i> Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <option value="1">Ganjil</option>
                            <option value="2">Genap</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-play text-success me-1"></i> Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-stop text-danger me-1"></i> Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #f59e0b, #f97316); color: #fff; border-radius: 10px; font-weight: 600; border: none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aktifkan -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center border-0">
            <div class="modal-body py-5">
                <div class="mb-3" style="font-size: 4rem; color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="fw-bold mb-1">Aktifkan Tahun Ajaran</h5>
                <p class="text-muted mb-0" id="activateMessage">Yakin ingin mengaktifkan tahun ajaran ini?</p>
            </div>
            <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnConfirmActivate" class="btn px-4 shadow-sm text-white" style="background: #10b981;">
                    <i class="fas fa-check me-1"></i> Ya, Aktifkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center">
            <div class="modal-body py-5">
                <div class="mb-3" style="font-size: 4rem; color: #f97316;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="fw-bold mb-1">Hapus Tahun Ajaran</h5>
                <p class="text-muted mb-0" id="deleteMessage">Yakin ingin menghapus tahun ajaran ini?</p>
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
    var _activateUrl = '';
    var _deleteUrl = '';
    var activateModal = document.getElementById('activateModal');
    if (activateModal) {
        activateModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            _activateUrl = btn.getAttribute('data-url');
            var name = btn.getAttribute('data-name');
            document.getElementById('activateMessage').textContent = 'Yakin ingin mengaktifkan "' + name + '"?';
        });
        document.getElementById('btnConfirmActivate').addEventListener('click', function () {
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = 'Mengaktifkan...';
            var fd = new FormData();
            fd.append('_token', csrf);
            fd.append('_method', 'POST');
            fetch(_activateUrl, { method: 'POST', body: fd })
                .then(function () { window.location.reload(); })
                .catch(function () { window.location.reload(); });
        });
    }

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
