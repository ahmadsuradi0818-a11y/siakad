@extends('layouts.app')

@section('title', 'Detail Nilai - ' . $subject->name)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('admin.grades.index') }}" class="text-decoration-none text-muted small mb-1 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <h3 class="fw-bold mb-0" style="color: #1e293b;">
                    <i class="fas fa-calculator me-2" style="color: #6366f1;"></i>{{ $subject->name }}
                </h3>
                <p class="text-muted mb-0 small">{{ $class->name }} | {{ $selectedYear?->year }} - Semester {{ $selectedYear?->semester == 1 ? 'Ganjil' : 'Genap' }}</p>
            </div>
        </div>

        @if($grades->isEmpty())
        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body py-5 text-center">
                <p class="text-muted mb-0">Belum ada data olah nilai untuk kelas ini.</p>
            </div>
        </div>
        @else
        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
