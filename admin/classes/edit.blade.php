@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h3 class="card-title text-white m-0">
                    <i class="fas fa-edit me-2"></i> Edit Kelas
                </h3>
                <div class="ms-auto">
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-light btn-sm shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.classes.update', $class) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-school text-primary me-1"></i> Nama Kelas
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-graduation-cap text-muted"></i>
                            </span>
                            <input type="text" name="name" value="{{ old('name', $class->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: XII RPL, XI AKL 1, X-1"
                                required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Masukkan nama kelas, misal: <strong>XII RPL</strong>, <strong>XI AKL 1</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-chalkboard-teacher text-success me-1"></i> Wali Kelas
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-user-tie text-muted"></i>
                            </span>
                            <select name="homeroom_teacher_id" class="form-select @error('homeroom_teacher_id') is-invalid @enderror">
                                <option value="">— Pilih Guru —</option>
                                @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}" {{ old('homeroom_teacher_id', $class->homeroom_teacher_id) == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('homeroom_teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Pilih guru yang akan menjadi wali kelas (opsional)
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
