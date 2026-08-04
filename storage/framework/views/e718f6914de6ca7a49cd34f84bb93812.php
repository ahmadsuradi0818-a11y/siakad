<?php
    $user = Auth::user();
?>
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <?php if($user->photo_url): ?>
                    <img src="<?php echo e($user->photo_url); ?>" alt=""
                        style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                    <?php endif; ?>
                    <span class="d-none d-md-inline ms-1"><?php echo e($user->name); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <li class="user-header text-bg-primary">
                        <?php if($user->photo_url): ?>
                        <img src="<?php echo e($user->photo_url); ?>" alt=""
                            style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                        <?php else: ?>
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                        <?php endif; ?>
                        <p>
                            <?php echo e($user->name); ?>

                            <small><?php echo e($user->email); ?></small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-person me-1"></i>Profil
                        </a>
                        <button type="button" class="btn btn-outline-danger float-end" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="bi bi-box-arrow-right me-1"></i>Keluar
                        </button>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<?php /**PATH C:\laragon\www\siakad\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>