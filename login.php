<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';
$success = '';
$mode = isset($_GET['mode']) && $_GET['mode'] == 'register' ? 'register' : 'login';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = getCurrentLanguage() == 'vi' ? 'Vui lòng điền đầy đủ thông tin' : 'Please fill in all fields';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = getCurrentLanguage() == 'vi' ? 'Tên đăng nhập hoặc mật khẩu không đúng' : 'Invalid username or password';
        }
    }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = sanitize($_POST['reg_username']);
    $email = sanitize($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm_password = $_POST['reg_confirm_password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = getCurrentLanguage() == 'vi' ? 'Vui lòng điền đầy đủ thông tin' : 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = getCurrentLanguage() == 'vi' ? 'Mật khẩu xác nhận không khớp' : 'Passwords do not match';
    } else {
        $conn = getDBConnection();
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch()) {
            $error = getCurrentLanguage() == 'vi' ? 'Tên đăng nhập đã tồn tại' : 'Username already exists';
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'customer')");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => hashPassword($password)
            ]);
            $success = getCurrentLanguage() == 'vi' ? 'Đăng ký thành công! Vui lòng đăng nhập.' : 'Registration successful! Please login.';
            $mode = 'login';
        }
    }
}

$page_title = $mode == 'register' ? t('register') : t('login');
?>
<!DOCTYPE html>
<html class="light" lang="<?php echo getCurrentLanguage(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#0f6eeb",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101822",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <header class="w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="index.php" class="flex items-center gap-2">
                    <div class="text-primary">
                        <span class="material-symbols-outlined text-3xl">bolt</span>
                    </div>
                    <span class="text-xl font-bold tracking-tight">TechStore</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <a href="?lang=vi" class="text-sm font-medium <?php echo getCurrentLanguage() == 'vi' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">VI</a>
                <span class="text-slate-300">|</span>
                <a href="?lang=en" class="text-sm font-medium <?php echo getCurrentLanguage() == 'en' ? 'text-primary' : 'text-slate-400 hover:text-slate-600'; ?>">EN</a>
            </div>
        </div>
    </header>
    
    <!-- Main Content: Focused Auth Card -->
    <main class="flex-grow flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-[440px] bg-white dark:bg-slate-900 shadow-xl rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <!-- Hero Image / Header Area -->
            <div class="relative h-32 bg-primary/10 overflow-hidden">
                <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=800')] bg-center bg-cover"></div>
                <div class="relative h-full flex flex-col items-center justify-center p-4">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white"><?php echo t('welcome_back'); ?></h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm"><?php echo t('access_hub'); ?></p>
                </div>
            </div>
            
            <!-- Tab Switcher -->
            <div class="flex border-b border-slate-200 dark:border-slate-800">
                <button onclick="switchMode('login')" id="login-tab" class="flex-1 py-4 text-sm font-bold border-b-2 <?php echo $mode == 'login' ? 'border-primary text-primary' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'; ?> transition-all">
                    <?php echo t('login'); ?>
                </button>
                <button onclick="switchMode('register')" id="register-tab" class="flex-1 py-4 text-sm font-bold border-b-2 <?php echo $mode == 'register' ? 'border-primary text-primary' : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'; ?> transition-all">
                    <?php echo t('register'); ?>
                </button>
            </div>
            
            <?php if ($error): ?>
            <div class="mx-8 mt-6 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-600 dark:text-red-400">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="mx-8 mt-6 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-600 dark:text-green-400">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <div id="login-form" class="p-8 space-y-6" style="display: <?php echo $mode == 'login' ? 'block' : 'none'; ?>;">
                <form method="POST">
                    <div class="space-y-6">
                        <!-- Email/Username Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="username"><?php echo getCurrentLanguage() == 'vi' ? 'Tên đăng nhập' : 'Username'; ?></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                </div>
                                <input class="block w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-colors placeholder:text-slate-400 dark:placeholder:text-slate-500" 
                                       id="username" name="username" placeholder="<?php echo getCurrentLanguage() == 'vi' ? 'Nhập tên đăng nhập' : 'Enter username'; ?>" type="text" required/>
                            </div>
                        </div>
                        
                        <!-- Password Input -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300" for="password"><?php echo t('password'); ?></label>
                                <a class="text-xs font-semibold text-primary hover:underline" href="#"><?php echo t('forgot_password'); ?></a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input class="block w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-colors placeholder:text-slate-400 dark:placeholder:text-slate-500" 
                                       id="password" name="password" placeholder="••••••••" type="password" required/>
                            </div>
                        </div>
                        
                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input class="w-4 h-4 text-primary bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-700 rounded focus:ring-primary" 
                                   id="remember" name="remember" type="checkbox"/>
                            <label class="ml-2 text-sm text-slate-600 dark:text-slate-400" for="remember"><?php echo t('remember_me'); ?></label>
                        </div>
                        
                        <!-- Action Button -->
                        <button class="w-full bg-primary text-white font-bold py-3.5 rounded-lg shadow-md hover:bg-primary/90 transition-all flex items-center justify-center gap-2 group" type="submit" name="login">
                            <?php echo t('sign_in'); ?>
                            <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                    </div>
                </form>
                
                <div class="text-center text-sm text-slate-600 dark:text-slate-400">
                    <?php echo getCurrentLanguage() == 'vi' ? 'Thông tin test: admin / password123' : 'Test credentials: admin / password123'; ?>
                </div>
            </div>
            
            <!-- Register Form -->
            <div id="register-form" class="p-8 space-y-6" style="display: <?php echo $mode == 'register' ? 'block' : 'none'; ?>;">
                <form method="POST">
                    <div class="space-y-4">
                        <!-- Username -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo getCurrentLanguage() == 'vi' ? 'Tên đăng nhập' : 'Username'; ?></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                </div>
                                <input class="block w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-colors" 
                                       name="reg_username" type="text" required/>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo t('email'); ?></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">mail</span>
                                </div>
                                <input class="block w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-colors" 
                                       name="reg_email" type="email" required/>
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo t('password'); ?></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input class="block w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-colors" 
                                       name="reg_password" type="password" required/>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo t('confirm_password'); ?></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input class="block w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-colors" 
                                       name="reg_confirm_password" type="password" required/>
                            </div>
                        </div>
                        
                        <button class="w-full bg-primary text-white font-bold py-3.5 rounded-lg shadow-md hover:bg-primary/90 transition-all" type="submit" name="register">
                            <?php echo t('sign_up'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    
    <script>
        function switchMode(mode) {
            if (mode === 'login') {
                document.getElementById('login-form').style.display = 'block';
                document.getElementById('register-form').style.display = 'none';
                document.getElementById('login-tab').classList.add('border-primary', 'text-primary');
                document.getElementById('login-tab').classList.remove('border-transparent', 'text-slate-500');
                document.getElementById('register-tab').classList.remove('border-primary', 'text-primary');
                document.getElementById('register-tab').classList.add('border-transparent', 'text-slate-500');
            } else {
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('register-form').style.display = 'block';
                document.getElementById('register-tab').classList.add('border-primary', 'text-primary');
                document.getElementById('register-tab').classList.remove('border-transparent', 'text-slate-500');
                document.getElementById('login-tab').classList.remove('border-primary', 'text-primary');
                document.getElementById('login-tab').classList.add('border-transparent', 'text-slate-500');
            }
        }
    </script>
</body>
</html>
