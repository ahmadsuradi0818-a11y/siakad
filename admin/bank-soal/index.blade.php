@extends('layouts.app')

@section('title', 'Bank Soal')

@section('content')
<style>
.question-row {
    transition: all 0.25s ease;
    border-radius: 12px;
    background: #fff;
}
.question-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(161, 140, 209, 0.15);
}
.question-row td {
    border-top: none;
    border-bottom: 1px solid #f0f0f5;
    padding: 1rem 0.75rem;
    vertical-align: middle;
    background: transparent;
}
.question-row:last-child td { border-bottom: none; }
.question-row + .question-row td { border-top: 1px solid #f0f0f5; }
.question-avatar {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 15px; color: #fff;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}
.action-btn {
    width: 36px; height: 36px;
    border-radius: 10px !important;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    transition: all 0.2s;
    text-decoration: none;
    cursor: pointer;
}
.action-btn:hover { transform: translateY(-2px); }
.action-btn.edit:hover { background: #fef3c7; border-color: #f59e0b; color: #b45309; }
.action-btn.delete:hover { background: #fee2e2; border-color: #ef4444; color: #dc2626; }
.type-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 16px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
}
.empty-state-icon { font-size: 5rem; background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

#bankTable thead th {
    border-bottom: 2px solid #e2e8f0;
    color: #64748b;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 0.75rem;
    font-weight: 700;
    background: transparent;
}

.option-input-group {
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 8px;
}
.option-input-group .option-label {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    flex-shrink: 0;
}
.answer-radio-group {
    display: flex; gap: 12px; flex-wrap: wrap;
}
.answer-radio-group .answer-pill {
    display: flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500; font-size: 14px;
}
.answer-radio-group .answer-pill:hover { border-color: #667eea; }
.answer-radio-group .answer-pill.active { border-color: #667eea; background: #f0edff; color: #764ba2; }
.answer-radio-group .answer-pill input[type="radio"] { display: none; }
.answer-radio-group .answer-pill input[type="checkbox"] { display: none; }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                    <i class="fas fa-database me-2" style="color: #667eea;"></i>Bank Soal
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-layer-group me-1"></i>Total <strong>{{ $questions->total() }}</strong> soal tersimpan
                </p>
            </div>
            <div>
                        <div class="d-flex gap-2">
                    <button type="button" class="btn shadow-sm px-4 py-2" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-upload me-2"></i>Import
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fas fa-plus me-2"></i>Tambah ke Bank
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="input-group" style="max-width: 320px; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <span class="input-group-text bg-white border-0 pe-0">
                            <i class="fas fa-search" style="color: #94a3b8;"></i>
                        </span>
                        <input type="text" id="searchBank" class="form-control border-0 ps-2" placeholder="Cari soal..." onkeyup="filterTable()" style="box-shadow: none;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0" id="bankTable" style="border-collapse: separate; border-spacing: 0 6px;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Soal</th>
                                <th class="text-center" style="width: 130px;">Tipe</th>
                                <th class="text-center" style="width: 60px;">Poin</th>
                                <th class="text-center" style="width: 120px;">Mapel</th>
                                <th class="text-center" style="width: 130px;">Dibuat Oleh</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                            @php
                                $typeInfo = [
                                    'pilihan_ganda' => ['label' => 'Pilihan Ganda', 'icon' => 'fa-list', 'bg' => '#e0f2fe', 'txt' => '#0369a1'],
                                    'pilihan_ganda_kompleks' => ['label' => 'PG Kompleks', 'icon' => 'fa-list-alt', 'bg' => '#dbeafe', 'txt' => '#1d4ed8'],
                                    'benar_salah' => ['label' => 'Benar/Salah', 'icon' => 'fa-check-circle', 'bg' => '#d1fae5', 'txt' => '#047857'],
                                    'isian_singkat' => ['label' => 'Isian Singkat', 'icon' => 'fa-pencil-alt', 'bg' => '#fef3c7', 'txt' => '#b45309'],
                                    'essay' => ['label' => 'Essay', 'icon' => 'fa-file-alt', 'bg' => '#f1f5f9', 'txt' => '#475569'],
                                ];
                                $ti = $typeInfo[$question->type] ?? ['label' => $question->type, 'icon' => 'fa-question', 'bg' => '#f1f5f9', 'txt' => '#475569'];
                            @endphp
                            <tr class="question-row">
                                <td class="text-center text-muted fw-semibold" style="font-size: 13px;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="question-avatar">{{ $loop->iteration }}</div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1e293b; font-size: 14px; max-width: 400px;">{!! Str::limit(strip_tags($question->question_text), 80) !!}</div>
                                            @if($question->correct_answer)
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Jawaban: <span class="fw-semibold" style="color: #047857;">{{ $question->correct_answer }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="type-badge" style="background: {{ $ti['bg'] }}; color: {{ $ti['txt'] }};">
                                        <i class="fas {{ $ti['icon'] }}"></i>
                                        {{ $ti['label'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold" style="color: {{ $ti['txt'] }}; font-size: 16px;">{{ $question->points }}</span>
                                </td>
                                <td class="text-center">
                                    @if($question->subject)
                                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-weight: 500; padding: 5px 12px; border-radius: 8px;">
                                        <i class="fas fa-book me-1"></i>{{ $question->subject->name }}
                                    </span>
                                    @else
                                    <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="text-muted small">
                                        <i class="fas fa-user me-1"></i>{{ $question->createdBy->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="action-btn edit" title="Edit Soal"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="{{ $question->id }}"
                                            data-text="{{ $question->question_text }}"
                                            data-type="{{ $question->type }}"
                                            data-points="{{ $question->points }}"
                                            data-correct="{{ $question->correct_answer }}"
                                            data-options='{{ json_encode($question->options ?? []) }}'
                                            data-subject-id="{{ $question->subject_id }}">
                                            <i class="fas fa-pen" style="font-size: 13px; pointer-events: none;"></i>
                                        </button>
                                        <button type="button" class="action-btn delete btn-delete" title="Hapus Soal"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="{{ route('admin.bank-soal.destroy', $question) }}">
                                            <i class="fas fa-trash" style="font-size: 13px; pointer-events: none;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5" style="border: none;">
                                    <div class="py-4">
                                        <div class="empty-state-icon mb-3"><i class="fas fa-database"></i></div>
                                        <h5 class="fw-bold mb-1" style="color: #1e293b;">Bank Soal Masih Kosong</h5>
                                        <p class="text-muted mb-4" style="font-size: 14px;">Simpan soal yang sudah dibuat agar bisa digunakan kembali untuk ujian lain.</p>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;" data-bs-toggle="modal" data-bs-target="#createModal">
                                                <i class="fas fa-plus me-2"></i>Tambah ke Bank
                                            </button>
                                            <button type="button" class="btn shadow-sm px-4 py-2" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;" data-bs-toggle="modal" data-bs-target="#importModal">
                                                <i class="fas fa-upload me-2"></i>Import
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($questions, 'links') && $questions->hasPages())
            <div class="card-footer bg-transparent border-top-0 pb-4 px-4">
                <div class="d-flex justify-content-center">{{ $questions->links() }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Soal ke Bank</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bank-soal.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                            <i class="fas fa-font text-primary me-1"></i> Soal
                        </label>
                        <textarea name="question_text" id="bankCrText" class="form-control" rows="3" placeholder="Tulis soal..." style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                                <i class="fas fa-tag text-info me-1"></i> Tipe Soal
                            </label>
                            <select name="type" class="form-select" onchange="toggleFormFields(this, 'create')" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="pilihan_ganda_kompleks">PG Kompleks</option>
                                <option value="benar_salah">Benar/Salah</option>
                                <option value="isian_singkat">Isian Singkat</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                                <i class="fas fa-book me-1" style="color: #667eea;"></i> Mata Pelajaran
                            </label>
                            <select name="subject_id" class="form-select" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                                <option value="">Pilih Mapel</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                                <i class="fas fa-star text-warning me-1"></i> Poin
                            </label>
                            <input type="number" name="points" class="form-control" value="1" min="1" required style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                        </div>
                    </div>

                    <div class="create-options-section mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                            <i class="fas fa-list me-1" style="color: #667eea;"></i> Pilihan Jawaban
                        </label>
                        <div class="p-3" style="background: #fafafa; border-radius: 14px; border: 1px solid #f0f0f5;">
                            @php $letters = ['A', 'B', 'C', 'D', 'E']; @endphp
                            @foreach($letters as $i => $letter)
                            <div class="option-input-group">
                                <div class="option-label">{{ $letter }}</div>
                                <input type="text" name="options[]" class="form-control create-opt-input" placeholder="Pilihan {{ $letter }}" style="border-radius: 10px; border-color: #e2e8f0; padding: 8px 12px;">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                            <i class="fas fa-check-circle me-1" style="color: #10b981;"></i> Jawaban Benar
                        </label>
                        <div class="create-correct-answer-content"></div>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-3 border-0" style="background: #f8fafc;">
                    <button type="button" class="btn px-4" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-edit me-2"></i>Edit Soal Bank</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                <div class="modal-body px-4 pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                            <i class="fas fa-font text-primary me-1"></i> Soal
                        </label>
                        <textarea name="question_text" id="editText" class="form-control" rows="3" placeholder="Tulis soal..." style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;"></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                                <i class="fas fa-tag text-info me-1"></i> Tipe Soal
                            </label>
                            <select name="type" id="editType" class="form-select" onchange="toggleFormFields(this, 'edit')" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="pilihan_ganda_kompleks">PG Kompleks</option>
                                <option value="benar_salah">Benar/Salah</option>
                                <option value="isian_singkat">Isian Singkat</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                                <i class="fas fa-book me-1" style="color: #667eea;"></i> Mata Pelajaran
                            </label>
                            <select name="subject_id" id="editSubject" class="form-select" style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                                <option value="">Pilih Mapel</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                                <i class="fas fa-star text-warning me-1"></i> Poin
                            </label>
                            <input type="number" name="points" id="editPoints" class="form-control" value="1" min="1" required style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">
                        </div>
                    </div>

                    <div class="edit-options-section mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                            <i class="fas fa-list me-1" style="color: #667eea;"></i> Pilihan Jawaban
                        </label>
                        <div class="p-3" style="background: #fafafa; border-radius: 14px; border: 1px solid #f0f0f5;">
                            @php $letters = ['A', 'B', 'C', 'D', 'E']; @endphp
                            @foreach($letters as $i => $letter)
                            <div class="option-input-group">
                                <div class="option-label">{{ $letter }}</div>
                                <input type="text" name="options[]" class="form-control edit-opt-input" data-idx="{{ $i }}" placeholder="Pilihan {{ $letter }}" style="border-radius: 10px; border-color: #e2e8f0; padding: 8px 12px;">
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px; color: #475569;">
                            <i class="fas fa-check-circle me-1" style="color: #10b981;"></i> Jawaban Benar
                        </label>
                        <div class="edit-correct-answer-content"></div>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 pt-3 border-0" style="background: #f8fafc;">
                    <button type="button" class="btn px-4" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #667eea, #764ba2); border: none;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-body py-5 px-4">
                <div class="mb-3"><div style="font-size: 4rem; color: #667eea;"><i class="fas fa-exclamation-triangle"></i></div></div>
                <h5 class="fw-bold mb-1" style="color: #1e293b;">Hapus Soal dari Bank</h5>
                <p class="text-muted mb-0">Yakin ingin menghapus soal ini dari bank?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn px-4" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 12px;">
                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.bank-soal.import') }}" method="POST" enctype="multipart/form-data" class="modal-content shadow" style="border-radius: 20px; border: none; overflow: hidden;">
            @csrf
            <div class="modal-header px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-upload me-2"></i>Import Soal ke Bank</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-4">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; opacity: 0.4;"><i class="fas fa-file-excel text-primary"></i></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fas fa-file me-1"></i> File (Excel .xlsx/.xls atau Word .docx)</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.docx" required style="border-radius: 12px;">
                </div>
                <div class="alert alert-info py-2 small mb-0" style="border-radius: 12px;">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Format kolom:</strong> Tipe, Soal, Pilihan A-E, Jawaban, Poin. Baris pertama = header.
                </div>
            </div>
            <div class="modal-footer px-4 pb-4 pt-3 border-0" style="background: #f8fafc;">
                <button type="button" class="btn px-4" data-bs-dismiss="modal" style="border-radius: 12px; border: 1px solid #e2e8f0; color: #64748b;">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="submit" class="btn btn-success px-4 shadow-sm" style="border-radius: 12px;">
                    <i class="fas fa-upload me-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>
@include('partials.math-modal')
@endsection

@push('styles')
<style>
.tox-tinymce-aux { z-index: 9999 !important; }
.tox { z-index: 9999 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
<script>
var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function initBankMathEditor(editor) {
    editor.ui.registry.addButton('math', {
        text: 'Σ',
        tooltip: 'Sisipkan Rumus Matematika (LaTeX)',
        onAction: function () {
            window.activeMathEditor = editor;
            openMathOverlay();
        }
    });
}

function initBankCrTextEditor() {
    if (tinymce.get('bankCrText')) return;
    tinymce.init({
        selector: '#bankCrText',
        height: 280,
        menubar: false,
        plugins: 'advlist lists link image charmap code fullscreen table wordcount',
        toolbar: 'undo redo | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table charmap | code fullscreen | math',
        images_upload_url: '{{ route('admin.bank-soal.upload-image') }}',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', csrfToken);
                fetch('{{ route('admin.bank-soal.upload-image') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(function (response) { return response.json(); })
                .then(function (result) {
                    if (result.location) { resolve(result.location); }
                    else { reject('Gagal mengupload gambar'); }
                })
                .catch(function (error) { reject('Gagal mengupload gambar: ' + error.message); });
            });
        },
        setup: initBankMathEditor
    });
}

function initBankEditTextEditor() {
    if (tinymce.get('editText')) return;
    tinymce.init({
        selector: '#editText',
        height: 280,
        menubar: false,
        plugins: 'advlist lists link image charmap code fullscreen table wordcount',
        toolbar: 'undo redo | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table charmap | code fullscreen | math',
        images_upload_url: '{{ route('admin.bank-soal.upload-image') }}',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', csrfToken);
                fetch('{{ route('admin.bank-soal.upload-image') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(function (response) { return response.json(); })
                .then(function (result) {
                    if (result.location) { resolve(result.location); }
                    else { reject('Gagal mengupload gambar'); }
                })
                .catch(function (error) { reject('Gagal mengupload gambar: ' + error.message); });
            });
        },
        setup: initBankMathEditor
    });
}

function filterTable() {
    var input = document.getElementById('searchBank');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('bankTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var found = false;
        for (var j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) { found = true; break; }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}

function toggleFormFields(select, prefix) {
    var type = select.value;
    var opts = document.querySelector('.' + prefix + '-options-section');
    var ans = document.querySelector('.' + prefix + '-correct-answer-content');

    if (type === 'pilihan_ganda' || type === 'pilihan_ganda_kompleks') {
        opts.style.display = 'block';
        var letters = ['A', 'B', 'C', 'D', 'E'];
        var multiple = type === 'pilihan_ganda_kompleks';
        var inputType = multiple ? 'checkbox' : 'radio';
        var inputName = multiple ? 'correct_answer_pill' : 'correct_answer';
        var html = '<div class="answer-radio-group" data-multiple="' + multiple + '">';
        for (var i = 0; i < letters.length; i++) {
            html += '<label class="answer-pill">';
            html += '<input type="' + inputType + '" name="' + inputName + '" value="' + letters[i] + '">';
            html += letters[i] + '</label>';
        }
        html += '</div>';
        if (multiple) {
            html += '<input type="hidden" name="correct_answer" class="' + prefix + '-correct-hidden" value="">';
            html += '<div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Pilih satu atau lebih jawaban benar.</div>';
        }
        ans.innerHTML = html;
    } else if (type === 'benar_salah') {
        opts.style.display = 'none';
        ans.innerHTML =
            '<div class="answer-radio-group" data-multiple="false">' +
'<label class="answer-pill"><input type="radio" name="correct_answer" value="benar"> Benar</label>' +
'<label class="answer-pill"><input type="radio" name="correct_answer" value="salah"> Salah</label>' +
            '</div>';
    } else if (type === 'isian_singkat') {
        opts.style.display = 'none';
        ans.innerHTML = '<input type="text" name="correct_answer" class="form-control" placeholder="Masukkan jawaban benar..." style="border-radius: 12px; border-color: #e2e8f0; padding: 10px 14px;">';
    } else {
        opts.style.display = 'none';
        ans.innerHTML = '<div class="text-muted small py-2"><i class="fas fa-info-circle me-1"></i>Tidak perlu jawaban untuk tipe Essay.</div>';
    }
}

function handleAnswerPillClick(e) {
    var pill = e.target.closest('.answer-pill');
    if (!pill) return;
    var group = pill.closest('.answer-radio-group');
    if (!group) return;
    var multiple = group.getAttribute('data-multiple') === 'true';
    e.preventDefault();
    if (multiple) {
        pill.classList.toggle('active');
        pill.querySelector('input').checked = pill.classList.contains('active');
        var container = group.parentElement;
        var hidden = container.querySelector('input[type="hidden"]');
        if (hidden) {
            var checked = group.querySelectorAll('input[name="correct_answer_pill"]:checked');
            var vals = [];
            checked.forEach(function(c) { vals.push(c.value); });
            hidden.value = vals.join(',');
        }
    } else {
        group.querySelectorAll('.answer-pill').forEach(function(p) { p.classList.remove('active'); });
        pill.classList.add('active');
        pill.querySelector('input').checked = true;
    }
}

function restoreCorrectAnswer(prefix, correct) {
    var container = document.querySelector('.' + prefix + '-correct-answer-content');
    if (!container || !correct) return;
    var answers = correct.split(',');
    var pills = container.querySelectorAll('.answer-pill');
    pills.forEach(function(pill) {
        var input = pill.querySelector('input');
        if (answers.includes(input.value.trim())) {
            pill.classList.add('active');
            input.checked = true;
        }
    });
    var group = container.querySelector('.answer-radio-group');
    var multiple = group && group.getAttribute('data-multiple') === 'true';
    if (multiple) {
        var hidden = container.querySelector('input[type="hidden"]');
        if (hidden) hidden.value = correct;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.create-opt-input').forEach(function(inp) { inp.value = ''; });
    document.querySelector('.create-correct-answer-content').addEventListener('click', handleAnswerPillClick);
    document.querySelector('.edit-correct-answer-content').addEventListener('click', handleAnswerPillClick);
    var firstCreateSelect = document.querySelector('#createModal select[name="type"]');
    if (firstCreateSelect) toggleFormFields(firstCreateSelect, 'create');

    // Create modal - lazy init TinyMCE
    var createModal = document.getElementById('createModal');
    if (createModal) {
        createModal.addEventListener('shown.bs.modal', function () {
            if (!tinymce.get('bankCrText')) {
                initBankCrTextEditor();
            } else {
                tinymce.get('bankCrText').fire('ResizeEditor');
            }
        });
        createModal.addEventListener('hidden.bs.modal', function () {
            var editor = tinymce.get('bankCrText');
            if (editor) editor.setContent('');
        });
    }

    // Edit modal
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        var id = btn.getAttribute('data-id');
        var text = btn.getAttribute('data-text');
        var type = btn.getAttribute('data-type');
        var points = btn.getAttribute('data-points');
        var correct = btn.getAttribute('data-correct');
        var options = JSON.parse(btn.getAttribute('data-options') || '[]');

        document.getElementById('editForm').action = '{{ route("admin.bank-soal.update", "__ID__") }}'.replace('__ID__', id);
        document.getElementById('editType').value = type;
        document.getElementById('editSubject').value = btn.getAttribute('data-subject-id') || '';
        document.getElementById('editPoints').value = points;

        var editor = tinymce.get('editText');
        if (editor) {
            editor.setContent(text);
        } else {
            document.getElementById('editText').value = text;
        }

        var optInputs = document.querySelectorAll('#editModal .edit-opt-input');
        optInputs.forEach(function(inp, i) {
            inp.value = options[i] || '';
        });

        toggleFormFields(document.getElementById('editType'), 'edit');
        restoreCorrectAnswer('edit', correct);
    });
    editModal.addEventListener('shown.bs.modal', function () {
        if (!tinymce.get('editText')) {
            initBankEditTextEditor();
        } else {
            tinymce.get('editText').fire('ResizeEditor');
        }
    });
    editModal.addEventListener('hidden.bs.modal', function () {
        var editor = tinymce.get('editText');
        if (editor) editor.setContent('');
    });

    // Sync TinyMCE content on form submit
    document.querySelector('#createModal form').addEventListener('submit', function() {
        var editor = tinymce.get('bankCrText');
        if (editor) editor.save();
    });
    document.querySelector('#editModal form').addEventListener('submit', function() {
        var editor = tinymce.get('editText');
        if (editor) editor.save();
    });

    // Delete modal
    var deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            var url = btn.getAttribute('data-url');
            document.getElementById('deleteForm').action = url;
        });
    }
});
</script>
@endpush
