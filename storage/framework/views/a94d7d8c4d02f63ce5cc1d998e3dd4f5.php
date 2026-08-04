<?php $__env->startSection('title', 'Data Siswa'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12 d-flex align-items-stretch">
        <div class="card border-0 shadow-sm rounded-3 w-100">
            
            <div class="card-header d-flex align-items-center justify-content-between border-0 rounded-top-3 px-3 px-md-4" style="background: linear-gradient(135deg, #1e3a5f, #2563eb);">
                <h3 class="card-title text-white m-0 fs-5">
                    <i class="fas fa-user-graduate me-2"></i> Data Siswa
                </h3>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-upload me-1"></i> Import
                    </button>
                    <button type="button" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>
            </div>

            
            <div class="card-body px-3 px-md-4 pt-3">
                
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div class="input-group input-group-sm" style="max-width: 320px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill">
                            <i class="fas fa-search text-muted" style="font-size: 0.8rem;"></i>
                        </span>
                        <input type="text" id="searchSiswa" class="form-control border-start-0 ps-2 rounded-end-pill" placeholder="Cari nama, NIS, email..." onkeyup="filterTable()" style="font-size: 0.85rem;">
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-normal small">
                            <i class="fas fa-layer-group me-1"></i> <?php echo e($students->total() ?? $students->count()); ?> siswa
                        </span>
                        <?php
                            $noClass = $students->filter(fn($s) => is_null($s->class_id))->count();
                        ?>
                        <?php if($noClass > 0): ?>
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fw-normal small">
                            <i class="fas fa-exclamation-triangle me-1"></i> <?php echo e($noClass); ?> belum punya kelas
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="table-responsive rounded-3" style="border: 1px solid #eef2f7;">
                    <table class="table align-middle mb-0 table-responsive-card" id="siswaTable" style="border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th class="text-center border-bottom-0 py-3" style="width: 60px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">No</th>
                                <th class="table-col-nis border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">NIS</th>
                                <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Nama</th>
                                <th class="table-col-email border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Email</th>
                                <th class="table-col-kelas border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Kelas</th>
                                <th class="table-col-phone border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">No. WA</th>
                                <th class="text-center border-bottom-0 py-3" style="width: 110px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $avatarGradients = [
                                    'linear-gradient(135deg, #667eea, #764ba2)',
                                    'linear-gradient(135deg, #f093fb, #f5576c)',
                                    'linear-gradient(135deg, #4facfe, #00f2fe)',
                                    'linear-gradient(135deg, #43e97b, #38f9d7)',
                                    'linear-gradient(135deg, #fa709a, #fee140)',
                                    'linear-gradient(135deg, #a18cd1, #fbc2eb)',
                                ];
                                $avatarBg = $avatarGradients[crc32($student->user->name) % count($avatarGradients)];
                            ?>
                            <tr class="align-middle" style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <td class="text-center text-muted py-3" style="font-size: 0.8rem;"><?php echo e($loop->iteration); ?></td>
                                <td class="table-col-nis py-3"><span class="badge bg-light text-muted font-monospace rounded-pill px-3 py-1.5 fw-normal" style="font-size: 0.75rem;"><?php echo e($student->nis); ?></span></td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-sm" style="width: 40px; height: 40px; font-size: 0.85rem; background: <?php echo e($avatarBg); ?>;">
                                            <?php echo e(strtoupper(substr($student->user->name, 0, 1))); ?>

                                        </div>
                                        <div class="min-width-0">
                                            <span class="fw-semibold d-block text-truncate" style="color: #1e293b; font-size: 0.9rem; max-width: 200px;"><?php echo e($student->user->name); ?></span>
                                            <span class="text-muted d-block" style="font-size: 0.7rem;">
                                                <?php if($student->nisn): ?>
                                                <span class="me-2"><i class="fas fa-id-card me-1" style="font-size: 0.6rem;"></i>NISN: <?php echo e($student->nisn); ?></span>
                                                <?php endif; ?>
                                                <?php if($student->place_of_birth): ?>
                                                <span><i class="fas fa-map-pin me-1" style="font-size: 0.6rem;"></i><?php echo e($student->place_of_birth); ?>, <?php echo e($student->date_of_birth?->format('d M Y')); ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-col-email py-3">
                                    <span class="text-muted small d-flex align-items-center gap-1">
                                        <i class="fas fa-envelope" style="font-size: 0.7rem; color: #94a3b8;"></i>
                                        <span style="font-size: 0.8rem;"><?php echo e($student->user->email); ?></span>
                                    </span>
                                </td>
                                <td class="table-col-kelas py-3">
                                    <?php if($student->class): ?>
                                    <span class="badge rounded-pill px-3 py-1.5 fw-normal d-inline-flex align-items-center gap-1" style="background: #eef2ff; color: #4f46e5; font-size: 0.75rem;">
                                        <i class="fas fa-school" style="font-size: 0.6rem;"></i> <?php echo e($student->class->name); ?>

                                    </span>
                                    <?php else: ?>
                                    <span class="badge rounded-pill px-3 py-1.5 fw-normal d-inline-flex align-items-center gap-1" style="background: #fef2f2; color: #dc2626; font-size: 0.75rem;">
                                        <i class="fas fa-exclamation-circle" style="font-size: 0.6rem;"></i> Belum ditentukan
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-col-phone py-3">
                                    <?php if($student->parent_phone): ?>
                                    <span class="d-flex align-items-center gap-1" style="font-size: 0.8rem; color: #059669;">
                                        <i class="fab fa-whatsapp" style="font-size: 0.85rem;"></i>
                                        <span><?php echo e($student->parent_phone); ?></span>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.75rem;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button"
                                            class="btn btn-sm border-0 rounded-2 d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px; background: #fef3c7; color: #d97706; transition: all 0.2s;"
                                            onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'"
                                            title="Edit Siswa"
                                            data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-url="<?php echo e(route('admin.students.update', $student)); ?>"
                                            data-name="<?php echo e($student->user->name); ?>"
                                            data-email="<?php echo e($student->user->email); ?>"
                                            data-nis="<?php echo e($student->nis); ?>"
                                            data-class="<?php echo e($student->class_id); ?>"
                                            data-parent-phone="<?php echo e($student->parent_phone); ?>"
                                            data-place-of-birth="<?php echo e($student->place_of_birth); ?>"
                                            data-date-of-birth="<?php echo e($student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : ''); ?>"
                                            data-address="<?php echo e($student->address); ?>"
                                            data-parent-name="<?php echo e($student->parent_name); ?>"
                                            data-nisn="<?php echo e($student->nisn); ?>"
                                            data-gender="<?php echo e($student->gender); ?>"
                                            data-religion="<?php echo e($student->religion); ?>">
                                            <i class="fas fa-edit" style="font-size: 0.75rem;"></i>
                                        </button>
                                        <button type="button"
                                            class="btn btn-sm border-0 rounded-2 d-flex align-items-center justify-content-center btn-delete"
                                            style="width: 32px; height: 32px; background: #fee2e2; color: #dc2626; transition: all 0.2s;"
                                            onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'"
                                            title="Hapus Siswa"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-url="<?php echo e(route('admin.students.destroy', $student)); ?>"
                                            data-name="<?php echo e($student->user->name); ?>">
                                            <i class="fas fa-trash" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="border-0">
                                    <div class="text-center py-5">
                                        <div class="mb-3" style="font-size: 3.5rem; opacity: 0.15;"><i class="fas fa-user-graduate"></i></div>
                                        <h5 class="text-muted mb-1" style="color: #64748b !important;">Belum ada data siswa</h5>
                                        <p class="text-muted small mb-3" style="color: #94a3b8 !important;">Mulai dengan menambahkan siswa baru.</p>
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                                            <i class="fas fa-plus me-1"></i> Tambah Siswa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <?php if(method_exists($students, 'links') && $students->hasPages()): ?>
            <div class="card-footer border-top bg-white rounded-bottom-3 px-3 px-md-4 py-3">
                <div class="d-flex justify-content-center">
                    <?php echo e($students->links()); ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo e(route('admin.students.import')); ?>" method="POST" enctype="multipart/form-data" class="modal-content shadow">
            <?php echo csrf_field(); ?>
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #1e3a5f, #2563eb);">
                <h5 class="modal-title text-white"><i class="fas fa-upload me-2"></i>Import Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; opacity: 0.4;">
                        <i class="fas fa-file-excel text-primary"></i>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fas fa-file me-1"></i> File (Excel .xlsx/.xls atau Word .docx)</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.docx" required>
                </div>
                <div class="alert alert-info py-2 small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Format file:</strong> Kolom: Nama, Email, Password, NIS, Nama Kelas, No. WA Orang Tua (opsional).
                                    Baris pertama = header (diabaikan). Nama Kelas harus sesuai data yang sudah ada.
                    <br>
                    <a href="<?php echo e(route('admin.students.download-template')); ?>" class="text-primary fw-semibold">
                        <i class="fas fa-download me-1"></i> Download template
                    </a>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="submit" class="btn btn-success shadow-sm">
                    <i class="fas fa-upload me-1"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #1e3a5f, #2563eb);">
                <h5 class="modal-title text-white"><i class="fas fa-user-plus me-2"></i>Tambah Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-user text-primary me-1"></i> Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user text-muted"></i></span>
                                <input type="text" id="crName" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-envelope text-warning me-1"></i> Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-envelope text-muted"></i></span>
                                <input type="email" id="crEmail" class="form-control" placeholder="contoh@email.com" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-lock text-danger me-1"></i> Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-key text-muted"></i></span>
                                <input type="password" id="crPassword" class="form-control" placeholder="Minimal 8 karakter" required>
                                <button type="button" class="input-group-text bg-white" onclick="togglePass()" tabindex="-1">
                                    <i class="fas fa-eye text-muted" id="crEye"></i>
                                </button>
                            </div>
                            <div class="form-text small text-muted"><i class="fas fa-info-circle me-1"></i> Minimal <strong>8 karakter</strong></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-id-card text-info me-1"></i> NIS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-barcode text-muted"></i></span>
                                <input type="text" id="crNis" class="form-control" placeholder="Nomor Induk Siswa" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-id-card text-secondary me-1"></i> NISN</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-qrcode text-muted"></i></span>
                                <input type="text" id="crNisn" class="form-control" placeholder="Nomor Induk Siswa Nasional">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-venus-mars text-purple me-1"></i> Jenis Kelamin</label>
                            <select id="crGender" class="form-select">
                                <option value="">— Pilih —</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-map-pin text-danger me-1"></i> Tempat Lahir</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-city text-muted"></i></span>
                                <input type="text" id="crPlaceBirth" class="form-control" placeholder="Kota lahir">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-calendar text-info me-1"></i> Tanggal Lahir</label>
                            <input type="date" id="crDateBirth" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-place-of-worship text-secondary me-1"></i> Agama</label>
                            <select id="crReligion" class="form-select">
                                <option value="">— Pilih —</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fas fa-home text-success me-1"></i> Alamat</label>
                    <textarea id="crAddress" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-users text-secondary me-1"></i> Nama Orang Tua / Wali</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user-friends text-muted"></i></span>
                                <input type="text" id="crParentName" class="form-control" placeholder="Nama ayah/ibu/wali">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label fw-semibold"><i class="fab fa-whatsapp text-success me-1"></i> No. WA Orang Tua</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fab fa-whatsapp text-muted"></i></span>
                                <input type="text" id="crParentPhone" class="form-control" placeholder="Contoh: 081234567890">
                            </div>
                            <div class="form-text small text-muted"><i class="fas fa-info-circle me-1"></i> Untuk notifikasi presensi via WhatsApp</div>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold"><i class="fas fa-school text-success me-1"></i> Kelas</label>
                    <select id="crClass" class="form-select">
                        <option value="">— Pilih Kelas —</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnSave" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #1e3a5f, #2563eb);">
                <h5 class="modal-title text-white"><i class="fas fa-edit me-2"></i>Edit Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-user text-primary me-1"></i> Nama Lengkap</label>
                            <input type="text" id="editName" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-envelope text-warning me-1"></i> Email</label>
                            <input type="email" id="editEmail" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-id-card text-info me-1"></i> NIS</label>
                            <input type="text" id="editNis" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-id-card text-secondary me-1"></i> NISN</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-qrcode text-muted"></i></span>
                                <input type="text" id="editNisn" class="form-control" placeholder="Nomor Induk Siswa Nasional">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-venus-mars text-purple me-1"></i> Jenis Kelamin</label>
                            <select id="editGender" class="form-select">
                                <option value="">— Pilih —</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-map-pin text-danger me-1"></i> Tempat Lahir</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-city text-muted"></i></span>
                                <input type="text" id="editPlaceBirth" class="form-control" placeholder="Kota lahir">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-calendar text-info me-1"></i> Tanggal Lahir</label>
                            <input type="date" id="editDateBirth" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold"><i class="fas fa-home text-success me-1"></i> Alamat</label>
                    <textarea id="editAddress" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-users text-secondary me-1"></i> Nama Orang Tua / Wali</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user-friends text-muted"></i></span>
                                <input type="text" id="editParentName" class="form-control" placeholder="Nama ayah/ibu/wali">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fab fa-whatsapp text-success me-1"></i> No. WA Orang Tua</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fab fa-whatsapp text-muted"></i></span>
                                <input type="text" id="editParentPhone" class="form-control" placeholder="Contoh: 081234567890">
                            </div>
                            <div class="form-text small text-muted"><i class="fas fa-info-circle me-1"></i> Untuk notifikasi presensi via WhatsApp</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="fas fa-place-of-worship text-secondary me-1"></i> Agama</label>
                            <select id="editReligion" class="form-select">
                                <option value="">— Pilih —</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label fw-semibold"><i class="fas fa-school text-success me-1"></i> Kelas</label>
                            <select id="editClass" class="form-select">
                                <option value="">— Pilih Kelas —</option>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->id); ?>"><?php echo e($class->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-0">
                            <label class="form-label fw-semibold"><i class="fas fa-lock text-danger me-1"></i> Password <span class="text-muted fw-normal">(kosongkan jika tidak diubah)</span></label>
                            <div class="input-group">
                                <input type="password" id="editPassword" class="form-control" placeholder="Minimal 8 karakter">
                                <button type="button" class="input-group-text bg-white" onclick="toggleEditPass()" tabindex="-1">
                                    <i class="fas fa-eye text-muted" id="editEye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnEdit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow text-center">
            <div class="modal-body py-5">
                <div class="mb-3" style="font-size: 4rem; color: #f5576c;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="fw-bold mb-1">Hapus Siswa</h5>
                <p class="text-muted mb-0" id="deleteMessage">Yakin ingin menghapus siswa ini?</p>
            </div>
            <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-4">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" id="btnConfirmDelete" class="btn btn-danger px-4 shadow-sm">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<style>
.btn-delete i { pointer-events: none; }
</style>
<script>
function filterTable() {
    var input = document.getElementById('searchSiswa');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('siswaTable');
    var tbody = table.querySelector('tbody');
    if (!tbody) return;
    var rows = tbody.getElementsByTagName('tr');
    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName('td');
        var found = false;
        for (var j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}

function togglePass() {
    var inp = document.getElementById('crPassword');
    var eye = document.getElementById('crEye');
    if (inp.type === 'password') { inp.type = 'text'; eye.classList.replace('fa-eye', 'fa-eye-slash'); }
    else { inp.type = 'password'; eye.classList.replace('fa-eye-slash', 'fa-eye'); }
}
function toggleEditPass() {
    var inp = document.getElementById('editPassword');
    var eye = document.getElementById('editEye');
    if (inp.type === 'password') { inp.type = 'text'; eye.classList.replace('fa-eye', 'fa-eye-slash'); }
    else { inp.type = 'password'; eye.classList.replace('fa-eye-slash', 'fa-eye'); }
}

document.addEventListener('DOMContentLoaded', function () {
    var csrf = document.querySelector('meta[name="csrf-token"]').content;

    // CREATE
    document.getElementById('btnSave').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('name', document.getElementById('crName').value);
        fd.append('email', document.getElementById('crEmail').value);
        fd.append('password', document.getElementById('crPassword').value);
        fd.append('nis', document.getElementById('crNis').value);
        fd.append('class_id', document.getElementById('crClass').value);
        fd.append('place_of_birth', document.getElementById('crPlaceBirth').value);
        fd.append('date_of_birth', document.getElementById('crDateBirth').value);
        fd.append('address', document.getElementById('crAddress').value);
        fd.append('parent_name', document.getElementById('crParentName').value);
        fd.append('parent_phone', document.getElementById('crParentPhone').value);
        fd.append('nisn', document.getElementById('crNisn').value);
        fd.append('gender', document.getElementById('crGender').value);
        fd.append('religion', document.getElementById('crReligion').value);
        fetch('<?php echo e(route('admin.students.store')); ?>', { method: 'POST', body: fd })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });

    // EDIT
    var _editUrl = '';
    document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        _editUrl = btn.getAttribute('data-url');
        document.getElementById('editName').value = btn.getAttribute('data-name');
        document.getElementById('editEmail').value = btn.getAttribute('data-email');
        document.getElementById('editNis').value = btn.getAttribute('data-nis');
        document.getElementById('editClass').value = btn.getAttribute('data-class');
        document.getElementById('editParentPhone').value = btn.getAttribute('data-parent-phone') || '';
        document.getElementById('editPlaceBirth').value = btn.getAttribute('data-place-of-birth') || '';
        document.getElementById('editDateBirth').value = btn.getAttribute('data-date-of-birth') || '';
        document.getElementById('editAddress').value = btn.getAttribute('data-address') || '';
        document.getElementById('editParentName').value = btn.getAttribute('data-parent-name') || '';
        document.getElementById('editNisn').value = btn.getAttribute('data-nisn') || '';
        document.getElementById('editGender').value = btn.getAttribute('data-gender') || '';
        document.getElementById('editReligion').value = btn.getAttribute('data-religion') || '';
        document.getElementById('editPassword').value = '';
    });
    document.getElementById('btnEdit').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('_method', 'PUT');
        fd.append('name', document.getElementById('editName').value);
        fd.append('email', document.getElementById('editEmail').value);
        fd.append('nis', document.getElementById('editNis').value);
        fd.append('class_id', document.getElementById('editClass').value);
        fd.append('place_of_birth', document.getElementById('editPlaceBirth').value);
        fd.append('date_of_birth', document.getElementById('editDateBirth').value);
        fd.append('address', document.getElementById('editAddress').value);
        fd.append('parent_name', document.getElementById('editParentName').value);
        fd.append('parent_phone', document.getElementById('editParentPhone').value);
        fd.append('nisn', document.getElementById('editNisn').value);
        fd.append('gender', document.getElementById('editGender').value);
        fd.append('religion', document.getElementById('editReligion').value);
        fd.append('password', document.getElementById('editPassword').value);
        fetch(_editUrl, { method: 'POST', body: fd })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });

    // DELETE
    var _deleteUrl = '';
    document.getElementById('deleteModal').addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        _deleteUrl = btn.getAttribute('data-url');
        document.getElementById('deleteMessage').textContent = 'Yakin ingin menghapus siswa ' + btn.getAttribute('data-name') + '?';
    });
    document.getElementById('btnConfirmDelete').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = 'Menghapus...';
        var fd = new FormData();
        fd.append('_token', csrf);
        fd.append('_method', 'DELETE');
        fetch(_deleteUrl, { method: 'POST', body: fd })
            .then(function () { window.location.reload(); })
            .catch(function () { window.location.reload(); });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\siakad\resources\views/admin/students/index.blade.php ENDPATH**/ ?>