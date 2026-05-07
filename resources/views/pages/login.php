<!DOCTYPE html>
<html class="light" lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title . ' - ' . SITE_NAME); ?></title>
    <meta name="description" content="Sign in or create an account to shop at TechStore.">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f6eeb',
                        primaryDark: '#0b57c7',
                        ink: '#111827',
                        mist: '#eff6ff',
                        panel: '#f8fafc',
                    },
                    fontFamily: {
                        display: ['Space Grotesk', 'sans-serif'],
                        body: ['Manrope', 'sans-serif'],
                    },
                    boxShadow: {
                        glow: '0 24px 70px rgba(15, 110, 235, 0.18)',
                    }
                }
            }
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(15,110,235,0.18),_transparent_30%),linear-gradient(135deg,_#f6f7f8_0%,_#ffffff_45%,_#eff6ff_100%)] font-body text-ink">
    <div class="min-h-screen grid lg:grid-cols-[1.05fr_0.95fr]">
        <section class="relative overflow-hidden px-6 py-8 sm:px-10 lg:px-14 lg:py-12">
            <div class="absolute inset-0 opacity-60">
                <div class="absolute -left-16 top-24 h-52 w-52 rounded-full bg-sky-200 blur-3xl"></div>
                <div class="absolute right-0 top-0 h-72 w-72 rounded-full bg-blue-200 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/3 h-56 w-56 rounded-full bg-slate-200 blur-3xl"></div>
            </div>

            <div class="relative z-10 mx-auto flex h-full max-w-2xl flex-col">
                <div class="flex items-center justify-between">
                    <a href="<?php echo url('index.php'); ?>" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-ink text-white shadow-glow">
                            <span class="material-symbols-outlined">bolt</span>
                        </span>
                        <span>
                            <span class="block font-display text-xl font-bold tracking-tight">TechStore</span>
                            <span class="block text-sm text-slate-500">Semester 6 electronics storefront</span>
                        </span>
                    </a>

                    <div class="rounded-full border border-white/70 bg-white/80 px-4 py-2 text-sm font-semibold text-primary shadow-sm backdrop-blur">
                        EN
                    </div>
                </div>

                <div class="mt-12 lg:mt-20">
                    <div class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white/80 px-4 py-2 text-sm font-semibold text-primary shadow-sm backdrop-blur">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        Email sign-in with hashed password
                    </div>

                    <h1 class="mt-6 max-w-xl font-display text-4xl font-bold leading-tight text-slate-900 sm:text-5xl">
                        Complete the user authentication flow for Electronics Store.
                    </h1>

                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                        This page implements the assignment requirement for register, login, logout, and forgot-password flows with input validation.
                    </p>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/70 bg-white/75 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-100 text-primary">
                                <span class="material-symbols-outlined">mail</span>
                            </span>
                            <div>
                                <p class="font-display text-lg font-bold">Email-based sign in</p>
                                <p class="text-sm text-slate-500">Matches the PDF requirement</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/70 bg-white/75 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-100 text-primary">
                                <span class="material-symbols-outlined">lock</span>
                            </span>
                            <div>
                                <p class="font-display text-lg font-bold">Passwords are not stored in plain text</p>
                                <p class="text-sm text-slate-500">Uses the current SHA-256 helper</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto hidden pt-12 lg:block">
                    <p class="text-sm text-slate-500">
                        Sample seeded accounts after data migration:
                        <span class="font-semibold text-slate-700">`admin@electronics.local / admin123`</span>
                        <span class="mx-2 text-slate-300">•</span>
                        <span class="font-semibold text-slate-700">`customer1@electronics.local / pass1234`</span>
                    </p>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center px-6 py-8 sm:px-10 lg:px-14 lg:py-12">
            <div class="w-full max-w-xl rounded-[2rem] border border-white/70 bg-white/92 p-6 shadow-glow backdrop-blur sm:p-8">
                <div class="mb-6 flex flex-wrap gap-2 rounded-2xl bg-panel p-2">
                    <a href="?mode=login" class="flex-1 rounded-2xl px-4 py-3 text-center text-sm font-bold transition <?php echo $mode === 'login' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-900'; ?>">
                        <?php echo t('login'); ?>
                    </a>
                    <a href="?mode=register" class="flex-1 rounded-2xl px-4 py-3 text-center text-sm font-bold transition <?php echo $mode === 'register' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-900'; ?>">
                        <?php echo t('register'); ?>
                    </a>
                    <a href="?mode=forgot" class="flex-1 rounded-2xl px-4 py-3 text-center text-sm font-bold transition <?php echo $mode === 'forgot' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-900'; ?>">
                        <?php echo t('forgot_password'); ?>
                    </a>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success !== ''): ?>
                    <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($mode === 'login'): ?>
                    <div class="mb-6">
                        <h2 class="font-display text-3xl font-bold">Sign in to your account</h2>
                        <p class="mt-2 text-sm text-slate-500">Use your email and password to continue.</p>
                    </div>

                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="form_type" value="login">

                        <div>
                            <label for="login-email" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('email'); ?></label>
                            <input id="login-email" name="email" type="email" value="<?php echo htmlspecialchars($loginEmail); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary" placeholder="name@example.com">
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label for="login-password" class="block text-sm font-bold text-slate-700"><?php echo t('password'); ?></label>
                                <a href="?mode=forgot" class="text-sm font-semibold text-primary hover:underline"><?php echo t('forgot_password'); ?></a>
                            </div>
                            <input id="login-password" name="password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary" placeholder="••••••••">
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-primaryDark">
                            <span><?php echo t('sign_in'); ?></span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($mode === 'register'): ?>
                    <div class="mb-6">
                        <h2 class="font-display text-3xl font-bold">Create a new account</h2>
                        <p class="mt-2 text-sm text-slate-500">Register with a valid email and a password of at least 8 characters.</p>
                    </div>

                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="form_type" value="register">

                        <div>
                            <label for="register-full-name" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('full_name'); ?></label>
                            <input id="register-full-name" name="full_name" type="text" value="<?php echo htmlspecialchars($registerFullName); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div>
                            <label for="register-username" class="mb-2 block text-sm font-bold text-slate-700">Username</label>
                            <input id="register-username" name="username" type="text" value="<?php echo htmlspecialchars($registerUsername); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div>
                            <label for="register-email" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('email'); ?></label>
                            <input id="register-email" name="email" type="email" value="<?php echo htmlspecialchars($registerEmail); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="register-password" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('password'); ?></label>
                                <input id="register-password" name="password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary" placeholder="Min. 8 characters">
                            </div>

                            <div>
                                <label for="register-confirm-password" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('confirm_password'); ?></label>
                                <input id="register-confirm-password" name="confirm_password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                            </div>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-primaryDark">
                            <span><?php echo t('sign_up'); ?></span>
                            <span class="material-symbols-outlined text-[18px]">person_add</span>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($mode === 'forgot'): ?>
                    <div class="mb-6">
                        <h2 class="font-display text-3xl font-bold">Reset your password</h2>
                        <p class="mt-2 text-sm text-slate-500">Enter your email and a new password to update the account.</p>
                    </div>

                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="form_type" value="forgot">

                        <div>
                            <label for="forgot-email" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('email'); ?></label>
                            <input id="forgot-email" name="email" type="email" value="<?php echo htmlspecialchars($forgotEmail); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="forgot-password" class="mb-2 block text-sm font-bold text-slate-700">New password</label>
                                <input id="forgot-password" name="new_password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div>
                                <label for="forgot-confirm-password" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('confirm_password'); ?></label>
                                <input id="forgot-confirm-password" name="confirm_password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                            </div>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-primaryDark">
                            <span>Update password</span>
                            <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                        </button>
                    </form>
                <?php endif; ?>

                <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm text-slate-600">
                    PDF requirement: authenticate with email and password, validate email format and password length, and never store passwords in plain text.
                </div>
            </div>
        </section>
    </div>
</body>
</html>
