@extends('layouts.app')

@section('title', 'Edit Tahun Ajaran')

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
                    <i class="fas fa-edit me-2" style="color: #f59e0b;"></i>Edit Tahun Ajaran
                </h3>
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-calendar text-primary me-1"></i> Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $academicYear->year) }}" required>
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-layer-group text-primary me-1"></i> Semester <span class="text-danger">*</span></label>
                            <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                <option value="1" {{ old('semester', $academicYear->semester) == 1 ? 'selected' : '' }}>Ganjil</option>
                                <option value="2" {{ old('semester', $academicYear->semester) == 2 ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">
                                    <i class="fas fa-check-circle text-success me-1"></i> Aktifkan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-play text-success me-1"></i> Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-stop text-danger me-1"></i> Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary me-2" style="border-radius: 12px;">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #f59e0b, #f97316); color: #fff; border-radius: 12px; font-weight: 600; border: none; padding: 10px 32px;">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
