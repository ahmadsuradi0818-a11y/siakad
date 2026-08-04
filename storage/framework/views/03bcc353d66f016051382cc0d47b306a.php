<?php $__env->startSection('title', 'Cetak Kartu Ujian'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12 d-flex align-items-stretch">
        <div class="card border-0 shadow-sm rounded-3 w-100">
            <div class="card-header d-flex align-items-center justify-content-between border-0 rounded-top-3 px-3 px-md-4" style="background: linear-gradient(135deg, #1e3a5f, #2563eb);">
                <h3 class="card-title text-white m-0 fs-5">
                    <i class="fas fa-file-alt me-2"></i> Cetak Kartu Ujian
                </h3>
            </div>

            <div class="card-body px-3 px-md-4 pt-3">
                <div class="d-flex align-items-center gap-2 mb-4 p-3 rounded-3" style="background: #f8fafc; border: 1px solid #eef2f7;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #1e3a5f, #2563eb); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; flex-shrink: 0;">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #1e293b;"><?php echo e($exam->name); ?></h5>
                        <div class="d-flex gap-3 flex-wrap small text-muted">
                            <span><i class="fas fa-book me-1"></i> <?php echo e($exam->subject->name ?? '-'); ?></span>
                            <span><i class="fas fa-calendar me-1"></i> <?php echo e($exam->date ? $exam->date->format('d/m/Y') : '-'); ?></span>
                            <span><i class="fas fa-clock me-1"></i> <?php echo e($exam->start_time ? substr($exam->start_time, 0, 5) : '-'); ?> - <?php echo e($exam->end_time ? substr($exam->end_time, 0, 5) : '-'); ?></span>
                            <span><i class="fas fa-tag me-1"></i> <?php echo e(strtoupper($exam->type)); ?></span>
                            <span><i class="fas fa-chalkboard-teacher me-1"></i> <?php echo e($exam->teacher->name ?? '-'); ?></span>
                        </div>
                    </div>
                </div>

                <form action="<?php echo e(route('admin.exams.id-card-print', $exam)); ?>" method="GET" id="printForm" target="_blank">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-normal small">
                                <i class="fas fa-layer-group me-1"></i> <?php echo e($students->count()); ?> siswa
                            </span>
                            <?php $__currentLoopData = $exam->classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge rounded-pill px-3 py-2 fw-normal" style="background: #eef2ff; color: #4f46e5; font-size: 0.75rem;">
                                <i class="fas fa-school me-1"></i> <?php echo e($class->name); ?>

                            </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="table-responsive rounded-3" style="border: 1px solid #eef2f7;">
                        <table class="table align-middle mb-0" style="border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th class="border-bottom-0 py-3 text-center" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; width: 60px;">No</th>
                                    <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">NIS</th>
                                    <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Nama</th>
                                    <th class="border-bottom-0 py-3" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">Kelas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                                    <td class="text-center py-3">
                                        <div class="form-check">
                                            <input class="form-check-input student-check" type="checkbox" name="students[]" value="<?php echo e($student->id); ?>">
                                        </div>
                                    </td>
                                    <td class="text-muted py-3" style="font-size: 0.8rem;"><?php echo e($loop->iteration); ?></td>
                                    <td class="py-3"><span class="badge bg-light text-muted font-monospace rounded-pill px-3 py-1.5 fw-normal" style="font-size: 0.75rem;"><?php echo e($student->nis); ?></span></td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-sm" style="width: 36px; height: 36px; font-size: 0.8rem; background: linear-gradient(135deg, #667eea, #764ba2);">
                                                <?php echo e(strtoupper(substr($student->user->name, 0, 1))); ?>

                                            </div>
                                            <span class="fw-semibold" style="color: #1e293b; font-size: 0.9rem;"><?php echo e($student->user->name); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill px-3 py-1.5 fw-normal" style="background: #eef2ff; color: #4f46e5; font-size: 0.75rem;">
                                            <i class="fas fa-school me-1"></i> <?php echo e($student->class->name ?? '-'); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="mb-2" style="font-size: 2.5rem; opacity: 0.2;"><i class="fas fa-file-alt"></i></div>
                                        Tidak ada siswa untuk ujian ini
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <div>
                            <span class="text-muted small" id="selectedCount"><i class="fas fa-check-circle me-1"></i> 0 siswa dipilih</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('admin.exams.index')); ?>" class="btn btn-outline-secondary rounded-pill px-3">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" onclick="printSelected()">
                                <i class="fas fa-print me-1"></i> Cetak Kartu Ujian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);
    updateCount();
});

document.querySelectorAll('.student-check').forEach(cb => {
    cb.addEventListener('change', updateCount);
});

function updateCount() {
    var count = document.querySelectorAll('.student-check:checked').length;
    document.getElementById('selectedCount').innerHTML = '<i class="fas fa-check-circle me-1"></i> ' + count + ' siswa dipilih';
}

function printSelected() {
    var checked = document.querySelectorAll('.student-check:checked');
    if (checked.length === 0) {
        alert('Pilih minimal satu siswa.');
        return;
    }
    var form = document.getElementById('printForm');
    form.submit();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\siakad\resources\views/admin/exams/id-card-index.blade.php ENDPATH**/ ?>