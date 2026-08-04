<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'SIAKAD')); ?> — Sistem Informasi Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .illustration-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-16px) rotate(2deg); }
        }
        .float-anim { animation: float 6s ease-in-out infinite; }
        .float-anim-2 { animation: float 8s ease-in-out infinite reverse; }
        .float-anim-3 { animation: float 7s ease-in-out 1s infinite; }
    </style>
</head>
<body>

    <!-- Background blobs -->
    <div class="illustration-blob w-72 h-72 bg-indigo-200/40 top-[-5%] left-[-3%]"></div>
    <div class="illustration-blob w-96 h-96 bg-amber-200/30 bottom-[-8%] right-[-5%]"></div>
    <div class="illustration-blob w-60 h-60 bg-purple-200/30 top-[40%] right-[30%]"></div>

    <div class="relative w-full max-w-6xl mx-4 my-8 bg-white rounded-3xl shadow-xl shadow-indigo-500/5 border border-gray-100 overflow-hidden">
        <div class="grid lg:grid-cols-2 min-h-[600px]">

            <!-- LEFT — Illustration & Branding -->
            <div class="relative bg-gradient-to-br from-indigo-50 via-white to-amber-50 p-10 lg:p-14 flex flex-col justify-between">
                <!-- Decorative dots -->
                <div class="absolute top-8 right-8 grid grid-cols-4 gap-2 opacity-20">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                </div>

                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white text-sm shadow-lg shadow-indigo-200">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold text-gray-900 tracking-tight"><?php echo e(config('app.name', 'SIAKAD')); ?></h1>
                        <p class="text-[0.6rem] font-semibold tracking-[2px] uppercase text-indigo-400">Sistem Informasi Akademik Belajar</p>
                    </div>
                </div>

                <!-- Illustration -->
                <div class="flex-1 flex items-center justify-center py-8">
                    <div class="relative w-full max-w-sm mx-auto">
                        <!-- Floating elements -->
                        <div class="float-anim absolute -top-4 -right-4 w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-200/30">
                            <i class="fa-solid fa-pen-to-square text-amber-500 text-xl"></i>
                        </div>
                        <div class="float-anim-2 absolute -bottom-3 -left-3 w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200/30">
                            <i class="fa-solid fa-book-open text-indigo-500 text-lg"></i>
                        </div>
                        <div class="float-anim-3 absolute top-[30%] -right-6 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center shadow-lg shadow-purple-200/30">
                            <i class="fa-solid fa-users text-purple-500 text-base"></i>
                        </div>

                        <!-- Main illustration -- laptop + dashboard -->
                        <div class="relative">
                            <!-- Laptop base -->
                            <div class="relative mx-auto max-w-[280px]">
                                <!-- Screen -->
                                <div class="bg-gray-900 rounded-xl p-2 shadow-2xl">
                                    <div class="bg-white rounded-lg overflow-hidden">
                                        <!-- Browser bar -->
                                        <div class="flex items-center gap-1.5 px-3 py-2 bg-gray-50 border-b border-gray-100">
                                            <div class="w-2 h-2 rounded-full bg-red-300"></div>
                                            <div class="w-2 h-2 rounded-full bg-amber-300"></div>
                                            <div class="w-2 h-2 rounded-full bg-green-300"></div>
                                            <div class="ml-2 h-2 w-20 rounded bg-gray-100"></div>
                                        </div>
                                        <!-- Screen content -->
                                        <div class="p-3 space-y-2.5">
                                            <div class="h-2 w-3/4 rounded bg-indigo-100"></div>
                                            <div class="h-2 w-1/2 rounded bg-gray-100"></div>
                                            <div class="flex gap-2">
                                                <div class="h-16 w-1/3 rounded-lg bg-indigo-50 border border-indigo-100 p-2">
                                                    <div class="h-1.5 w-3/4 rounded bg-indigo-200"></div>
                                                    <div class="h-1.5 w-1/2 rounded bg-indigo-100 mt-1.5"></div>
                                                </div>
                                                <div class="h-16 w-1/3 rounded-lg bg-amber-50 border border-amber-100 p-2">
                                                    <div class="h-1.5 w-3/4 rounded bg-amber-200"></div>
                                                    <div class="h-1.5 w-1/2 rounded bg-amber-100 mt-1.5"></div>
                                                </div>
                                                <div class="h-16 w-1/3 rounded-lg bg-purple-50 border border-purple-100 p-2">
                                                    <div class="h-1.5 w-3/4 rounded bg-purple-200"></div>
                                                    <div class="h-1.5 w-1/2 rounded bg-purple-100 mt-1.5"></div>
                                                </div>
                                            </div>
                                            <div class="h-2 w-1/3 rounded bg-gray-100"></div>
                                            <div class="h-6 w-full rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Keyboard -->
                                <div class="mx-auto mt-[-2px] w-[90%] h-3 bg-gray-200 rounded-b-lg"></div>
                                <!-- Trackpad -->
                                <div class="mx-auto mt-0.5 w-[30%] h-1.5 bg-gray-300 rounded-b-md"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tagline -->
                <div>
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-gray-900 leading-tight">
                        Kelola Akademik<br>
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Semakin Mudah</span>
                    </h2>
                    <p class="text-gray-400 text-sm mt-2 leading-relaxed max-w-sm">
                        Platform digital terpadu untuk pembelajaran, ujian, penilaian, dan administrasi sekolah.
                    </p>
                    <div class="flex items-center gap-6 mt-5 text-[0.6rem] font-semibold tracking-wider uppercase text-gray-400">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-check-circle text-green-400"></i>  Materi</span>
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-check-circle text-green-400"></i>  Ujian</span>
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-check-circle text-green-400"></i>  Presensi</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT — Login Form -->
            <div class="p-10 lg:p-14 flex flex-col justify-center bg-white">
                <?php if(auth()->guard()->check()): ?>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-2xl text-indigo-500">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Sudah Masuk</h2>
                        <p class="text-gray-400 text-sm mt-1">Anda sudah terautentikasi</p>
                        <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                            <i class="fa-solid fa-th-large"></i>
                            Buka Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    <div class="max-w-sm mx-auto w-full">
                        <div class="text-center mb-8">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 flex items-center justify-center text-xl text-indigo-500">
                                <i class="fa-solid fa-user-astronaut"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Selamat Datang</h2>
                            <p class="text-gray-400 text-sm mt-1">Silakan masuk ke akun Anda</p>
                        </div>

                        <?php if(session('status')): ?>
                        <div class="mb-5 px-4 py-3 rounded-xl flex items-center gap-3 text-sm bg-emerald-50 border border-emerald-100 text-emerald-600">
                            <i class="fa-solid fa-check-circle"></i> <?php echo e(session('status')); ?>

                        </div>
                        <?php endif; ?>
                        <?php if($errors->any()): ?>
                        <div class="mb-5 px-4 py-3 rounded-xl flex items-center gap-3 text-sm bg-red-50 border border-red-100 text-red-500">
                            <i class="fa-solid fa-exclamation-circle"></i> Email atau password salah.
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5 tracking-wide uppercase">Alamat Email</label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 focus-within:border-indigo-300 focus-within:bg-white focus-within:shadow-sm transition-all duration-200">
                                    <i class="fa-solid fa-envelope text-gray-300 text-sm"></i>
                                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username"
                                        class="w-full bg-transparent text-sm text-gray-700 outline-none placeholder:text-gray-300"
                                        placeholder="contoh@email.com">
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1.5 tracking-wide uppercase">Kata Sandi</label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 focus-within:border-indigo-300 focus-within:bg-white focus-within:shadow-sm transition-all duration-200">
                                    <i class="fa-solid fa-lock text-gray-300 text-sm"></i>
                                    <input type="password" name="password" required autocomplete="current-password"
                                        class="w-full bg-transparent text-sm text-gray-700 outline-none placeholder:text-gray-300"
                                        placeholder="••••••••">
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-gray-400 text-xs">Ingat saya</span>
                                </label>
                                <?php if(Route::has('password.request')): ?>
                                <a href="<?php echo e(route('password.request')); ?>" class="text-xs font-medium text-indigo-400 hover:text-indigo-600 transition-colors">Lupa Password?</a>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold shadow-lg shadow-indigo-200 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                                <span>Masuk</span>
                            </button>
                        </form>

                        <p class="fade-in-3 text-center text-xs text-gray-400 mt-5">
                                <a href="<?php echo e(route('register.student')); ?>" class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-user-plus text-[0.6rem]"></i>
                                    Daftar Siswa Baru
                                </a>
                            </p>                        

                        <?php if(isset($demoAdmin) || isset($demoGuru) || isset($demoSiswa)): ?>
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <p class="text-[0.55rem] font-semibold tracking-[2px] uppercase text-gray-300 text-center mb-3">Akses Demo Cepat</p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <?php if(isset($demoAdmin)): ?>
                                <button onclick="fillLogin('<?php echo e($demoAdmin->email); ?>','password')" class="px-4 py-2 rounded-lg text-[0.65rem] font-medium bg-indigo-50 text-indigo-400 border border-indigo-100 hover:bg-indigo-100 transition-colors">
                                    <i class="fa-solid fa-shield-alt mr-1"></i> Admin
                                </button>
                                <?php endif; ?>
                                <?php if(isset($demoGuru)): ?>
                                <button onclick="fillLogin('<?php echo e($demoGuru->email); ?>','password')" class="px-4 py-2 rounded-lg text-[0.65rem] font-medium bg-amber-50 text-amber-500 border border-amber-100 hover:bg-amber-100 transition-colors">
                                    <i class="fa-solid fa-chalkboard mr-1"></i> Guru
                                </button>
                                <?php endif; ?>
                                <?php if(isset($demoSiswa)): ?>
                                <button onclick="fillLogin('<?php echo e($demoSiswa->email); ?>','password')" class="px-4 py-2 rounded-lg text-[0.65rem] font-medium bg-purple-50 text-purple-400 border border-purple-100 hover:bg-purple-100 transition-colors">
                                    <i class="fa-solid fa-user-graduate mr-1"></i> Siswa
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
    function fillLogin(email, password) {
        const emailInput = document.querySelector('input[name="email"]');
        const passInput = document.querySelector('input[name="password"]');
        if (emailInput) { emailInput.value = email; }
        if (passInput) { passInput.value = password; }
    }
    </script>
</body>
</html><?php /**PATH C:\laragon\www\siakad\resources\views/landing.blade.php ENDPATH**/ ?>