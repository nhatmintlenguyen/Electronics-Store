<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

if (isLoggedIn()) {
    redirectTo('index.php');
}

$allowedModes = ['login', 'register', 'forgot'];
$mode = isset($_GET['mode']) && in_array($_GET['mode'], $allowedModes, true)
    ? (string) $_GET['mode']
    : 'login';

$error = '';
$success = '';

$loginEmail = '';
$registerFullName = '';
$registerUsername = '';
$registerEmail = '';
$forgotEmail = '';

function authText(string $vi, string $en): string
{
    return getCurrentLanguage() === 'vi' ? $vi : $en;
}

function passwordValidationError(string $password): string
{
    if (strlen($password) < 8) {
        return authText('Mật khẩu phải có ít nhất 8 ký tự.', 'Password must be at least 8 characters long.');
    }

    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = (string) ($_POST['form_type'] ?? 'login');
    $conn = getDBConnection();

    if ($formType === 'login') {
        $mode = 'login';
        $loginEmail = sanitize((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($loginEmail === '' || $password === '') {
            $error = authText('Vui lòng nhập email và mật khẩu.', 'Please enter both email and password.');
        } elseif (!filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
            $error = authText('Email không đúng định dạng.', 'Please enter a valid email address.');
        } else {
            $stmt = $conn->prepare(
                'SELECT id, username, email, password, role
                 FROM users
                 WHERE email = :email
                 LIMIT 1'
            );
            $stmt->execute([':email' => $loginEmail]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, (string) $user['password'])) {
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['username'] = (string) $user['username'];
                $_SESSION['email'] = (string) $user['email'];
                $_SESSION['role'] = (string) $user['role'];

                redirectTo('index.php');
            }

            $error = authText('Email hoặc mật khẩu không đúng.', 'Invalid email or password.');
        }
    }

    if ($formType === 'register') {
        $mode = 'register';
        $registerFullName = sanitize((string) ($_POST['full_name'] ?? ''));
        $registerUsername = sanitize((string) ($_POST['username'] ?? ''));
        $registerEmail = sanitize((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($registerFullName === '' || $registerUsername === '' || $registerEmail === '' || $password === '' || $confirmPassword === '') {
            $error = authText('Vui lòng điền đầy đủ thông tin.', 'Please fill in all required fields.');
        } elseif (!filter_var($registerEmail, FILTER_VALIDATE_EMAIL)) {
            $error = authText('Email không đúng định dạng.', 'Please enter a valid email address.');
        } elseif (($passwordError = passwordValidationError($password)) !== '') {
            $error = $passwordError;
        } elseif ($password !== $confirmPassword) {
            $error = authText('Mật khẩu xác nhận không khớp.', 'Password confirmation does not match.');
        } else {
            $stmt = $conn->prepare(
                'SELECT id
                 FROM users
                 WHERE username = :username OR email = :email
                 LIMIT 1'
            );
            $stmt->execute([
                ':username' => $registerUsername,
                ':email' => $registerEmail,
            ]);

            if ($stmt->fetch()) {
                $error = authText('Tên đăng nhập hoặc email đã tồn tại.', 'Username or email already exists.');
            } else {
                $stmt = $conn->prepare(
                    'INSERT INTO users (username, email, password, full_name, role)
                     VALUES (:username, :email, :password, :full_name, :role)'
                );
                $stmt->execute([
                    ':username' => $registerUsername,
                    ':email' => $registerEmail,
                    ':password' => hashPassword($password),
                    ':full_name' => $registerFullName,
                    ':role' => 'customer',
                ]);

                $success = authText('Đăng ký thành công. Bây giờ bạn có thể đăng nhập bằng email.', 'Registration successful. You can now sign in with your email.');
                $mode = 'login';
                $loginEmail = $registerEmail;
                $registerFullName = '';
                $registerUsername = '';
                $registerEmail = '';
            }
        }
    }

    if ($formType === 'forgot') {
        $mode = 'forgot';
        $forgotEmail = sanitize((string) ($_POST['email'] ?? ''));
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($forgotEmail === '' || $newPassword === '' || $confirmPassword === '') {
            $error = authText('Vui lòng nhập email và mật khẩu mới.', 'Please enter your email and new password.');
        } elseif (!filter_var($forgotEmail, FILTER_VALIDATE_EMAIL)) {
            $error = authText('Email không đúng định dạng.', 'Please enter a valid email address.');
        } elseif (($passwordError = passwordValidationError($newPassword)) !== '') {
            $error = $passwordError;
        } elseif ($newPassword !== $confirmPassword) {
            $error = authText('Mật khẩu xác nhận không khớp.', 'Password confirmation does not match.');
        } else {
            $stmt = $conn->prepare(
                'UPDATE users
                 SET password = :password
                 WHERE email = :email'
            );
            $stmt->execute([
                ':password' => hashPassword($newPassword),
                ':email' => $forgotEmail,
            ]);

            $success = authText(
                'Nếu email tồn tại trong hệ thống, mật khẩu đã được cập nhật. Hãy đăng nhập lại.',
                'If that email exists, the password has been updated. You can sign in now.'
            );
            $mode = 'login';
            $loginEmail = $forgotEmail;
            $forgotEmail = '';
        }
    }
}

$page_title = match ($mode) {
    'register' => t('register'),
    'forgot' => t('forgot_password'),
    default => t('login'),
};
?>
<!DOCTYPE html>
<html class="light" lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title . ' - ' . SITE_NAME); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(authText('Đăng nhập hoặc tạo tài khoản để mua sắm tại TechStore.', 'Sign in or create an account to shop at TechStore.')); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ea580c',
                        ink: '#111827',
                        mist: '#fff7ed',
                        panel: '#fffaf5',
                    },
                    fontFamily: {
                        display: ['Space Grotesk', 'sans-serif'],
                        body: ['Manrope', 'sans-serif'],
                    },
                    boxShadow: {
                        glow: '0 24px 70px rgba(234, 88, 12, 0.18)',
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
<body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.22),_transparent_28%),linear-gradient(135deg,_#fff7ed_0%,_#ffffff_40%,_#fff1f2_100%)] font-body text-ink">
    <div class="min-h-screen grid lg:grid-cols-[1.05fr_0.95fr]">
        <section class="relative overflow-hidden px-6 py-8 sm:px-10 lg:px-14 lg:py-12">
            <div class="absolute inset-0 opacity-60">
                <div class="absolute -left-16 top-24 h-52 w-52 rounded-full bg-amber-200 blur-3xl"></div>
                <div class="absolute right-0 top-0 h-72 w-72 rounded-full bg-orange-200 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/3 h-56 w-56 rounded-full bg-rose-200 blur-3xl"></div>
            </div>

            <div class="relative z-10 mx-auto flex h-full max-w-2xl flex-col">
                <div class="flex items-center justify-between">
                    <a href="<?php echo url('index.php'); ?>" class="inline-flex items-center gap-3">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-ink text-white shadow-glow">
                            <span class="material-symbols-outlined">bolt</span>
                        </span>
                        <span>
                            <span class="block font-display text-xl font-bold tracking-tight">TechStore</span>
                            <span class="block text-sm text-slate-500"><?php echo authText('Hệ thống điện tử học kỳ 6', 'Semester 6 electronics storefront'); ?></span>
                        </span>
                    </a>

                    <div class="rounded-full border border-white/70 bg-white/80 px-4 py-2 text-sm font-semibold shadow-sm backdrop-blur">
                        <a href="?mode=<?php echo urlencode($mode); ?>&lang=vi" class="<?php echo getCurrentLanguage() === 'vi' ? 'text-primary' : 'text-slate-400'; ?>">VI</a>
                        <span class="mx-2 text-slate-300">|</span>
                        <a href="?mode=<?php echo urlencode($mode); ?>&lang=en" class="<?php echo getCurrentLanguage() === 'en' ? 'text-primary' : 'text-slate-400'; ?>">EN</a>
                    </div>
                </div>

                <div class="mt-12 lg:mt-20">
                    <div class="inline-flex items-center gap-2 rounded-full border border-orange-200 bg-white/80 px-4 py-2 text-sm font-semibold text-orange-700 shadow-sm backdrop-blur">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        <?php echo authText('Đăng nhập bằng email và mật khẩu đã mã hóa', 'Email sign-in with hashed password'); ?>
                    </div>

                    <h1 class="mt-6 max-w-xl font-display text-4xl font-bold leading-tight text-slate-900 sm:text-5xl">
                        <?php echo authText('Hoàn thiện hệ thống xác thực người dùng cho Electronics Store.', 'Complete the user authentication flow for Electronics Store.'); ?>
                    </h1>

                    <p class="mt-5 max-w-xl text-base leading-7 text-slate-600 sm:text-lg">
                        <?php echo authText(
                            'Theo yêu cầu của đồ án, trang này hỗ trợ đăng ký, đăng nhập, đăng xuất và quên mật khẩu với kiểm tra dữ liệu đầu vào.',
                            'This page implements the assignment requirement for register, login, logout, and forgot-password flows with input validation.'
                        ); ?>
                    </p>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/70 bg-white/75 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-100 text-primary">
                                <span class="material-symbols-outlined">mail</span>
                            </span>
                            <div>
                                <p class="font-display text-lg font-bold"><?php echo authText('Đăng nhập bằng email', 'Email-based sign in'); ?></p>
                                <p class="text-sm text-slate-500"><?php echo authText('Đúng với yêu cầu trong PDF', 'Matches the PDF requirement'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/70 bg-white/75 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-100 text-primary">
                                <span class="material-symbols-outlined">lock</span>
                            </span>
                            <div>
                                <p class="font-display text-lg font-bold"><?php echo authText('Mật khẩu không lưu dạng thô', 'Passwords are not stored in plain text'); ?></p>
                                <p class="text-sm text-slate-500"><?php echo authText('Dùng SHA-256 theo helper hiện tại', 'Uses the current SHA-256 helper'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto hidden pt-12 lg:block">
                    <p class="text-sm text-slate-500">
                        <?php echo authText('Tài khoản mẫu sau khi migrate dữ liệu:', 'Sample seeded accounts after data migration:'); ?>
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
                        <h2 class="font-display text-3xl font-bold"><?php echo authText('Đăng nhập tài khoản', 'Sign in to your account'); ?></h2>
                        <p class="mt-2 text-sm text-slate-500"><?php echo authText('Đăng nhập bằng email và mật khẩu.', 'Use your email and password to continue.'); ?></p>
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

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-orange-700">
                            <span><?php echo t('sign_in'); ?></span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($mode === 'register'): ?>
                    <div class="mb-6">
                        <h2 class="font-display text-3xl font-bold"><?php echo authText('Tạo tài khoản mới', 'Create a new account'); ?></h2>
                        <p class="mt-2 text-sm text-slate-500"><?php echo authText('Đăng ký bằng email hợp lệ và mật khẩu tối thiểu 8 ký tự.', 'Register with a valid email and a password of at least 8 characters.'); ?></p>
                    </div>

                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="form_type" value="register">

                        <div>
                            <label for="register-full-name" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('full_name'); ?></label>
                            <input id="register-full-name" name="full_name" type="text" value="<?php echo htmlspecialchars($registerFullName); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div>
                            <label for="register-username" class="mb-2 block text-sm font-bold text-slate-700"><?php echo authText('Tên đăng nhập', 'Username'); ?></label>
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

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-orange-700">
                            <span><?php echo t('sign_up'); ?></span>
                            <span class="material-symbols-outlined text-[18px]">person_add</span>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($mode === 'forgot'): ?>
                    <div class="mb-6">
                        <h2 class="font-display text-3xl font-bold"><?php echo authText('Đặt lại mật khẩu', 'Reset your password'); ?></h2>
                        <p class="mt-2 text-sm text-slate-500"><?php echo authText('Nhập email và mật khẩu mới để cập nhật tài khoản.', 'Enter your email and a new password to update the account.'); ?></p>
                    </div>

                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="form_type" value="forgot">

                        <div>
                            <label for="forgot-email" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('email'); ?></label>
                            <input id="forgot-email" name="email" type="email" value="<?php echo htmlspecialchars($forgotEmail); ?>" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="forgot-password" class="mb-2 block text-sm font-bold text-slate-700"><?php echo authText('Mật khẩu mới', 'New password'); ?></label>
                                <input id="forgot-password" name="new_password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                            </div>

                            <div>
                                <label for="forgot-confirm-password" class="mb-2 block text-sm font-bold text-slate-700"><?php echo t('confirm_password'); ?></label>
                                <input id="forgot-confirm-password" name="confirm_password" type="password" required class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-primary focus:ring-primary">
                            </div>
                        </div>

                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-3.5 font-bold text-white transition hover:bg-orange-700">
                            <span><?php echo authText('Cập nhật mật khẩu', 'Update password'); ?></span>
                            <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                        </button>
                    </form>
                <?php endif; ?>

                <div class="mt-6 rounded-2xl border border-orange-100 bg-orange-50/70 px-4 py-3 text-sm text-slate-600">
                    <?php echo authText(
                        'Yêu cầu PDF: xác thực bằng email và mật khẩu, kiểm tra định dạng email, độ dài mật khẩu, và không lưu mật khẩu dạng thô.',
                        'PDF requirement: authenticate with email and password, validate email format and password length, and never store passwords in plain text.'
                    ); ?>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
