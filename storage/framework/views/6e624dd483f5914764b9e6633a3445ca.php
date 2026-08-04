<?php
    $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $hariNama = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear = $month == 1 ? $year - 1 : $year;
    $nextMonth = $month == 12 ? 1 : $month + 1;
    $nextYear = $month == 12 ? $year + 1 : $year;
?>
<div>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="<?php echo e(route($route, ['month' => $prevMonth, 'year' => $prevYear])); ?>" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fas fa-chevron-left"></i></a>
        <span class="font-weight-bold text-dark"><?php echo e($namaBulan[$month - 1]); ?> <?php echo e($year); ?></span>
        <a href="<?php echo e(route($route, ['month' => $nextMonth, 'year' => $nextYear])); ?>" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fas fa-chevron-right"></i></a>
    </div>
    <table class="table table-bordered table-sm text-center mb-0">
        <thead>
            <tr>
                <?php $__currentLoopData = $hariNama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th class="text-uppercase text-muted small font-weight-bold py-1"><?php echo e($h); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
        <tbody>
            <?php
                $totalCells = $startDayOfWeek + $daysInMonth;
                $totalRows = ceil($totalCells / 7);
                $dayNum = 1;
            ?>
            <?php for($row = 0; $row < $totalRows; $row++): ?>
            <tr>
                <?php for($col = 0; $col < 7; $col++): ?>
                    <?php
                        $cellIndex = $row * 7 + $col;
                    ?>
                    <?php if($cellIndex < $startDayOfWeek || $dayNum > $daysInMonth): ?>
                    <td class="p-1"></td>
                    <?php else: ?>
                        <?php
                            $hasExam = isset($examDates[$dayNum]);
                            $isToday = $dayNum == today()->day && $month == today()->month && $year == today()->year;
                        ?>
                        <td class="p-1 align-top <?php echo e($isToday ? 'bg-primary' : ''); ?>">
                            <div class="d-flex flex-column align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center <?php echo e($isToday ? 'bg-white text-primary font-weight-bold rounded-circle' : ($hasExam ? 'text-warning font-weight-bold' : 'text-secondary')); ?>" style="width:24px;height:24px;font-size:0.8rem;"><?php echo e($dayNum); ?></span>
                                <?php if($hasExam): ?>
                                <div class="bg-warning rounded-circle mt-0" style="width:4px;height:4px;"></div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php $dayNum++; ?>
                    <?php endif; ?>
                <?php endfor; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</div>
<?php /**PATH C:\laragon\www\siakad\resources\views/components/calendar.blade.php ENDPATH**/ ?>