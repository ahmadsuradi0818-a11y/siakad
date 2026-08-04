@extends('layouts.app')

@section('title', 'Koreksi: ' . $exam->name)

@section('content')
<style>
.question-card {
    border-radius: 16px;
    border: 1px solid #e8edf3;
    background: #fff;
    transition: all 0.2s;
    overflow: hidden;
}
.question-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.question-card .card-hdr {
    padding: 0.9rem 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e8edf3;
    display: flex; align-items: center; justify-content: space-between;
}
.question-card .card-bd { padding: 1.25rem; }
.opt-item {
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    margin-bottom: 6px;
    display: flex; align-items: center; gap: 10px;
    transition: all 0.15s;
}
.opt-item.is-correct { border-color: #10b981; background: #f0fdf4; }
.opt-item.is-wrong { border-color: #f87171; background: #fef2f2; }
.opt-item.is-student { border-color: #6366f1; background: #eef2ff; }
.opt-item .opt-letter {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 12px;
    background: #f1f5f9; color: #475569;
    flex-shrink: 0;
}
.opt-item.is-correct .opt-letter { background: #10b981; color: #fff; }
.opt-item.is-wrong .opt-letter { background: #f87171; color: #fff; }
.opt-item.is-student .opt-letter { background: #6366f1; color: #fff; }
.score-input-group {
    display: inline-flex; align-items: center; gap: 8px;
    background: #f8fafc;
    padding: 4px 12px 4px 4px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}
.score-input-group input {
    width: 80px;
    border: none;
    background: transparent;
    text-align: center;
    font-weight: 700;
    font-size: 16px;
    padding: 6px 0;
    outline: none;
}
.score-input-group input:focus { box-shadow: none; }
.score-input-group .max-label {
    font-size: 13px;
    color: #94a3b8;
    font-weight: 600;
}
.submit-btn {
    padding: 12px 48px;
    border-radius: 14px;
    font-weight: 700; font-size: 16px;
    border: none;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: #fff;
    transition: all 0.3s;
}
.submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(99,102,241,0.35); }
</style>

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
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('admin.exams.nilai-detail', $exam) }}" class="btn px-3" style="border-radius: 10px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">
                        <i class="fas fa-check-double me-2" style="color: #6366f1;"></i>Koreksi: {{ $exam->name }}
                    </h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1" style="background: #e0f2fe; color: #0369a1;">
                            <i class="fas fa-user me-1"></i>{{ $student->user->name }}
                        </span>
                        <span class="badge rounded-pill px-3 py-1" style="background: #f1f5f9; color: #475569;">
                            <i class="fas fa-id-card me-1"></i>{{ $student->nis }}
                        </span>
                        <span class="badge rounded-pill px-3 py-1" style="background: #fef3c7; color: #b45309;">
                            <i class="fas fa-list me-1"></i>{{ $questions->count() }} Soal
                        </span>
                    </div>
                </div>
            </div>
            @if($result && $result->score !== null)
            <div class="text-end">
                <div class="text-muted small fw-semibold">Nilai Saat Ini</div>
                <div class="fw-bold" style="font-size: 32px; color: #10b981;">{{ $result->score }}</div>
            </div>
            @else
            <div class="text-end">
                <div class="text-muted small fw-semibold">Status</div>
                <span class="badge rounded-pill px-3 py-1" style="background: #fef3c7; color: #b45309; font-size: 14px;">
                    <i class="fas fa-clock me-1"></i>Belum Dinilai
                </span>
            </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.exams.store-koreksi', [$exam, $student]) }}">
            @csrf
            @php
                $typeName = ['pilihan_ganda' => 'Pilihan Ganda', 'pilihan_ganda_kompleks' => 'PG Kompleks', 'benar_salah' => 'Benar/Salah', 'isian_singkat' => 'Isian Singkat', 'essay' => 'Essay'];
                $typeColors = ['pilihan_ganda' => '#3b82f6', 'pilihan_ganda_kompleks' => '#8b5cf6', 'benar_salah' => '#10b981', 'isian_singkat' => '#f59e0b', 'essay' => '#f97316'];
            @endphp

            @foreach($questions as $index => $question)
            @php
                $answer = $answers->get($question->id);
                $autoGraded = in_array($question->type, ['pilihan_ganda', 'benar_salah', 'pilihan_ganda_kompleks']);
                $tc = $typeColors[$question->type] ?? '#64748b';
            @endphp
            <div class="question-card mb-3">
                <div class="card-hdr">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 10px; background: #6366f1; color: #fff; font-weight: 700; font-size: 13px;">{{ $index + 1 }}</span>
                        <span class="fw-semibold mathjax" style="color: #1e293b;">{!! $question->question_text !!}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-2 py-1" style="background: {{ $tc }}15; color: {{ $tc }}; font-size: 11px;">{{ $typeName[$question->type] ?? $question->type }}</span>
                        <span class="badge rounded-pill px-2 py-1" style="background: #e0e7ff; color: #4338ca; font-size: 11px;">
                            <i class="fas fa-star me-1"></i>{{ $question->points }} poin
                        </span>
                        @if($autoGraded)
                        <span class="badge rounded-pill px-2 py-1" style="background: #d1fae5; color: #047857; font-size: 11px;">
                            <i class="fas fa-robot me-1"></i>Otomatis
                        </span>
                        @else
                        <span class="badge rounded-pill px-2 py-1" style="background: #fef3c7; color: #b45309; font-size: 11px;">
                            <i class="fas fa-pen me-1"></i>Manual
                        </span>
                        @endif
                    </div>
                </div>
                <div class="card-bd">
                    @if($question->type === 'pilihan_ganda' && $question->options)
                        @foreach($question->options as $oi => $opt)
                            @php
                                $isStudent = $answer && $answer->answer == $opt;
                                $isCorrect = $question->correct_answer == $opt;
                                $cls = $isStudent && $isCorrect ? 'is-correct' : ($isStudent && !$isCorrect ? 'is-wrong' : '');
                                $letters = range('A', 'E');
                            @endphp
                            <div class="opt-item {{ $cls }}">
                                <span class="opt-letter">{{ $letters[$oi] ?? $oi }}</span>
                                <span class="fw-medium" style="color: #334155;">{{ $opt }}</span>
                                @if($isCorrect)
                                <span class="ms-auto" style="color: #10b981;"><i class="fas fa-check-circle"></i></span>
                                @endif
                                @if($isStudent)
                                <span class="badge rounded-pill" style="background: #6366f1; color: #fff; font-size: 10px;">JAWABAN</span>
                                @endif
                            </div>
                        @endforeach
                    @elseif($question->type === 'benar_salah')
                        @foreach(['Benar', 'Salah'] as $opt)
                            @php
                                $isStudent = $answer && $answer->answer == $opt;
                                $isCorrect = $question->correct_answer == $opt;
                                $cls = $isStudent && $isCorrect ? 'is-correct' : ($isStudent && !$isCorrect ? 'is-wrong' : '');
                            @endphp
                            <div class="opt-item {{ $cls }}">
                                <span class="opt-letter"><i class="fas {{ $opt == 'Benar' ? 'fa-check' : 'fa-times' }}"></i></span>
                                <span class="fw-medium" style="color: #334155;">{{ $opt }}</span>
                                @if($isCorrect)
                                <span class="ms-auto" style="color: #10b981;"><i class="fas fa-check-circle"></i></span>
                                @endif
                                @if($isStudent)
                                <span class="badge rounded-pill" style="background: #6366f1; color: #fff; font-size: 10px;">JAWABAN</span>
                                @endif
                            </div>
                        @endforeach
                    @elseif($question->type === 'pilihan_ganda_kompleks' && $question->options)
                        @php
                            $studentAnswers = $answer ? (json_decode($answer->answer, true) ?: []) : [];
                            $correctAnswers = json_decode($question->correct_answer, true) ?: explode(',', $question->correct_answer);
                            $letters = range('A', 'E');
                        @endphp
                        @foreach($question->options as $oi => $opt)
                            @php
                                $isStudent = in_array($opt, $studentAnswers);
                                $isCorrect = in_array($opt, $correctAnswers);
                                $cls = $isStudent && $isCorrect ? 'is-correct' : ($isStudent && !$isCorrect ? 'is-wrong' : '');
                            @endphp
                            <div class="opt-item {{ $cls }}">
                                <span class="opt-letter">{{ $letters[$oi] ?? $oi }}</span>
                                <span class="fw-medium" style="color: #334155;">{{ $opt }}</span>
                                @if($isCorrect && !$isStudent)
                                <span class="ms-auto" style="color: #94a3b8;"><i class="fas fa-check-circle" style="opacity: 0.4;"></i></span>
                                @endif
                                @if($isStudent)
                                <span class="badge rounded-pill" style="background: #6366f1; color: #fff; font-size: 10px;">JAWABAN</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="mb-3">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span class="d-flex align-items-center" style="color: #6366f1;"><i class="fas fa-reply"></i></span>
                                <div class="flex-grow-1">
                                    <small class="text-muted fw-semibold">Jawaban Siswa:</small>
                                    <p class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #1e293b; font-size: 15px;">{{ $answer->answer ?? '(tidak dijawab)' }}</p>
                                </div>
                            </div>
                            @if($question->type === 'isian_singkat')
                            <div class="d-flex align-items-start gap-2">
                                <span class="d-flex align-items-center" style="color: #10b981;"><i class="fas fa-check-circle"></i></span>
                                <div>
                                    <small class="text-muted fw-semibold">Jawaban Benar:</small>
                                    <p class="fw-bold" style="color: #10b981; font-size: 15px;">{{ $question->correct_answer }}</p>
                                </div>
                            </div>
                            @endif
                            <div class="mt-3">
                                <label class="fw-semibold d-block mb-1" style="color: #1e293b; font-size: 14px;">
                                    <i class="fas fa-star me-1" style="color: #f59e0b;"></i>Berikan Nilai
                                </label>
                                <div class="score-input-group">
                                    <input type="number" name="points_{{ $question->id }}"
                                        value="{{ $answer->points_earned ?? 0 }}"
                                        min="0" max="{{ $question->points }}" step="0.01"
                                        oninput="updatePoints(this, {{ $question->points }})">
                                    <span class="max-label">/ {{ $question->points }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($autoGraded && $answer)
                        @if($answer->is_correct !== null)
                        <div class="mt-2 pt-2" style="border-top: 1px solid #e8edf3;">
                            <small>
                                @if($answer->is_correct)
                                <span class="fw-semibold" style="color: #10b981;"><i class="fas fa-check-circle me-1"></i>Auto-grade: Benar ({{ $answer->points_earned ?? $question->points }}/{{ $question->points }} poin)</span>
                                @else
                                <span class="fw-semibold" style="color: #ef4444;"><i class="fas fa-times-circle me-1"></i>Auto-grade: Salah (0/{{ $question->points }} poin)</span>
                                @endif
                            </small>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach

            <div class="text-center my-4">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save me-2"></i> Simpan Koreksi & Nilai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePoints(input, max) {
    if (parseFloat(input.value) > max) input.value = max;
    if (parseFloat(input.value) < 0) input.value = 0;
}
</script>
@endpush
