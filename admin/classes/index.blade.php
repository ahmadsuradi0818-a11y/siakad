@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex align-items-stretch">
        <div class="card card-primary card-outline w-100">
            <div class="card-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h3 class="card-title text-white m-0">
                    <i class="fas fa-graduation-cap me-2"></i> Data Kelas
                </h3>
                <button type="button" class="btn btn-light btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="fas fa-plus me-1"></i> Tambah Kelas
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group input-group-sm" style="max-width: 300px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" id="searchKelas" class="form-control border-start-0 ps-0" placeholder="Cari kelas..." onkeyup="filterTable()">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <span class="text-muted small">
                            <i class="fas fa-layer-group me-1"></i> Total: <strong>{{ $classes->total() ?? $classes->count() }}</strong> kelas
                        </span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="kelasTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th>Nama Kelas</th>
                                <th>Wali Kelas</th>
                                <th class="text-center">Jumlah Siswa</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $class)
                            <tr class="align-middle">
                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: linear-gradient(135deg, #667eea22, #764ba222); color: #667eea; font-weight: 700; font-size: 14px;">
                                                {{ substr($class->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-semibold">{{ $class->name }}</span>
                                            <div class="text-muted small">Kelas</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($class->homeroomTeacher)
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white me-2" style="width: 34px; height: 34px; font-size: 13px; font-weight: 600;">
                                                {{ substr($class->homeroomTeacher->name, 0, 1) }}
                                            </div>
                                            <span>{{ $class->homeroomTeacher->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-2 fw-normal">
                                        <i class="fas fa-user-graduate me-1"></i> {{ $class->students->count() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-warning rounded-circle"
                                            style="width: 34px; height: 34px;"
                                            title="Edit Kelas"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-url="{{ route('admin.classes.update', $class) }}"
                                            data-name="{{ $class->name }}"
                                            data-teacher="{{ $class->homeroom_teacher_id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-circle btn-delete"
                                            style="width: 34px; height: 34px;"
                                            title="Hapus Kelas"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.classes.destroy', $class) }}"
                                            data-name="{{ $class->name }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5">
                                        <div class="mb-3" style="font-size: 4rem; opacity: 0.3;">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <h5 class="text-muted mb-1">Belum Ada Data Kelas</h5>
                                        <p class="text-muted small mb-3">Mulai dengan menambahkan kelas baru.</p>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                                            <i class="fas fa-plus me-1"></i> Tambah Kelas
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($classes, 'links') && $classes->hasPages())
            <div class="card-footer border-top-0 bg-transparent">
                <div class="d-flex justify-content-center">
                    {{ $classes->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.classes.store') }}" method="POST" class="modal-content shadow">
            @csrf
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white"><i class="fas fa-plus-circle me-2"></i>Tambah Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-school text-primary me-1"></i> Nama Kelas
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-graduation-cap text-muted"></i>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control"
                            placeholder="Contoh: XII RPL"
                            required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chalkboard-teacher text-success me-1"></i> Wali Kelas
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user-tie text-muted"></i>
                        </span>
                        <select name="homeroom_teacher_id" class="form-select">
                            <option value="">— Pilih Guru —</option>
                            @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>Edit Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-school text-primary me-1"></i> Nama Kelas
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-graduation-cap text-muted"></i>
                        </span>
                        <input type="text" id="editName" class="form-control" placeholder="Contoh: XII RPL" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chalkboard-teacher text-success me-1"></i> Wali Kelas
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-user-tie text-muted"></i>
                        </span>
                        <select id="editTeacher" class="form-select">
                            <option value="">— Pilih Guru —</option>
                            @foreach($guruList as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnSaveEdit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Kelas -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center">
            <div class="modal-body py-5">
                <div class="mb-3" style="font-size: 4rem; color: #f5576c;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="fw-bold mb-1">Hapus Kelas</h5>
                <p class="text-muted mb-0" id="deleteMessage">Yakin ingin menghapus kelas ini?</p>
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
<style>
.btn-edit i { pointer-events: none; }
.btn-delete i { pointer-events: none; }
</style>
<script>
function filterTable() {
    var input = document.getElementById('searchKelas');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('kelasTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var found = false;
        for (var j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var editModalEl = document.getElementById('editModal');
    var _editUrl = '';

    editModalEl.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        _editUrl = btn.getAttribute('data-url');
        document.getElementById('editName').value = btn.getAttribute('data-name');
        document.getElementById('editTeacher').value = btn.getAttribute('data-teacher') || '';
    });

    document.getElementById('btnSaveEdit').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
        var formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('_method', 'PUT');
        formData.append('name', document.getElementById('editName').value);
        formData.append('homeroom_teacher_id', document.getElementById('editTeacher').value || '');
        fetch(_editUrl, { method: 'POST', body: formData })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });

    var deleteModalEl = document.getElementById('deleteModal');
    var _deleteUrl = '';

    deleteModalEl.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        _deleteUrl = btn.getAttribute('data-url');
        document.getElementById('deleteMessage').textContent = 'Yakin ingin menghapus kelas ' + btn.getAttribute('data-name') + '? Semua data terkait akan ikut terhapus.';
    });

    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Menghapus...';
        var formData = new FormData();
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        formData.append('_method', 'DELETE');
        fetch(_deleteUrl, { method: 'POST', body: formData })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });
});
</script>
@endpush
