@extends('layouts.app')

@section('title', 'Olah Nilai')

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
                    <i class="fas fa-calculator me-2" style="color: #6366f1;"></i>Olah Nilai
                </h3>
                <p class="text-muted mb-0 small">Lihat data olah nilai seluruh guru</p>
            </div>
        </div>

        <div class="card shadow-sm mb-4" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-school text-primary me-1"></i> Kelas</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><i class="fas fa-book text-primary me-1"></i> Mata Pelajaran</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar-alt text-primary me-1"></i> Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->year }} - Semester {{ $year->semester == 1 ? 'Ganjil' : 'Genap' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border-radius: 12px; font-weight: 600; border: none;">
                            <i class="fas fa-search me-1"></i> Lihat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($grades->isNotEmpty())
        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-header bg-transparent pt-4 px-4" style="border: none;">
                <h5 class="fw-bold mb-0" style="color: #1e293b;">
                    {{ $selectedClass?->name }} - {{ $selectedSubject?->name }}
                </h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table class="table table-bordered" style="font-size: 13px;">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th style="width: 35px;">No</th>
                                <th class="text-start">Nama Siswa</th>
                                <th>NIS</th>
                                <th>Absensi</th>
                                <th>Tugas</th>
                                <th>UH</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Nilai Akhir</th>
                                <th style="width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $g)
                            <tr class="align-middle">
                                <td class="text-center fw-semibold">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $g->student->user->name }}</td>
                                <td class="text-center">{{ $g->student->nis }}</td>
                                <td class="text-center">{{ $g->attendance_score ?? '-' }}</td>
                                <td class="text-center">{{ $g->assignment_score ?? '-' }}</td>
                                <td class="text-center">{{ $g->daily_test_avg ?? '-' }}</td>
                                <td class="text-center">{{ $g->midterm_score ?? '-' }}</td>
                                <td class="text-center">{{ $g->final_score ?? '-' }}</td>
                                <td class="text-center fw-bold" style="color: #4f46e5;">{{ $g->final_grade ?? '-' }}</td>
                                <td class="text-center">
                                    <form method="POST" action="{{ route('admin.grades.destroy', $g->id) }}" onsubmit="return confirm('Hapus data nilai ini?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif(request()->has('class_id'))
        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body py-5 text-center">
                <div style="font-size: 5rem; background: linear-gradient(135deg, #6366f1, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;" class="mb-3">
                    <i class="fas fa-database"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color: #1e293b;">Belum Ada Data</h5>
                <p class="text-muted mb-0" style="font-size: 14px;">Guru belum mengolah nilai untuk kelas dan mata pelajaran ini.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
