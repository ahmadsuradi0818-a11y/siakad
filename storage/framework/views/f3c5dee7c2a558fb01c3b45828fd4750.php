<?php
    $role = auth()->user()->role;
    $prefix = $role === 'admin' ? 'admin' : 'teacher';
    $panel = $role === 'admin' ? 'Admin Panel' : 'Guru Panel';
?>

<aside class="app-sidebar shadow" data-bs-theme="dark" style="background: linear-gradient(180deg, #1e3a5f 0%, #15294a 100%);">
    <div class="sidebar-brand">
        <a href="<?php echo e(route('dashboard')); ?>" class="brand-link" style="display:flex;align-items:center;gap:8px;text-decoration:none;">
            <i class="bi bi-mortarboard-fill brand-image opacity-75 shadow" style="margin:0;flex-shrink:0;"></i>
            <div style="min-width:0;overflow:hidden;">
                <span class="brand-text fw-light d-block text-truncate"><?php echo e(config('app.name', 'SIABEL')); ?></span>
                <small style="font-size:0.5rem;letter-spacing:1.5px;text-transform:uppercase;opacity:0.5;line-height:1.2;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Sistem Informasi Akademik Belajar</small>
            </div>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2" aria-label="Main navigation">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" data-accordion="false">
                <li class="nav-item">
                    <small class="text-muted text-uppercase px-3 py-2 d-block"><?php echo e($panel); ?></small>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.dashboard")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.dashboard") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if($role === 'admin'): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route('admin.classes.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.classes.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-building"></i>
                        <p>Kelas</p>
                    </a>
                </li>

                <?php $sekolahActive = request()->routeIs('admin.school-profile.*') || request()->routeIs('admin.academic-years.*'); ?>
                <li class="nav-item <?php echo e($sekolahActive ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e($sekolahActive ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-buildings"></i>
                        <p>
                            Data Sekolah
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.school-profile.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.school-profile.*') ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-info-circle"></i>
                                <p>Profil Sekolah</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.academic-years.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.academic-years.*') ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-calendar-range"></i>
                                <p>Tahun Ajaran</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.teachers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.teachers.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Guru</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.subjects.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.subjects.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-journal-text"></i>
                        <p>Mata Pelajaran</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.teacher-subjects.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.teacher-subjects.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-person-workspace"></i>
                        <p>Guru Mapel</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.schedules.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.schedules.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-calendar-week"></i>
                        <p>Jadwal Mengajar</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.grades.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.grades.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-calculator"></i>
                        <p>Olah Nilai</p>
                    </a>
                </li>

                <?php $laporanActive = request()->routeIs('admin.reports.*'); ?>
                <li class="nav-item <?php echo e($laporanActive ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e($laporanActive ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-bar-chart"></i>
                        <p>
                            Laporan
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reports.presensi')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.reports.presensi') ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-clipboard-data"></i>
                                <p>Presensi Siswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reports.presensi-guru')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.reports.presensi-guru') ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-person-check"></i>
                                <p>Presensi Guru</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reports.siswa')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.reports.siswa') ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-people"></i>
                                <p>Data Siswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reports.guru')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.reports.guru') ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-person-badge"></i>
                                <p>Data Guru</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.verification.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.verification.*') ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-clipboard-check"></i>
                        <p>Verifikasi Pendaftar</p>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.students.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.students.*") && !request()->routeIs("$prefix.students.id-card*") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Siswa</p>
                    </a>
                </li>

                <?php if($role !== 'admin'): ?>
                <?php $presensiActive = request()->routeIs("$prefix.presences.*"); ?>
                <li class="nav-item <?php echo e($presensiActive ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e($presensiActive ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-clipboard-check"></i>
                        <p>
                            Presensi
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.presences.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.presences.index") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-table"></i>
                                <p>Riwayat Presensi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.presences.create")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.presences.create") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>Input Presensi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.presences.report")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.presences.report") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-file-earmark-text"></i>
                                <p>Laporan Presensi</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if($role === 'guru'): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.schedule")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.schedule") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-calendar-week"></i>
                        <p>Jadwal Saya</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.teacher-presences.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.teacher-presences.*") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-fingerprint"></i>
                        <p>Presensi Guru</p>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.bank-soal.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.bank-soal.*") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-database"></i>
                        <p>Bank Soal</p>
                    </a>
                </li>

                <?php $examActive = request()->routeIs("$prefix.exams.*"); ?>
                <li class="nav-item <?php echo e($examActive ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e($examActive ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-file-text"></i>
                        <p>
                            Ujian
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.exams.soal")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.exams.soal", "$prefix.exams.questions.*") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-question-circle"></i>
                                <p>Soal</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.exams.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.exams.index", "$prefix.exams.create", "$prefix.exams.edit") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-calendar-check"></i>
                                <p>Rencana Ujian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.exams.monitoring")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.exams.monitoring") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-eye"></i>
                                <p>Monitoring Ujian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.exams.nilai")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.exams.nilai") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-file-earmark-check"></i>
                                <p>Nilai Ujian</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.exams.id-card-select")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.exams.id-card*") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-card-heading"></i>
                                <p>Kartu Ujian</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php $tugasActive = request()->routeIs("$prefix.assignments.*"); ?>
                <li class="nav-item <?php echo e($tugasActive ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e($tugasActive ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-check2-square"></i>
                        <p>
                            Tugas
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.assignments.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.assignments.index", "$prefix.assignments.create", "$prefix.assignments.edit") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-list-task"></i>
                                <p>Daftar Tugas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.assignments.nilai")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.assignments.nilai") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-file-earmark-check"></i>
                                <p>Nilai Tugas</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.announcements.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.announcements.*") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-megaphone"></i>
                        <p>Pengumuman</p>
                    </a>
                </li>

                <?php if($role === 'guru'): ?>
                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.grades.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.grades.*") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-calculator"></i>
                        <p>Olah Nilai</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if($role === 'admin' || ($role === 'guru' && auth()->user()->homeroomClasses()->exists())): ?>
                <?php $raportActive = request()->routeIs("$prefix.report-cards.*"); ?>
                <li class="nav-item <?php echo e($raportActive ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e($raportActive ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-file-earmark-text"></i>
                        <p>
                            Raport
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.report-cards.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.report-cards.index", "$prefix.report-cards.show", "$prefix.report-cards.generate", "$prefix.report-cards.edit") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-files"></i>
                                <p>Daftar Raport</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route("$prefix.report-cards.leger")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.report-cards.leger", "$prefix.report-cards.leger-update") ? 'active' : ''); ?>">
                                <i class="nav-icon bi bi-table"></i>
                                <p>Leger Nilai</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?php echo e(route("$prefix.lessons.index")); ?>" class="nav-link <?php echo e(request()->routeIs("$prefix.lessons.*") ? 'active' : ''); ?>">
                        <i class="nav-icon bi bi-book"></i>
                        <p>Materi</p>
                    </a>
                </li>

                <li class="nav-header">Akun</li>

                <li class="nav-item">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="nav-link <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>">
                        <?php $u = auth()->user(); ?>
                        <?php if($u->photo_url): ?>
                        <img src="<?php echo e($u->photo_url); ?>" alt=""
                            style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin: 0 14px 0 4px;">
                        <?php else: ?>
                        <i class="nav-icon bi bi-person"></i>
                        <?php endif; ?>
                        <p>Profil</p>
                    </a>
                </li>
            </ul>

            <div class="p-3 mt-3 border-top border-secondary border-opacity-25">
                <button type="button" class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </div>
        </nav>
    </div>
</aside>
<?php /**PATH C:\laragon\www\siakad\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>