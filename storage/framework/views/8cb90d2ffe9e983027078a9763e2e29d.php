<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta Ujian - <?php echo e($exam->name); ?></title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 5px; }
        .card-container { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        .exam-card {
            width: 180mm; height: 60mm;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            overflow: hidden;
            page-break-inside: avoid;
            border: 2px solid #1e3a5f;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .exam-card-header {
            background: linear-gradient(135deg, #1e3a5f, #2563eb);
            color: white;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .exam-card-header .school-logo {
            width: 28px; height: 28px;
            object-fit: contain; border-radius: 50%;
            background: #fff; flex-shrink: 0;
        }
        .exam-card-header .header-text {
            flex: 1; line-height: 1.1;
        }
        .exam-card-header .header-text .school-name {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .exam-card-header .header-text .exam-title {
            font-size: 7px; opacity: 0.85;
        }
        .exam-card-body {
            flex: 1; display: flex;
            padding: 6px 12px;
            gap: 12px;
        }
        .exam-card-left {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
        }
        .exam-card-left .card-label {
            font-size: 13px; font-weight: 800;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .exam-card-left .data-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
            padding: 1.5px 0;
            border-bottom: 1px dotted #e0e0e0;
        }
        .exam-card-left .data-row .label {
            font-size: 7px; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.5px;
            width: 55px; flex-shrink: 0;
        }
        .exam-card-left .data-row .value {
            font-size: 9px; font-weight: 600;
            color: #1e293b;
        }
        .exam-card-left .data-row .value.highlight {
            font-size: 11px;
        }
        .exam-card-center {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 90px;
            border-left: 2px dashed #e0e0e0;
            padding-left: 12px;
        }
        .exam-card-center .photo {
            width: 52px; height: 60px;
            border: 2px solid #1e3a5f;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 22px;
            color: white;
            overflow: hidden;
        }
        .exam-card-center .photo img {
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .exam-card-center .no-peserta {
            font-size: 7px; color: #64748b;
            margin-top: 3px; text-align: center; line-height: 1.2;
        }
        .exam-card-center .no-peserta .num {
            font-size: 11px; font-weight: 700; color: #1e3a5f;
            letter-spacing: 1px;
        }
        .exam-card-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 95px;
            border-left: 2px dashed #e0e0e0;
            padding-left: 12px;
        }
        .exam-card-right .schedule-row {
            padding: 2px 0;
        }
        .exam-card-right .schedule-row .label {
            font-size: 6.5px; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .exam-card-right .schedule-row .value {
            font-size: 8px; font-weight: 600;
            color: #1e293b;
        }
        .exam-card-footer {
            background: #f8fafc;
            border-top: 1px solid #e0e0e0;
            padding: 4px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .exam-card-footer .signature-col {
            text-align: center; line-height: 1.1;
        }
        .exam-card-footer .signature-col .role {
            font-size: 6px; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .exam-card-footer .signature-col .line {
            display: block; margin: 1px auto;
            border-top: 1px solid #1e293b;
            width: 65px; height: 14px;
        }
        .exam-card-footer .signature-col .name {
            font-size: 7px; font-weight: 600; color: #1e293b;
        }
        .exam-card-footer .barcode-area {
            display: flex; align-items: center; gap: 6px;
        }
        .exam-card-footer .barcode-area .barcode-text {
            font-size: 14px; font-weight: 800;
            color: #1e293b;
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
        }
        .exam-card-footer .barcode-area .barcode-label {
            font-size: 5px; color: #94a3b8;
            text-transform: uppercase;
        }
        .footer-actions { text-align: center; margin: 15px 0; }
        .btn-print {
            padding: 10px 30px; font-size: 14px; border: none;
            border-radius: 50px;
            background: linear-gradient(135deg, #1e3a5f, #2563eb);
            color: white; cursor: pointer;
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        .btn-print:hover { opacity: 0.9; }
        .page-info { text-align: center; font-size: 11px; color: #64748b; margin-bottom: 10px; }
        @media print {
            .footer-actions { display: none; }
            .exam-card { box-shadow: none; }
            @page { margin: 5mm; }
        }
    </style>
</head>
<body>
    <div class="footer-actions">
        <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
        <p style="margin-top:8px;font-size:12px;color:#64748b;">Pastikan orientasi kertas Landscape.</p>
    </div>

    <div class="page-info">
        Kartu Peserta Ujian — <?php echo e($exam->name); ?>

        (<?php echo e($students->count()); ?> peserta)
    </div>

    <div class="card-container">
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $initial = strtoupper(substr($student->user->name, 0, 1));
            $gradients = ['linear-gradient(135deg, #667eea, #764ba2)','linear-gradient(135deg, #f093fb, #f5576c)','linear-gradient(135deg, #4facfe, #00f2fe)','linear-gradient(135deg, #43e97b, #38f9d7)','linear-gradient(135deg, #fa709a, #fee140)','linear-gradient(135deg, #a18cd1, #fbc2eb)','linear-gradient(135deg, #fc5c7d, #6a82fb)','linear-gradient(135deg, #11998e, #38ef7d)'];
            $avatarBg = $gradients[crc32($student->user->name) % count($gradients)];
            $photoUrl = $student->user->photo ? asset('storage/' . $student->user->photo) : null;
            $noPeserta = str_pad($student->id, 6, '0', STR_PAD_LEFT);
        ?>
        <div class="exam-card">
            
            <div class="exam-card-header">
                <?php if($school && $school->logo): ?>
                <img class="school-logo" src="<?php echo e(asset('storage/' . $school->logo)); ?>" alt="Logo">
                <?php else: ?>
                <div style="width:28px;height:28px;border-radius:50%;background:#ffffff30;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">🏫</div>
                <?php endif; ?>
                <div class="header-text">
                    <div class="school-name"><?php echo e($school->school_name ?? config('app.name')); ?></div>
                    <div class="exam-title"><?php echo e($exam->name); ?> — <?php echo e(strtoupper($exam->type)); ?> — <?php echo e($exam->subject->name ?? ''); ?></div>
                </div>
            </div>

            
            <div class="exam-card-body">
                <div class="exam-card-left">
                    <div class="card-label">Kartu Peserta Ujian</div>
                    <div class="data-row">
                        <span class="label">Nama</span>
                        <span class="value highlight"><?php echo e($student->user->name); ?></span>
                    </div>
                    <div class="data-row">
                        <span class="label">NIS</span>
                        <span class="value"><?php echo e($student->nis); ?></span>
                    </div>
                    <div class="data-row">
                        <span class="label">NISN</span>
                        <span class="value"><?php echo e($student->nisn ?? '-'); ?></span>
                    </div>
                    <div class="data-row">
                        <span class="label">Kelas</span>
                        <span class="value"><?php echo e($student->class->name ?? '-'); ?></span>
                    </div>
                    <div class="data-row">
                        <span class="label">Mata Uji</span>
                        <span class="value"><?php echo e($exam->subject->name ?? '-'); ?></span>
                    </div>
                </div>

                <div class="exam-card-center">
                    <div class="photo" style="background: <?php echo e($avatarBg); ?>;">
                        <?php if($photoUrl): ?>
                        <img src="<?php echo e($photoUrl); ?>" alt="Photo">
                        <?php else: ?>
                        <?php echo e($initial); ?>

                        <?php endif; ?>
                    </div>
                    <div class="no-peserta">
                        No. Peserta<br>
                        <span class="num"><?php echo e($noPeserta); ?></span>
                    </div>
                </div>

                <div class="exam-card-right">
                    <div class="schedule-row">
                        <div class="label">Tanggal</div>
                        <div class="value"><?php echo e($exam->date ? $exam->date->isoFormat('dddd, D MMMM Y') : '-'); ?></div>
                    </div>
                    <div class="schedule-row">
                        <div class="label">Jam</div>
                        <div class="value"><?php echo e($exam->start_time ? substr($exam->start_time, 0, 5) : '-'); ?> - <?php echo e($exam->end_time ? substr($exam->end_time, 0, 5) : '-'); ?> WIB</div>
                    </div>
                    <div class="schedule-row">
                        <div class="label">Ruangan</div>
                        <div class="value">______________</div>
                    </div>
                    <div class="schedule-row">
                        <div class="label">Status</div>
                        <div class="value" style="color: #059669;">Peserta</div>
                    </div>
                </div>
            </div>

            
            <div class="exam-card-footer">
                <div class="signature-col">
                    <span class="role">Pengawas</span>
                    <span class="line"></span>
                    <span class="name">( ______________ )</span>
                </div>
                <div class="barcode-area">
                    <div>
                        <div class="barcode-label">Kode Verifikasi</div>
                        <div class="barcode-text"><?php echo e($noPeserta); ?><?php echo e($exam->id); ?></div>
                    </div>
                </div>
                <div class="signature-col">
                    <span class="role">Peserta</span>
                    <span class="line"></span>
                    <span class="name">( ______________ )</span>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="footer-actions">
        <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\siakad\resources\views/admin/exams/id-card-print.blade.php ENDPATH**/ ?>