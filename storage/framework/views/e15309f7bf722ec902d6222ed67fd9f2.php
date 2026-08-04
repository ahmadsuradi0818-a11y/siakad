<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="manifest" href="/manifest.json">

    <title><?php echo e(config('app.name', 'SIABEL')); ?> - <?php echo e($title ?? 'Dashboard'); ?></title>

    <script>
        (() => {
            'use strict';
            const STORAGE_KEY = 'lte-theme';
            let stored = null;
            try {
                stored = localStorage.getItem(STORAGE_KEY);
            } catch {}
            const prefersDark = globalThis.matchMedia('(prefers-color-scheme: dark)').matches;
            let resolved = 'light';
            if (stored === 'dark' || stored === 'light') {
                resolved = stored;
            } else if (prefersDark) {
                resolved = 'dark';
            }
            document.documentElement.setAttribute('data-bs-theme', resolved);
            document.documentElement.style.colorScheme = resolved;
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.table-responsive-card').forEach(function(table) {
            var headers = [];
            table.querySelectorAll('thead th').forEach(function(th) {
                headers.push(th.textContent.trim());
            });
            table.querySelectorAll('tbody tr').forEach(function(tr) {
                tr.querySelectorAll('td').forEach(function(td, i) {
                    if (headers[i]) td.setAttribute('data-label', headers[i]);
                });
            });
        });
    });
    </script>
    <style>
        body { background-color: #e8ecf4 !important; }
        .app-content { background-color: #e8ecf4 !important; }
        .app-content-header { background-color: #e8ecf4 !important; }
        .card { box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .main-footer { background-color: #e8ecf4 !important; }

        /* Responsive sidebar overlay on mobile */
        @media (max-width: 991.98px) {
            .app-sidebar {
                position: fixed !important;
                z-index: 1050 !important;
                height: 100% !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease !important;
                width: 280px !important;
            }
            .sidebar-open .app-sidebar {
                transform: translateX(0);
            }
            .sidebar-open .app-wrapper::before {
                content: '';
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 1049;
            }
            .main-header .navbar-nav:first-child {
                flex-direction: row !important;
            }
            .app-content {
                padding-top: 0 !important;
            }
            .content-header {
                padding: 12px 0.5rem !important;
            }
            .content-header .breadcrumb {
                font-size: 0.75rem !important;
            }
            .card-body {
                padding: 1rem !important;
            }
        }

        /* Responsive tables - collapse to cards on mobile */
        @media (max-width: 768px) {
            .table-responsive-card thead { display: none; }
            .table-responsive-card tbody tr {
                display: block; border: 1px solid #dee2e6; border-radius: 8px;
                padding: 10px 12px; margin-bottom: 8px; background: #fff;
            }
            .table-responsive-card tbody tr td {
                display: flex; justify-content: space-between; align-items: center;
                padding: 4px 0; border: none; gap: 8px;
                text-align: right !important;
            }
            .table-responsive-card tbody tr td::before {
                content: attr(data-label);
                font-weight: 600; font-size: 0.7rem; text-transform: uppercase;
                color: #6c757d; text-align: left; flex-shrink: 0;
            }
            .table-responsive-card tbody tr td.text-center {
                justify-content: space-between;
            }
            .table-responsive-card tbody tr td:empty { display: none; }
        }
        @media (max-width: 576px) {
            .table-col-nis { display: none !important; }
        }

        /* Small phones */
        @media (max-width: 576px) {
            .card-body {
                padding: 0.75rem !important;
            }
            .table {
                font-size: 0.75rem !important;
            }
            .btn-sm {
                font-size: 0.65rem !important;
                padding: 0.2rem 0.4rem !important;
            }
            h3.card-title {
                font-size: 0.9rem !important;
            }
            .content-header h1 {
                font-size: 1rem !important;
            }
            .small-box h3 {
                font-size: 1.2rem !important;
            }
            .small-box span {
                font-size: 0.7rem !important;
            }
            .brand-text {
                font-size: 0.9rem !important;
            }
        }
    </style>
</head>
<body class="sidebar-expand-lg">
    <?php $role = auth()->user()->role; ?>

    <div class="app-wrapper">
        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->renderWhen($role !== 'siswa', 'layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
        <?php echo $__env->renderWhen($role === 'siswa', 'layouts.sidebar-siswa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0"><?php echo e($title ?? 'Dashboard'); ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Home</a></li>
                                <li class="breadcrumb-item active"><?php echo e($title ?? 'Dashboard'); ?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>
        </main>

        <footer class="main-footer">
            
        </footer>
    </div>

    <?php if($role === 'siswa'): ?>
        <?php echo $__env->make('layouts.bottom-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow text-center" style="border-radius: 20px; border: none;">
                <div class="modal-body py-5">
                    <div class="mb-3" style="font-size: 4rem; color: #ef4444;">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="color: #1e293b;">Yakin ingin keluar?</h5>
                    <p class="text-muted mb-0 small">Anda akan kembali ke halaman login.</p>
                </div>
                <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-4" style="border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 12px; font-weight: 600;" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    <form id="logoutForm" method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-danger px-4 shadow-sm" style="border-radius: 12px; font-weight: 700;">
                            <i class="fas fa-sign-out-alt me-1"></i> Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        MathJax = {
            tex: {
                inlineMath: [['\\(', '\\)']],
                displayMath: [['\\[', '\\]']],
                processEscapes: true
            },
            options: {
                ignoreHtmlClass: 'no-mathjax',
                processHtmlClass: 'mathjax'
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js" id="MathJax-script" async></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\laragon\www\siakad\resources\views/layouts/app.blade.php ENDPATH**/ ?>