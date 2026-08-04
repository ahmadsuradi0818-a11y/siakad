@extends('layouts.app')

@section('title', 'Edit Ujian')

@section('content')
<style>
.form-input {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    transition: all 0.2s;
    box-shadow: none;
}
.form-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.form-label-custom {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    margin-bottom: 6px;
}
.form-select-custom {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    transition: all 0.2s;
    box-shadow: none;
}
.form-select-custom:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
.back-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 18px;
    border-radius: 10px;
    font-weight: 600; font-size: 13px;
    border: 1px solid #e2e8f0;
    color: #64748b;
    background: #fff;
    transition: all 0.2s;
    text-decoration: none;
}
.back-btn:hover { background: #f8fafc; border-color: #94a3b8; color: #475569; }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-pen me-2" style="color: #667eea;"></i>Edit Ujian
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-file-alt me-1"></i>{{ $exam->name }}
                </p>
            </div>
            <a href="{{ route('admin.exams.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="card-header px-4 py-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff;">
                        <i class="fas fa-pen"></i>
                    </div>
                    <div>
                        <h5 class="text-white fw-bold mb-0">Form Edit Ujian</h5>
                        <small class="text-white opacity-75">Perbarui data ujian {{ $exam->name }}</small>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                @csrf @method('PUT')
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label-custom"><i class="fas fa-font text-primary me-1"></i> Nama Ujian</label>
                        <input type="text" name="name" value="{{ old('name', $exam->name) }}" class="form-control form-input @error('name') is-invalid @enderror" placeholder="Cth: Ulangan Harian 1" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom"><i class="fas fa-book text-info me-1"></i> Mata Pelajaran</label>
                            <select name="subject_id" class="form-select form-select-custom @error('subject_id') is-invalid @enderror">
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom"><i class="fas fa-users text-success me-1"></i> Kelas <small class="text-muted fw-normal text-lowercase">(bisa pilih lebih dari satu)</small></label>
                            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 14px;">
                                @php $examClassIds = $exam->classes->pluck('id')->toArray(); @endphp
                                @foreach($classes as $class)
                                <label class="d-flex align-items-center gap-2 py-1" style="cursor: pointer;">
                                    <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" {{ in_array($class->id, old('class_ids', $examClassIds)) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #667eea;">
                                    <span style="font-size: 14px;">{{ $class->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            @error('class_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @error('class_ids.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom"><i class="fas fa-chalkboard-teacher text-warning me-1"></i> Guru Pengawas</label>
                            <select name="teacher_id" class="form-select form-select-custom @error('teacher_id') is-invalid @enderror" required>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id', $exam->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom"><i class="fas fa-tag text-secondary me-1"></i> Tipe Ujian</label>
                            <select name="type" class="form-select form-select-custom @error('type') is-invalid @enderror" required>
                                <option value="uh" {{ old('type', $exam->type) == 'uh' ? 'selected' : '' }}>Ulangan Harian</option>
                                <option value="uts" {{ old('type', $exam->type) == 'uts' ? 'selected' : '' }}>UTS</option>
                                <option value="uas" {{ old('type', $exam->type) == 'uas' ? 'selected' : '' }}>UAS</option>
                                <option value="pat" {{ old('type', $exam->type) == 'pat' ? 'selected' : '' }}>PAT</option>
                                <option value="tryout" {{ old('type', $exam->type) == 'tryout' ? 'selected' : '' }}>Try Out</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="fas fa-calendar-alt text-danger me-1"></i> Tanggal</label>
                        <input type="date" name="date" value="{{ old('date', $exam->date->format('Y-m-d')) }}" class="form-control form-input @error('date') is-invalid @enderror" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label-custom"><i class="fas fa-play-circle text-success me-1"></i> Jam Mulai</label>
                            <input type="time" name="start_time" value="{{ old('start_time', $exam->start_time) }}" class="form-control form-input @error('start_time') is-invalid @enderror">
                            @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom"><i class="fas fa-stop-circle text-danger me-1"></i> Jam Selesai</label>
                            <input type="time" name="end_time" value="{{ old('end_time', $exam->end_time) }}" class="form-control form-input @error('end_time') is-invalid @enderror">
                            @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color: #f0f0f5 !important;">
                        <a href="{{ route('admin.exams.index') }}" class="btn px-4" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
