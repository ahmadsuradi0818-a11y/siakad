@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title m-0">Tambah Materi</h3>
    </div>
    <form action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Materi</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Kelas</label>
                <select name="class_id" class="form-control @error('class_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Guru</label>
                <select name="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>File (PDF/DOCX max 20MB)</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
