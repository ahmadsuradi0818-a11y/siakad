<?php $__env->startSection('title', 'Kartu Ujian - Pilih Ujian'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12 d-flex align-items-stretch">
        <div class="card border-0 shadow-sm rounded-3 w-100">
            <div class="card-header d-flex align-items-center justify-content-between border-0 rounded-top-3 px-3 px-md-4" style="background: linear-gradient(135deg, #1e3a5f, #2563eb);">
                <h3 class="card-title text-white m-0 fs-5">
                    <i class="fas fa-file-alt me-2"></i> Kartu Ujian
                </h3>
            </div>
            <div class="card-body px-3 px-md-4 pt-3">
                <div class="table-responsive rounded-3" style="border: 1px solid #eef2f7;">
                    <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: #f8fafc;">
                                <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Ujian</th>
                                <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Mapel</th>
                                <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Kelas</th>
                                <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Tanggal</th>
                                <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Tipe</th>
                                <th class="text-center border-bottom-0 py-3" style="width: 100px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                <td class="py-3">
                                    <span class="fw-semibold" style="color: #1e293b; font-size: 0.9rem;"><?php echo e($exam->name); ?></span>
                                </td>
                                <td class="py-3" style="font-size: 0.85rem;"><?php echo e($exam->subject->name ?? '-'); ?></td>
                                <td class="py-3">
                                    <?php $__currentLoopData = $exam->classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge rounded-pill px-2 py-1 fw-normal" style="background: #eef2ff; color: #4f46e5; font-size: 0.7rem;"><?php echo e($class->name); ?></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td class="py-3" style="font-size: 0.85rem;"><?php echo e($exam->date ? $exam->date->format('d/m/Y') : '-'); ?></td>
                                <td class="py-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5 fw-normal text-uppercase" style="font-size: 0.7rem;"><?php echo e($exam->type); ?></span>
                                </td>
                                <td class="text-center py-3">
                                    <a href="<?php echo e(route('admin.exams.id-card', $exam)); ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-print me-1"></i> Cetak
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="mb-2" style="font-size: 2.5rem; opacity: 0.2;"><i class="fas fa-file-alt"></i></div>
                                    Belum ada ujian
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if(method_exists($exams, 'links') && $exams->hasPages()): ?>
            <div class="card-footer border-top bg-white rounded-bottom-3 px-3 px-md-4 py-3">
                <div class="d-flex justify-content-center"><?php echo e($exams->links()); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\siakad\resources\views/admin/exams/id-card-select-exam.blade.php ENDPATH**/ ?>