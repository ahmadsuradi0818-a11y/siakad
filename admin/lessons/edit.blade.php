@extends('layouts.app')

@section('title', 'Edit Materi')

@section('content')
<style>
.edit-container {
    max-width: 720px;
    margin: 0 auto;
}
.form-card {
    border-radius: 20px;
    border: none;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    overflow: hidden;
}
.form-card .form-hdr {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    padding: 1.25rem 1.5rem;
}
.form-card .form-bd {
    padding: 1.75rem 1.5rem;
    background: #fff;
}
.form-card .form-ft {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e8edf3;
    display: flex; align-items: center; justify-content: flex-end;
    gap: 10px;
}
.form-label-custom {
    font-weight: 600;
    font-size: 14px;
    color: #1e293b;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-label-custom i {
    width: 20px;
    color: #f5576c;
}
.form-control-custom {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    font-size: 14px;
    transition: all 0.25s;
    background: #fff;
}
.form-control-custom:focus {
    border-color: #f5576c;
    box-shadow: 0 0 0 3px rgba(245,87,108,0.1);
}
.form-control-custom.is-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
}
select.form-control-custom {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 32px;
}
textarea.form-control-custom {
    resize: vertical;
    min-height: 100px;
}
.file-upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    transition: all 0.25s;
    cursor: pointer;
    background: #fafbfc;
}
.file-upload-area:hover {
    border-color: #f5576c;
    background: #fff5f7;
}
.file-upload-area.has-file {
    border-color: #10b981;
    background: #f0fdf4;
}
.current-file {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 10px;
    background: #f1f5f9;
    font-size: 13px;
    color: #475569;
}
.current-file i { color: #f5576c; }
.btn-gradient {
    padding: 10px 32px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 14px;
    border: none;
    background: linear-gradient(135deg, #f093fb, #f5576c);
    color: #fff;
    transition: all 0.3s;
}
.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245,87,108,0.35);
    color: #fff;
}
.btn-outline-cancel {
    padding: 10px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-outline-cancel:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #475569;
}
</style>

<div class="edit-container">
    <div class="d-flex align-items-center gap-3 mb-3">
        <a href="{{ route('admin.lessons.index') }}" class="btn px-3" style="border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0" style="color: #1e293b;">
                <i class="fas fa-book-open me-2" style="color: #f5576c;"></i>Edit Materi
            </h4>
            <small class="text-muted">Perbarui informasi materi pembelajaran</small>
        </div>
    </div>

    <div class="form-card">
        <div class="form-hdr">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.2); color: #fff; font-size: 20px;">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-white">{{ $lesson->name }}</h5>
                    <small class="text-white" style="opacity: 0.85;">Form edit materi</small>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div class="form-bd">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-custom">
                            <i class="fas fa-heading"></i> Nama Materi
                        </label>
                        <input type="text" name="name" value="{{ old('name', $lesson->name) }}"
                            class="form-control form-control-custom @error('name') is-invalid @enderror" required
                            placeholder="Masukkan nama materi">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-custom">
                            <i class="fas fa-users"></i> Kelas
                        </label>
                        <select name="class_id" class="form-control form-control-custom @error('class_id') is-invalid @enderror" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $lesson->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-custom">
                            <i class="fas fa-chalkboard-teacher"></i> Guru
                        </label>
                        <select name="teacher_id" class="form-control form-control-custom @error('teacher_id') is-invalid @enderror" required>
                            <option value="">Pilih Guru</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $lesson->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea name="description" rows="4" class="form-control form-control-custom @error('description') is-invalid @enderror" placeholder="Deskripsi materi (opsional)">{{ old('description', $lesson->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-custom">
                            <i class="fas fa-file-upload"></i> File (PDF/DOCX max 20MB)
                        </label>
                        @if($lesson->file_path)
                        <div class="mb-3">
                            <span class="current-file">
                                <i class="fas fa-file"></i> {{ $lesson->file_path }}
                            </span>
                        </div>
                        @endif
                        <div class="file-upload-area" id="fileUploadArea" onclick="document.getElementById('fileInput').click()">
                            <div class="py-2">
                                <div style="font-size: 2rem; color: #f5576c; opacity: 0.5;" class="mb-2">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <p class="fw-semibold mb-1" style="color: #1e293b;">Klik untuk upload file baru</p>
                                <p class="text-muted small mb-0">atau seret file ke sini</p>
                            </div>
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".pdf,.docx,.doc" onchange="handleFileSelect(this)">
                        </div>
                        <div id="fileName" class="mt-2 small text-muted d-none"></div>
                        @error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-ft">
                <a href="{{ route('admin.lessons.index') }}" class="btn-outline-cancel">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
                <button type="submit" class="btn-gradient">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function handleFileSelect(input) {
    var area = document.getElementById('fileUploadArea');
    var nameDisplay = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        area.classList.add('has-file');
        nameDisplay.textContent = 'File dipilih: ' + input.files[0].name;
        nameDisplay.classList.remove('d-none');
    } else {
        area.classList.remove('has-file');
        nameDisplay.classList.add('d-none');
    }
}
</script>
@endpush
