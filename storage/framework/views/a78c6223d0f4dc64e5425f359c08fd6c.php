<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(config('app.name', 'SIAKAD')); ?> — Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        serif: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,800,900|plus-jakarta-sans:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        @keyframes floatSlow2 {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }
        @keyframes shimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInLeft {
            0% { opacity: 0; transform: translateX(-30px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        .float-1 { animation: floatSlow 6s ease-in-out infinite; }
        .float-2 { animation: floatSlow2 8s ease-in-out infinite; }
        .float-3 { animation: floatSlow 7s ease-in-out 1s infinite; }
        .fade-in { animation: fadeIn 0.8s ease-out forwards; }
        .fade-in-left { animation: fadeInLeft 0.8s ease-out 0.2s forwards; opacity: 0; }
        .fade-in-1 { animation: fadeIn 0.6s ease-out 0.1s forwards; opacity: 0; }
        .fade-in-2 { animation: fadeIn 0.6s ease-out 0.3s forwards; opacity: 0; }
        .fade-in-3 { animation: fadeIn 0.6s ease-out 0.5s forwards; opacity: 0; }
        .fade-in-4 { animation: fadeIn 0.6s ease-out 0.7s forwards; opacity: 0; }
        .shimmer-text {
            background: linear-gradient(90deg, #1e3a5f 0%, #3b82f6 50%, #1e3a5f 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s linear infinite;
        }
        .input-focus-ring:focus-within {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }
        .btn-primary {
            background: linear-gradient(135deg, #1e3a5f, #2563eb);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #15294a, #1d4ed8);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(30, 58, 95, 0.2);
        }
        @media (max-width: 1023px) {
            .login-left-illustration { display: none; }
        }
        @media (max-width: 576px) {
            .login-left { padding: 1.5rem !important; }
            .login-left h2 { font-size: 1.3rem !important; }
            .login-left p.text-sm { font-size: 0.75rem !important; }
            .login-form-input { padding: 0.65rem 0.75rem !important; font-size: 0.85rem !important; }
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen bg-[#f0f4f8]">

    <!-- Background pattern -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-bl from-[#e8eef7] to-transparent opacity-60"></div>
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-[#dbe6f5] opacity-30 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-[#e0eaf5] opacity-30 blur-3xl"></div>
        <div class="absolute top-1/3 left-1/4 w-2 h-2 rounded-full bg-blue-200 opacity-40"></div>
        <div class="absolute top-1/4 right-1/3 w-1.5 h-1.5 rounded-full bg-amber-200 opacity-30"></div>
        <div class="absolute bottom-1/3 right-1/4 w-1 h-1 rounded-full bg-blue-200 opacity-40"></div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center p-3 sm:p-6 lg:p-8">

        <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl shadow-blue-900/5 overflow-hidden">

            <div class="grid lg:grid-cols-5" style="min-height: 520px;">

                <!-- LEFT — Brand & Illustration (3/5 on large) -->
                <div class="lg:col-span-3 relative bg-gradient-to-br from-[#0f1f3d] via-[#162d50] to-[#1a3a5c] p-6 sm:p-10 lg:p-14 flex flex-col overflow-hidden">

                    <!-- Decorative overlay -->
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute top-[-20%] left-[-10%] w-[60%] h-[60%] rounded-full bg-[rgba(59,130,246,0.03)] blur-2xl"></div>
                        <div class="absolute bottom-[-15%] right-[-10%] w-[50%] h-[50%] rounded-full bg-[rgba(251,191,36,0.02)] blur-2xl"></div>
                        <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.02) 1px, transparent 1px); background-size: 24px 24px;"></div>
                    </div>

                    <!-- Brand -->
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center text-white text-base border border-white/5 shadow-lg">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white/90 tracking-tight"><?php echo e(config('app.name', 'SIABEL')); ?></h1>
                            <p class="text-[0.55rem] tracking-[2px] uppercase text-blue-200/40 mt-0.5">Sistem Informasi Akademik Belajar</p>
                        </div>
                    </div>

                    <!-- Illustration -->
                    <div class="relative z-10 flex-1 flex items-center justify-center py-6 lg:py-8">
                        <div class="relative w-full max-w-[340px] mx-auto">

                            <!-- Floating badges -->
                            <div class="float-1 absolute -top-3 -right-2 w-14 h-14 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/5 flex items-center justify-center shadow-xl">
                                <i class="fa-solid fa-star text-amber-300/60 text-lg"></i>
                            </div>
                            <div class="float-2 absolute -bottom-2 -left-2 w-12 h-12 bg-white/5 backdrop-blur-sm rounded-xl border border-white/5 flex items-center justify-center shadow-xl">
                                <i class="fa-solid fa-trophy text-amber-300/50 text-base"></i>
                            </div>
                            <div class="float-3 absolute top-1/3 -right-4 w-10 h-10 bg-white/5 backdrop-blur-sm rounded-xl border border-white/5 flex items-center justify-center shadow-xl">
                                <i class="fa-solid fa-medal text-blue-300/50 text-sm"></i>
                            </div>

                            <!-- Building illustration -->
                            <div class="relative">
                                <!-- School building -->
                                <svg viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-2xl">
                                    <!-- Ground -->
                                    <rect x="0" y="200" width="320" height="40" fill="rgba(255,255,255,0.02)"/>
                                    <path d="M0 210 Q40 205 80 210 Q120 215 160 210 Q200 205 240 210 Q280 215 320 210 V240 H0 Z" fill="rgba(255,255,255,0.01)"/>

                                    <!-- Main building body -->
                                    <rect x="60" y="70" width="200" height="130" rx="2" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.06)" stroke-width="0.5"/>

                                    <!-- Roof -->
                                    <path d="M50 75 L160 35 L270 75 Z" fill="rgba(255,255,255,0.03)" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/>

                                    <!-- Roof flag/pinnacle -->
                                    <line x1="160" y1="35" x2="160" y2="18" stroke="rgba(255,255,255,0.08)" stroke-width="1.5"/>
                                    <path d="M160 18 L178 24 L160 30 Z" fill="rgba(251,191,36,0.15)"/>

                                    <!-- Columns / entrance -->
                                    <rect x="135" y="130" width="12" height="70" fill="rgba(255,255,255,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="173" y="130" width="12" height="70" fill="rgba(255,255,255,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="148" y="155" width="24" height="45" rx="2" fill="rgba(59,130,246,0.04)" stroke="rgba(59,130,246,0.08)" stroke-width="0.5"/>
                                    <circle cx="167" cy="175" r="1.5" fill="rgba(251,191,36,0.15)"/>

                                    <!-- Windows row 1 -->
                                    <rect x="72" y="88" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="100" y="88" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="198" y="88" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="226" y="88" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>

                                    <!-- Windows row 2 -->
                                    <rect x="72" y="126" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="100" y="126" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="198" y="126" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="226" y="126" width="22" height="28" rx="1.5" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>

                                    <!-- Window crosses -->
                                    <line x1="83" y1="88" x2="83" y2="116" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="72" y1="102" x2="94" y2="102" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="111" y1="88" x2="111" y2="116" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="100" y1="102" x2="122" y2="102" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="209" y1="88" x2="209" y2="116" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="198" y1="102" x2="220" y2="102" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="237" y1="88" x2="237" y2="116" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <line x1="226" y1="102" x2="248" y2="102" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>

                                    <!-- Clock on building -->
                                    <circle cx="160" cy="120" r="8" fill="rgba(255,255,255,0.02)" stroke="rgba(255,255,255,0.04)" stroke-width="0.5"/>
                                    <line x1="160" y1="120" x2="160" y2="115" stroke="rgba(251,191,36,0.08)" stroke-width="1"/>
                                    <line x1="160" y1="120" x2="164" y2="120" stroke="rgba(251,191,36,0.08)" stroke-width="1"/>

                                    <!-- Steps -->
                                    <rect x="130" y="195" width="60" height="4" rx="1" fill="rgba(255,255,255,0.02)"/>
                                    <rect x="135" y="199" width="50" height="3" rx="1" fill="rgba(255,255,255,0.01)"/>

                                    <!-- Left wing -->
                                    <rect x="30" y="110" width="30" height="90" rx="1" fill="rgba(255,255,255,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="36" y="125" width="18" height="22" rx="1" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <rect x="36" y="155" width="18" height="22" rx="1" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>

                                    <!-- Right wing -->
                                    <rect x="260" y="110" width="30" height="90" rx="1" fill="rgba(255,255,255,0.02)" stroke="rgba(255,255,255,0.03)" stroke-width="0.3"/>
                                    <rect x="266" y="125" width="18" height="22" rx="1" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>
                                    <rect x="266" y="155" width="18" height="22" rx="1" fill="rgba(59,130,246,0.02)" stroke="rgba(255,255,255,0.02)" stroke-width="0.3"/>

                                    <!-- Banner -->
                                    <rect x="110" y="68" width="100" height="14" rx="2" fill="rgba(251,191,36,0.04)"/>
                                    <text x="160" y="79" font-family="serif" font-size="8" fill="rgba(251,191,36,0.2)" text-anchor="middle" font-weight="600">SIABEL</text>

                                    <!-- Trees -->
                                    <circle cx="18" cy="190" r="14" fill="rgba(16,185,129,0.02)" stroke="rgba(16,185,129,0.03)" stroke-width="0.3"/>
                                    <rect x="16" y="190" width="4" height="14" fill="rgba(255,255,255,0.01)"/>
                                    <circle cx="302" cy="188" r="12" fill="rgba(16,185,129,0.02)" stroke="rgba(16,185,129,0.03)" stroke-width="0.3"/>
                                    <rect x="300" y="188" width="4" height="12" fill="rgba(255,255,255,0.01)"/>

                                    <!-- Sun -->
                                    <circle cx="270" cy="42" r="16" fill="rgba(251,191,36,0.02)"/>
                                    <circle cx="270" cy="42" r="8" fill="rgba(251,191,36,0.04)"/>
                                    <line x1="270" y1="22" x2="270" y2="18" stroke="rgba(251,191,36,0.03)" stroke-width="1"/>
                                    <line x1="270" y1="62" x2="270" y2="66" stroke="rgba(251,191,36,0.03)" stroke-width="1"/>
                                    <line x1="250" y1="42" x2="246" y2="42" stroke="rgba(251,191,36,0.03)" stroke-width="1"/>
                                    <line x1="290" y1="42" x2="294" y2="42" stroke="rgba(251,191,36,0.03)" stroke-width="1"/>
                                    <line x1="256" y1="28" x2="253" y2="25" stroke="rgba(251,191,36,0.03)" stroke-width="1"/>
                                    <line x1="284" y1="56" x2="287" y2="59" stroke="rgba(251,191,36,0.03)" stroke-width="1"/>

                                    <!-- Birds -->
                                    <path d="M40 55 Q45 50 50 55" stroke="rgba(255,255,255,0.04)" stroke-width="0.5" fill="none"/>
                                    <path d="M55 48 Q60 43 65 48" stroke="rgba(255,255,255,0.04)" stroke-width="0.5" fill="none"/>
                                    <path d="M30 62 Q35 57 40 62" stroke="rgba(255,255,255,0.03)" stroke-width="0.5" fill="none"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Tagline -->
                    <div class="relative z-10">
                        <p class="font-serif text-[0.6rem] tracking-[4px] uppercase text-blue-200/30 mb-2">—  Cerdas · Berkarakter · Mandiri  —</p>
                        <h2 class="font-serif text-2xl lg:text-3xl font-bold text-white leading-tight">
                            Cetak Generasi<br>
                            <span class="text-amber-300/80">Penerus Bangsa</span>
                        </h2>
                        <p class="text-blue-200/30 text-sm mt-2 leading-relaxed max-w-sm font-light">
                            Platform akademik digital untuk mewujudkan pendidikan berkualitas di era modern.
                        </p>
                    </div>
                </div>

                <!-- RIGHT — Login Form (2/5 on large) -->
                <div class="lg:col-span-2 p-5 sm:p-8 lg:p-12 flex flex-col justify-center bg-white">
                    <div class="max-w-sm mx-auto w-full">

                        <!-- Mobile header -->
                        <div class="lg:hidden flex items-center justify-center gap-3 mb-8 pb-6 border-b border-gray-100">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1e3a5f] to-[#2563eb] flex items-center justify-center text-white text-sm shadow-lg">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900"><?php echo e(config('app.name', 'SIABEL')); ?></p>
                                <p class="text-[0.5rem] tracking-[2px] uppercase text-gray-400">Sistem Informasi Akademik Belajar</p>
                            </div>
                        </div>

                        <!-- Form content -->
                        <div class="fade-in">
                            <div class="text-center mb-8">
                                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 flex items-center justify-center text-xl text-blue-600">
                                    <i class="fa-solid fa-user-astronaut"></i>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900">Selamat Datang</h2>
                                <p class="text-gray-400 text-sm mt-1">Silakan masuk ke akun Anda</p>
                            </div>

                            <?php if(session('status')): ?>
                            <div class="fade-in-1 mb-5 px-4 py-3.5 rounded-xl flex items-center gap-3 text-sm bg-emerald-50 border border-emerald-100 text-emerald-600">
                                <i class="fa-solid fa-check-circle text-emerald-400"></i>
                                <span><?php echo e(session('status')); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if($errors->any()): ?>
                            <div class="fade-in-1 mb-5 px-4 py-3.5 rounded-xl flex items-center gap-3 text-sm bg-red-50 border border-red-100 text-red-500">
                                <i class="fa-solid fa-exclamation-circle text-red-400"></i>
                                <span>Email atau kata sandi salah.</span>
                            </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5 fade-in-2">
                                <?php echo csrf_field(); ?>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 tracking-wide">Email</label>
                                    <div class="input-focus-ring flex items-center gap-3 px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50/50 focus-within:border-blue-300 focus-within:bg-white transition-all duration-200">
                                        <i class="fa-solid fa-envelope text-gray-300 text-sm"></i>
                                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username"
                                            class="w-full bg-transparent text-sm text-gray-700 outline-none placeholder:text-gray-300"
                                            placeholder="nama@sekolah.ac.id">
                                    </div>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-400 mt-1 ml-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 tracking-wide">Kata Sandi</label>
                                    <div class="input-focus-ring flex items-center gap-3 px-4 py-3.5 rounded-xl border border-gray-200 bg-gray-50/50 focus-within:border-blue-300 focus-within:bg-white transition-all duration-200">
                                        <i class="fa-solid fa-lock text-gray-300 text-sm"></i>
                                        <input type="password" name="password" required autocomplete="current-password"
                                            class="w-full bg-transparent text-sm text-gray-700 outline-none placeholder:text-gray-300"
                                            placeholder="••••••••">
                                    </div>
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs text-red-400 mt-1 ml-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>

                                <div class="flex items-center justify-between">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-xs text-gray-400 group-hover:text-gray-500 transition-colors">Ingat saya</span>
                                    </label>
                                    <?php if(Route::has('password.request')): ?>
                                    <a href="<?php echo e(route('password.request')); ?>" class="text-xs font-medium text-blue-500 hover:text-blue-700 transition-colors">Lupa password?</a>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn-primary w-full py-3.5 rounded-xl text-sm font-bold text-white shadow-lg shadow-blue-200 flex items-center justify-center gap-2">
                                    <span>Masuk</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </button>
                            </form>

                            <p class="fade-in-3 text-center text-xs text-gray-400 mt-5">
                                <a href="<?php echo e(route('register.student')); ?>" class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-user-plus text-[0.6rem]"></i>
                                    Daftar Siswa Baru
                                </a>
                            </p>
                        </div>

                        <!-- Demo accounts -->
                        <?php if(isset($demoAdmin) || isset($demoGuru) || isset($demoSiswa)): ?>
                        <div class="fade-in-4 mt-8 pt-6 border-t border-gray-100">
                            <p class="text-[0.5rem] font-semibold tracking-[2.5px] uppercase text-gray-300 text-center mb-4">—  Akses Demo  —</p>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <?php if(isset($demoAdmin)): ?>
                                <button onclick="fillLogin('<?php echo e($demoAdmin->email); ?>','password')"
                                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[0.6rem] font-medium bg-blue-50 text-blue-500 border border-blue-100 hover:bg-blue-100 hover:border-blue-200 transition-all duration-200">
                                    <i class="fa-solid fa-shield-alt text-[0.5rem]"></i> Admin
                                </button>
                                <?php endif; ?>
                                <?php if(isset($demoGuru)): ?>
                                <button onclick="fillLogin('<?php echo e($demoGuru->email); ?>','password')"
                                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[0.6rem] font-medium bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 hover:border-amber-200 transition-all duration-200">
                                    <i class="fa-solid fa-chalkboard text-[0.5rem]"></i> Guru
                                </button>
                                <?php endif; ?>
                                <?php if(isset($demoSiswa)): ?>
                                <button onclick="fillLogin('<?php echo e($demoSiswa->email); ?>','password')"
                                    class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-[0.6rem] font-medium bg-emerald-50 text-emerald-500 border border-emerald-100 hover:bg-emerald-100 hover:border-emerald-200 transition-all duration-200">
                                    <i class="fa-solid fa-user-graduate text-[0.5rem]"></i> Siswa
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Footer -->
                        <p class="fade-in-4 text-center text-[0.5rem] mt-6 tracking-[1.5px] text-gray-300">
                            &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'SIAKAD')); ?> @ Versi. 1.0.0
                        </p>.
                    </div>
                </div>

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
</html><?php /**PATH C:\laragon\www\siakad\resources\views/auth/login.blade.php ENDPATH**/ ?>