@extends('layouts.app')

@section('title', 'Edit Tugas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('admin.assignments.index') }}" class="btn px-3" style="border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-edit me-2" style="color: #fa709a;"></i>Edit Tugas
                </h3>
                <p class="text-muted mb-0 small">Perbarui informasi tugas.</p>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <form action="{{ route('admin.assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-font text-primary me-1"></i> Nama Tugas</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama tugas..." value="{{ old('name', $assignment->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-calendar-alt text-danger me-1"></i> Tenggat Waktu</label>
                            <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" required>
                            @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-users text-info me-1"></i> Kelas</label>
                            <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $assignment->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-chalkboard-teacher text-success me-1"></i> Guru</label>
                            <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $assignment->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="fas fa-book text-warning me-1"></i> Mata Pelajaran</label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $assignment->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold"><i class="fas fa-align-left text-secondary me-1"></i> Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Deskripsi tugas (opsional)">{{ old('description', $assignment->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-file-upload text-primary me-1"></i> File Tugas</label>
                            @if($assignment->file_path)
                            <div class="mb-2 p-2 rounded" style="background: #f1f5f9; font-size: 13px;">
                                <i class="fas fa-file me-1"></i> {{ basename($assignment->file_path) }}
                            </div>
                            @endif
                            <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpeg,.png,.jpg">
                            <div class="form-text text-muted small">Maks 10MB. Biarkan kosong jika tidak ingin mengganti file.</div>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn px-4" style="background: linear-gradient(135deg, #fa709a, #fee140); color: #fff; font-weight: 600; border: none; border-radius: 10px;">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
