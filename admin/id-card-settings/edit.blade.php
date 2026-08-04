@extends('layouts.app')

@section('title', 'Pengaturan Template Kartu ID')

@php
$elementLabels = [
    'logo' => 'Logo Sekolah',
    'school_name' => 'Nama Sekolah',
    'npsn' => 'NPSN',
    'photo' => 'Foto Siswa',
    'student_name' => 'Nama Siswa',
    'nis_nisn' => 'NIS / NISN',
    'class' => 'Kelas',
    'gender' => 'Jenis Kelamin',
    'birthplace' => 'Tempat & Tgl Lahir',
    'religion' => 'Agama',
    'address' => 'Alamat',
    'headmaster' => 'Kepala Sekolah',
    'validity' => 'Masa Berlaku',
    'custom_sidebar_text' => 'Teks Samping',
    'custom_footer_text' => 'Teks Footer',
];
$positions = $setting->element_positions ?? [];
@endphp

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
                    <i class="fas fa-palette me-2" style="color: #6366f1;"></i>Pengaturan Template Kartu ID
                </h3>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>Sesuaikan tampilan kartu ID siswa sesuai keinginan
                </p>
            </div>
            <div>
                <a href="{{ route('admin.students.id-card') }}" class="btn btn-outline-primary rounded-pill px-3 shadow-sm">
                    <i class="fas fa-id-card me-1"></i> Kelola Kartu ID
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.id-card-settings.update') }}" id="settingsForm" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                {{-- Preview --}}
                <div class="col-lg-5 order-lg-2">
                    <div class="card shadow-sm sticky-top" style="border-radius: 20px; border: none; top: 20px;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-eye me-2" style="color: #6366f1;"></i>Pratinjau Kartu</h5>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div id="cardPreview" style="margin: 0 auto; display: inline-block;">
                                <div id="previewCard"
                                     style="width: {{ $setting->card_width }}mm; height: {{ $setting->card_height }}mm; background: {{ $setting->card_bg_color }}; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.1); border: 1px solid {{ $setting->border_color }}; font-family: {{ $setting->font_family }}; text-align: left; transition: all 0.3s; position: relative;">
                                    <div id="previewHeader"
                                         style="background: linear-gradient(135deg, {{ $setting->header_bg_start }}, {{ $setting->header_bg_end }}); color: {{ $setting->header_text_color }}; padding: 5px 10px; display: flex; align-items: center; gap: 8px; height: 28px; position: relative; z-index: 2;">
                                        <div id="previewLogo" style="width: 22px; height: 22px; border-radius: 50%; background: #ffffff30; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: {{ $setting->header_text_color }}; font-size: 12px;">🏫</div>
                                        <div id="previewSchoolName" style="font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">NAMA SEKOLAH</div>
                                        <div id="previewNpsn" style="font-size: 6px; opacity: 0.8; margin-left: auto; flex-shrink: 0;">NPSN: 123456</div>
                                    </div>
                                    <div id="previewBody" style="flex: 1; display: flex; padding: 6px 10px; gap: 8px; position: relative; z-index: 2;">
                                        <div id="previewPhoto" style="width: 48px; height: 48px; border-radius: 50%; flex-shrink: 0; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 22px;">A</div>
                                        <div style="flex: 1; display: flex; flex-direction: column; gap: 1px; min-width: 0;">
                                            <div id="previewStudentName" style="font-size: {{ $setting->font_size_name }}px; font-weight: 700; color: #1e293b; line-height: 1.2; text-transform: uppercase;">NAMA SISWA</div>
                                            <div id="previewNis" style="font-size: 7px; color: #64748b;">NIS: <strong style="color: {{ $setting->accent_color }};">1234567890</strong></div>
                                            <div id="previewDetails" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1px 6px; margin-top: 2px;">
                                                <div id="previewClass" style="font-size: {{ $setting->font_size_detail }}px; color: #475569; line-height: 1.3;">
                                                    <span style="color: #94a3b8; display: block; font-size: 5.5px; text-transform: uppercase; letter-spacing: 0.3px;">Kelas</span>
                                                    XII RPL 1
                                                </div>
                                                <div id="previewGender" style="font-size: {{ $setting->font_size_detail }}px; color: #475569; line-height: 1.3;">
                                                    <span style="color: #94a3b8; display: block; font-size: 5.5px; text-transform: uppercase; letter-spacing: 0.3px;">JK</span>
                                                    Laki-laki
                                                </div>
                                                <div id="previewBirthplace" style="font-size: {{ $setting->font_size_detail }}px; color: #475569; line-height: 1.3;">
                                                    <span style="color: #94a3b8; display: block; font-size: 5.5px; text-transform: uppercase; letter-spacing: 0.3px;">Ttl</span>
                                                    Jakarta, 01/01/2000
                                                </div>
                                                <div id="previewReligion" style="font-size: {{ $setting->font_size_detail }}px; color: #475569; line-height: 1.3;">
                                                    <span style="color: #94a3b8; display: block; font-size: 5.5px; text-transform: uppercase; letter-spacing: 0.3px;">Agama</span>
                                                    Islam
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="previewFooter" style="background: #f8fafc; border-top: 1px solid {{ $setting->border_color }}; padding: 3px 10px; display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 2;">
                                        <div id="previewValidity" style="font-size: 5.5px; color: #94a3b8;">Berlaku: 2023/2026</div>
                                        <div id="previewHeadmaster" style="font-size: 6px; color: #1e293b; text-align: right; line-height: 1.2;">
                                            <div style="font-weight: 600;">Kepala Sekolah</div>
                                            <div style="font-size: 5px; color: #94a3b8;">NIP. 123456</div>
                                        </div>
                                    </div>
                                    @if($setting->background_template)
                                    <div id="previewBgOverlay" style="position: absolute; inset: 0; z-index: 1; pointer-events: none;">
                                        <img src="{{ asset('storage/' . $setting->background_template) }}" style="width: 100%; height: 100%; object-fit: fill; opacity: 1;">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted small mt-3 mb-0"><i class="fas fa-sync-alt me-1"></i> Pratinjau akan berubah saat Anda mengubah pengaturan</p>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="col-lg-7 order-lg-1">
                    {{-- Background Template --}}
                    <div class="card shadow-sm" style="border-radius: 20px; border: none;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-image me-2" style="color: #6366f1;"></i>Background Template</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Upload Background Kartu</label>
                                    <input type="file" name="background_template" class="form-control" accept="image/png,image/jpeg" onchange="previewBgFile(this)">
                                    <div class="form-text">Format: PNG/JPG, maks 2MB. Biarkan kosong jika tidak ingin menggunakan background.</div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    @if($setting->background_template)
                                    <div class="d-flex gap-2 w-100">
                                        <img src="{{ asset('storage/' . $setting->background_template) }}" style="width: 60px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #e0e0e0;">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteBg()">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Layout Mode --}}
                    <div class="card shadow-sm mt-4" style="border-radius: 20px; border: none;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-layer-group me-2" style="color: #6366f1;"></i>Mode Layout</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="layout_mode" value="auto" id="layoutAuto" {{ $setting->layout_mode === 'auto' ? 'checked' : '' }} onchange="toggleLayoutMode()">
                                    <label class="form-check-label fw-semibold" for="layoutAuto">Otomatis</label>
                                    <div class="form-text">Posisi elemen diatur otomatis oleh sistem (responsive).</div>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="layout_mode" value="custom" id="layoutCustom" {{ $setting->layout_mode === 'custom' ? 'checked' : '' }} onchange="toggleLayoutMode()">
                                    <label class="form-check-label fw-semibold" for="layoutCustom">Kustom</label>
                                    <div class="form-text">Atur posisi (x, y) setiap elemen secara manual.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Position Controls --}}
                    <div id="positionControls" class="mt-4" style="display: {{ $setting->layout_mode === 'custom' ? 'block' : 'none' }};">
                        <div class="card shadow-sm" style="border-radius: 20px; border: none;">
                            <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                                <h5 class="fw-bold mb-0"><i class="fas fa-arrows-alt me-2" style="color: #6366f1;"></i>Posisi Elemen (x, y)</h5>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small mb-3"><i class="fas fa-info-circle me-1"></i> Atur posisi setiap elemen dalam pixel. (0,0) = pojok kiri atas kartu. Biarkan kosong untuk posisi default.</p>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr style="background: #f8fafc;">
                                                <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Elemen</th>
                                                <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">X (px)</th>
                                                <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Y (px)</th>
                                                <th style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Ukuran Font</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($elementLabels as $key => $label)
                                            @php $pos = $positions[$key] ?? []; @endphp
                                            <tr>
                                                <td style="font-size: 0.85rem;">{{ $label }}</td>
                                                <td><input type="number" name="pos_{{ $key }}_x" class="form-control form-control-sm" style="width: 80px;" value="{{ $pos['x'] ?? '' }}" placeholder="-" oninput="updatePreview()"></td>
                                                <td><input type="number" name="pos_{{ $key }}_y" class="form-control form-control-sm" style="width: 80px;" value="{{ $pos['y'] ?? '' }}" placeholder="-" oninput="updatePreview()"></td>
                                                <td><input type="number" name="pos_{{ $key }}_font_size" class="form-control form-control-sm" style="width: 80px;" value="{{ $pos['font_size'] ?? '' }}" placeholder="-" oninput="updatePreview()"></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Warna & Tampilan --}}
                    <div class="card shadow-sm mt-4" style="border-radius: 20px; border: none;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-paint-brush me-2" style="color: #6366f1;"></i>Warna & Tampilan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Warna Header (Mulai)</label>
                                    <div class="input-group">
                                        <input type="color" name="header_bg_start" class="form-control form-control-color" value="{{ $setting->header_bg_start }}" oninput="updatePreview()">
                                        <input type="text" class="form-control" value="{{ $setting->header_bg_start }}" id="header_bg_start_text" oninput="updatePreview()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Warna Header (Akhir)</label>
                                    <div class="input-group">
                                        <input type="color" name="header_bg_end" class="form-control form-control-color" value="{{ $setting->header_bg_end }}" oninput="updatePreview()">
                                        <input type="text" class="form-control" value="{{ $setting->header_bg_end }}" id="header_bg_end_text" oninput="updatePreview()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Warna Latar Kartu</label>
                                    <div class="input-group">
                                        <input type="color" name="card_bg_color" class="form-control form-control-color" value="{{ $setting->card_bg_color }}" oninput="updatePreview()">
                                        <input type="text" class="form-control" value="{{ $setting->card_bg_color }}" id="card_bg_color_text" oninput="updatePreview()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Warna Teks Header</label>
                                    <div class="input-group">
                                        <input type="color" name="header_text_color" class="form-control form-control-color" value="{{ $setting->header_text_color }}" oninput="updatePreview()">
                                        <input type="text" class="form-control" value="{{ $setting->header_text_color }}" id="header_text_color_text" oninput="updatePreview()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Warna Aksen</label>
                                    <div class="input-group">
                                        <input type="color" name="accent_color" class="form-control form-control-color" value="{{ $setting->accent_color }}" oninput="updatePreview()">
                                        <input type="text" class="form-control" value="{{ $setting->accent_color }}" id="accent_color_text" oninput="updatePreview()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Warna Border</label>
                                    <div class="input-group">
                                        <input type="color" name="border_color" class="form-control form-control-color" value="{{ $setting->border_color }}" oninput="updatePreview()">
                                        <input type="text" class="form-control" value="{{ $setting->border_color }}" id="border_color_text" oninput="updatePreview()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Font Family</label>
                                    <select name="font_family" class="form-select" onchange="updatePreview()">
                                        <option value="Segoe UI, Arial, sans-serif" {{ $setting->font_family == 'Segoe UI, Arial, sans-serif' ? 'selected' : '' }}>Segoe UI</option>
                                        <option value="Arial, Helvetica, sans-serif" {{ $setting->font_family == 'Arial, Helvetica, sans-serif' ? 'selected' : '' }}>Arial</option>
                                        <option value="'Times New Roman', Times, serif" {{ $setting->font_family == "'Times New Roman', Times, serif" ? 'selected' : '' }}>Times New Roman</option>
                                        <option value="'Courier New', monospace" {{ $setting->font_family == "'Courier New', monospace" ? 'selected' : '' }}>Courier New</option>
                                        <option value="Georgia, serif" {{ $setting->font_family == 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                                        <option value="Verdana, Geneva, sans-serif" {{ $setting->font_family == 'Verdana, Geneva, sans-serif' ? 'selected' : '' }}>Verdana</option>
                                        <option value="Tahoma, Geneva, sans-serif" {{ $setting->font_family == 'Tahoma, Geneva, sans-serif' ? 'selected' : '' }}>Tahoma</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ukuran & Font --}}
                    <div class="card shadow-sm mt-4" style="border-radius: 20px; border: none;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-ruler-combined me-2" style="color: #6366f1;"></i>Ukuran & Font</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Lebar (mm)</label>
                                    <input type="number" step="0.1" name="card_width" class="form-control" value="{{ $setting->card_width }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tinggi (mm)</label>
                                    <input type="number" step="0.1" name="card_height" class="form-control" value="{{ $setting->card_height }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Ukuran Font Nama</label>
                                    <input type="number" name="font_size_name" class="form-control" value="{{ $setting->font_size_name }}" oninput="updatePreview()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Ukuran Font Detail</label>
                                    <input type="number" name="font_size_detail" class="form-control" value="{{ $setting->font_size_detail }}" oninput="updatePreview()">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Elemen --}}
                    <div class="card shadow-sm mt-4" style="border-radius: 20px; border: none;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-toggle-on me-2" style="color: #6366f1;"></i>Elemen yang Ditampilkan</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @php
                                $toggles = [
                                    'show_logo' => 'Logo Sekolah',
                                    'show_school_name' => 'Nama Sekolah',
                                    'show_npsn' => 'NPSN',
                                    'show_photo' => 'Foto Siswa',
                                    'show_nis' => 'NIS',
                                    'show_nisn' => 'NISN',
                                    'show_class' => 'Kelas',
                                    'show_gender' => 'Jenis Kelamin',
                                    'show_birthplace' => 'Tempat & Tgl Lahir',
                                    'show_religion' => 'Agama',
                                    'show_address' => 'Alamat',
                                    'show_headmaster' => 'Kepala Sekolah',
                                    'show_validity' => 'Masa Berlaku',
                                ];
                                @endphp
                                @foreach($toggles as $key => $label)
                                <div class="col-md-4 col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="{{ $key }}" role="switch" id="{{ $key }}" value="1" {{ $setting->$key ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Teks Kustom --}}
                    <div class="card shadow-sm mt-4" style="border-radius: 20px; border: none;">
                        <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius: 20px 20px 0 0;">
                            <h5 class="fw-bold mb-0"><i class="fas fa-pen me-2" style="color: #6366f1;"></i>Teks Kustom</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Teks Samping (Sidebar Text)</label>
                                <input type="text" name="custom_sidebar_text" class="form-control" value="{{ $setting->custom_sidebar_text }}" placeholder="Misal: Kartu ID Siswa 2025/2026">
                                <div class="form-text">Teks kecil yang muncul di samping nama sekolah pada header kartu.</div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Teks Footer Kustom</label>
                                <textarea name="custom_footer_text" class="form-control" rows="2" placeholder="Misal: Kartu ini adalah milik sekolah...">{{ $setting->custom_footer_text }}</textarea>
                                <div class="form-text">Teks tambahan di bagian bawah kartu (opsional).</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.students.id-card') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="deleteBgForm" method="POST" action="{{ route('admin.id-card-settings.delete-template') }}" style="display:none;">
    @csrf
</form>
@endsection

@push('scripts')
<script>
function updatePreview() {
    var card = document.getElementById('previewCard');
    var header = document.getElementById('previewHeader');

    var hbs = document.querySelector('input[name="header_bg_start"]').value;
    var hbe = document.querySelector('input[name="header_bg_end"]').value;
    var cbg = document.querySelector('input[name="card_bg_color"]').value;
    var htc = document.querySelector('input[name="header_text_color"]').value;
    var ac = document.querySelector('input[name="accent_color"]').value;
    var bc = document.querySelector('input[name="border_color"]').value;
    var ff = document.querySelector('select[name="font_family"]').value;
    var cw = document.querySelector('input[name="card_width"]').value;
    var ch = document.querySelector('input[name="card_height"]').value;
    var fsn = document.querySelector('input[name="font_size_name"]').value;

    document.getElementById('header_bg_start_text').value = hbs;
    document.getElementById('header_bg_end_text').value = hbe;
    document.getElementById('card_bg_color_text').value = cbg;
    document.getElementById('header_text_color_text').value = htc;
    document.getElementById('accent_color_text').value = ac;
    document.getElementById('border_color_text').value = bc;

    header.style.background = 'linear-gradient(135deg, ' + hbs + ', ' + hbe + ')';
    header.style.color = htc;
    card.style.width = cw + 'mm';
    card.style.height = ch + 'mm';
    card.style.background = cbg;
    card.style.borderColor = bc;
    card.style.fontFamily = ff;
    document.getElementById('previewStudentName').style.fontSize = fsn + 'px';
}

function toggleLayoutMode() {
    document.getElementById('positionControls').style.display =
        document.getElementById('layoutCustom').checked ? 'block' : 'none';
}

function previewBgFile(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var overlay = document.getElementById('previewBgOverlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'previewBgOverlay';
                overlay.style.cssText = 'position: absolute; inset: 0; z-index: 1; pointer-events: none;';
                document.getElementById('previewCard').appendChild(overlay);
            }
            overlay.innerHTML = '<img src="' + e.target.result + '" style="width: 100%; height: 100%; object-fit: fill; opacity: 1;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function deleteBg() {
    if (confirm('Hapus background template?')) {
        document.getElementById('deleteBgForm').submit();
    }
}
</script>
@endpush
