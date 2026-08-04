@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title m-0">Tambah Ujian</h3>
    </div>
    <form action="{{ route('admin.exams.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Nama Ujian</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Kelas <small class="text-muted">(bisa pilih lebih dari satu)</small></label>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px;">
                    @foreach($classes as $class)
                    <label class="d-flex align-items-center gap-2 py-1" style="cursor: pointer;">
                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" {{ in_array($class->id, old('class_ids', [])) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #667eea;">
                        <span>{{ $class->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('class_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @error('class_ids.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Mata Pelajaran</label>
                <select name="subject_id" class="form-control @error('subject_id') is-invalid @enderror">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Guru Pengawas</label>
                <select name="teacher_id" class="form-control @error('teacher_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="date" value="{{ old('date') }}" class="form-control @error('date') is-invalid @enderror" required>
                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" class="form-control @error('start_time') is-invalid @enderror">
                        @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" class="form-control @error('end_time') is-invalid @enderror">
                        @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Tipe Ujian</label>
                <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="uh" {{ old('type') == 'uh' ? 'selected' : '' }}>Ulangan Harian</option>
                    <option value="uts" {{ old('type') == 'uts' ? 'selected' : '' }}>UTS</option>
                    <option value="uas" {{ old('type') == 'uas' ? 'selected' : '' }}>UAS</option>
                    <option value="pat" {{ old('type') == 'pat' ? 'selected' : '' }}>PAT</option>
                    <option value="tryout" {{ old('type') == 'tryout' ? 'selected' : '' }}>Try Out</option>
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
