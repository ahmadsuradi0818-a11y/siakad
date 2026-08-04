@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('admin.announcements.index') }}" class="btn px-3" style="border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-edit me-2" style="color: #fa709a;"></i>Edit Pengumuman
                </h3>
                <p class="text-muted mb-0 small">Perbarui informasi pengumuman.</p>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-heading text-primary me-1"></i> Judul Pengumuman</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="Judul pengumuman..." value="{{ old('title', $announcement->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="fas fa-align-left text-secondary me-1"></i> Isi Pengumuman</label>
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="10" placeholder="Tulis isi pengumuman di sini..." required>{{ old('content', $announcement->content) }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary px-4">
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
