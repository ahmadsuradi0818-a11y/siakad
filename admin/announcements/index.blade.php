@extends('layouts.app')

@section('title', 'Pengumuman')

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
                    <i class="fas fa-bullhorn me-2" style="color: #fa709a;"></i>Pengumuman
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $announcements->total() ?? $announcements->count() }}</strong> pengumuman
                </p>
            </div>
            <a href="{{ route('admin.announcements.create') }}" class="btn" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff; border-radius: 12px; font-weight: 600; border: none;">
                <i class="fas fa-plus me-1"></i> Buat Pengumuman
            </a>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="input-group" style="max-width: 300px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0">
                            <i class="fas fa-search" style="color: #94a3b8;"></i>
                        </span>
                        <input type="text" id="searchPengumuman" class="form-control border-0 ps-2" placeholder="Cari pengumuman..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="announcementTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Tanggal</th>
                                <th class="text-center" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($announcements as $announcement)
                            <tr class="align-middle" style="background: #fff; border-radius: 12px;">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 15px; color: #fff; background: linear-gradient(135deg, #f093fb, #f5576c); flex-shrink: 0;">
                                            {{ strtoupper(substr($announcement->title, 0, 2)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold" style="color: #1e293b;">{{ $announcement->title }}</span>
                                            <div class="text-muted small text-truncate" style="max-width: 400px;">
                                                {{ strip_tags(Str::limit($announcement->content, 80)) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #dbeafe; color: #1d4ed8;"><i class="fas fa-user me-1"></i>{{ $announcement->author->name }}</span></td>
                                <td><span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #f1f5f9; color: #475569;"><i class="fas fa-calendar me-1"></i>{{ $announcement->created_at->format('d/m/Y H:i') }}</span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-warning rounded-circle" style="width: 34px; height: 34px;" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle delete-btn" style="width: 34px; height: 34px;" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.announcements.destroy', $announcement) }}"
                                            data-title="{{ $announcement->title }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div style="font-size: 5rem; background: linear-gradient(135deg, #f093fb, #f5576c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                                            <i class="fas fa-bullhorn"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Pengumuman</h5>
                                        <p class="text-muted mb-3" style="font-size: 14px;">Belum ada pengumuman yang dibuat.</p>
                                        <a href="{{ route('admin.announcements.create') }}" class="btn" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff; border-radius: 12px; font-weight: 600; border: none;">
                                            <i class="fas fa-plus me-1"></i> Buat Pengumuman
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($announcements->hasPages())
            <div class="card-footer bg-transparent border-top-0 pb-4 px-4">
                <div class="d-flex justify-content-center">{{ $announcements->links() }}</div>
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
                <h5 class="fw-bold mb-1">Hapus Pengumuman</h5>
                <p class="text-muted mb-0" id="deleteMessage">Yakin ingin menghapus pengumuman ini?</p>
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
function filterTable() {
    var input = document.getElementById('searchPengumuman');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('announcementTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var found = false;
        for (var j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) { found = true; break; }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}
document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var _deleteUrl = '';
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            _deleteUrl = btn.getAttribute('data-url');
            var title = btn.getAttribute('data-title');
            document.getElementById('deleteMessage').textContent = 'Yakin ingin menghapus "' + title + '"?';
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
