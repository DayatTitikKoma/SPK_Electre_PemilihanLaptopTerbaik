<?php
/**
 * Halaman Login
 */

require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Jika sudah login, redirect
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'login') {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        if (login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Email atau password salah!';
        }
    } elseif ($_POST['action'] == 'register') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $nama_lengkap = trim($_POST['nama_lengkap']);
        
        if ($password !== $confirm_password) {
            $error = 'Password dan konfirmasi password tidak sama!';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter!';
        } else {
            $result = register($username, $email, $password, $nama_lengkap);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SPK ELECTRE Pemilihan Laptop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background glow effect */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        body::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(118, 75, 162, 0.1) 0%, transparent 70%);
            animation: rotate 25s linear infinite reverse;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #E0E0E0;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            border-radius: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .login-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .login-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 1rem;
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 400;
        }

        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            background: white;
            border: 2px solid #ddd;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s;
            color: #333;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.95rem;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
            padding: 12px 16px;
        }

        .nav-tabs {
            border: none;
            margin-bottom: 25px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link:hover {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .input-group-text {
            background: white;
            border: 2px solid #ddd;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #666;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }

        .input-group:focus-within .form-control {
            border-color: #667eea;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .login-subtitle {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">
                <i class="bi bi-laptop"></i>
            </div>
            
            <h1 class="login-title">Sistem Pendukung Keputusan</h1>
            <p class="login-subtitle">Pemilihan Laptop Terbaik - Login</p>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" style="flex: 1;">
                    <a class="nav-link active" data-bs-toggle="tab" href="#login-tab">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
                <li class="nav-item" style="flex: 1;">
                    <a class="nav-link" data-bs-toggle="tab" href="#register-tab">
                        <i class="bi bi-person-plus"></i> Daftar
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Login Tab -->
                <div class="tab-pane fade show active" id="login-tab">
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" name="username" placeholder="nama@email.com" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-login">
                            Masuk
                        </button>
                    </form>
                    <div class="register-link">
                        Belum punya akun? <a href="#" onclick="document.querySelector('.nav-link[href=\'#register-tab\']').click(); return false;">Daftar di sini</a>
                    </div>
                </div>

                <!-- Register Tab -->
                <div class="tab-pane fade" id="register-tab">
                    <form method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Ulangi password" required>
                        </div>
                        <button type="submit" class="btn btn-login">
                            Daftar
                        </button>
                    </form>
                    <div class="register-link">
                        Sudah punya akun? <a href="#" onclick="document.querySelector('.nav-link[href=\'#login-tab\']').click(); return false;">Login di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto switch tab on link click
        document.querySelectorAll('.register-link a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetTab = this.textContent.includes('Daftar') ? '#register-tab' : '#login-tab';
                const tabLink = document.querySelector(`.nav-link[href="${targetTab}"]`);
                if (tabLink) {
                    const tab = new bootstrap.Tab(tabLink);
                    tab.show();
                }
            });
        });
    </script>
</body>
</html>
