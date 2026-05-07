<?php
declare(strict_types=1);

class AuthController
{
    public function login(): void
    {
        if (isLoggedIn()) {
            redirectTo('index.php');
        }

        $allowedModes = ['login', 'register', 'forgot'];
        $mode = isset($_GET['mode']) && in_array($_GET['mode'], $allowedModes, true)
            ? (string) $_GET['mode']
            : 'login';

        $data = [
            'error' => '',
            'success' => '',
            'loginEmail' => '',
            'registerFullName' => '',
            'registerUsername' => '',
            'registerEmail' => '',
            'forgotEmail' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mode = $this->handlePost($mode, $data);
        }

        view('pages/login.php', array_merge($data, [
            'mode' => $mode,
            'page_title' => match ($mode) {
                'register' => t('register'),
                'forgot' => t('forgot_password'),
                default => t('login'),
            },
        ]), null);
    }

    public function logout(): void
    {
        session_destroy();
        redirectTo('');
    }

    private function handlePost(string $mode, array &$data): string
    {
        $formType = (string) ($_POST['form_type'] ?? 'login');
        $conn = getDBConnection();

        if ($formType === 'login') {
            return $this->handleLogin($conn, $data);
        }

        if ($formType === 'register') {
            return $this->handleRegister($conn, $data);
        }

        if ($formType === 'forgot') {
            return $this->handleForgotPassword($conn, $data);
        }

        return $mode;
    }

    private function handleLogin(PDO $conn, array &$data): string
    {
        $data['loginEmail'] = sanitize((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($data['loginEmail'] === '' || $password === '') {
            $data['error'] = 'Please enter both email and password.';
            return 'login';
        }

        if (!filter_var($data['loginEmail'], FILTER_VALIDATE_EMAIL)) {
            $data['error'] = 'Please enter a valid email address.';
            return 'login';
        }

        $user = User::findByEmail($conn, $data['loginEmail']);

        if ($user && verifyPassword($password, (string) $user['password'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = (string) $user['username'];
            $_SESSION['email'] = (string) $user['email'];
            $_SESSION['role'] = (string) $user['role'];

            redirectTo('index.php');
        }

        $data['error'] = 'Invalid email or password.';
        return 'login';
    }

    private function handleRegister(PDO $conn, array &$data): string
    {
        $data['registerFullName'] = sanitize((string) ($_POST['full_name'] ?? ''));
        $data['registerUsername'] = sanitize((string) ($_POST['username'] ?? ''));
        $data['registerEmail'] = sanitize((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($data['registerFullName'] === '' || $data['registerUsername'] === '' || $data['registerEmail'] === '' || $password === '' || $confirmPassword === '') {
            $data['error'] = 'Please fill in all required fields.';
            return 'register';
        }

        if (!filter_var($data['registerEmail'], FILTER_VALIDATE_EMAIL)) {
            $data['error'] = 'Please enter a valid email address.';
            return 'register';
        }

        $passwordError = passwordValidationError($password);
        if ($passwordError !== '') {
            $data['error'] = $passwordError;
            return 'register';
        }

        if ($password !== $confirmPassword) {
            $data['error'] = 'Password confirmation does not match.';
            return 'register';
        }

        if (User::usernameOrEmailExists($conn, $data['registerUsername'], $data['registerEmail'])) {
            $data['error'] = 'Username or email already exists.';
            return 'register';
        }

        User::createCustomer($conn, $data['registerUsername'], $data['registerEmail'], $password, $data['registerFullName']);

        $data['success'] = 'Registration successful. You can now sign in with your email.';
        $data['loginEmail'] = $data['registerEmail'];
        $data['registerFullName'] = '';
        $data['registerUsername'] = '';
        $data['registerEmail'] = '';

        return 'login';
    }

    private function handleForgotPassword(PDO $conn, array &$data): string
    {
        $data['forgotEmail'] = sanitize((string) ($_POST['email'] ?? ''));
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if ($data['forgotEmail'] === '' || $newPassword === '' || $confirmPassword === '') {
            $data['error'] = 'Please enter your email and new password.';
            return 'forgot';
        }

        if (!filter_var($data['forgotEmail'], FILTER_VALIDATE_EMAIL)) {
            $data['error'] = 'Please enter a valid email address.';
            return 'forgot';
        }

        $passwordError = passwordValidationError($newPassword);
        if ($passwordError !== '') {
            $data['error'] = $passwordError;
            return 'forgot';
        }

        if ($newPassword !== $confirmPassword) {
            $data['error'] = 'Password confirmation does not match.';
            return 'forgot';
        }

        User::updatePasswordByEmail($conn, $data['forgotEmail'], $newPassword);

        $data['success'] = 'If that email exists, the password has been updated. You can sign in now.';
        $data['loginEmail'] = $data['forgotEmail'];
        $data['forgotEmail'] = '';

        return 'login';
    }
}
