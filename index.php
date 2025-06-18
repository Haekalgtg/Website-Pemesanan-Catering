<?php
session_start();
$err = "";

$koneksi = new mysqli("sql110.infinityfree.com", "if0_39236930", "T9GazsQgsvDaKbT", "if0_39236930_db_catering");



if (isset($_POST['demo_login'])) {
    $_SESSION['user'] = 'demo';
    $_SESSION['id'] = 999;
    $_SESSION['role'] = 'pembeli';
    header("Location: Pembeli/homePembeli.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['demo_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    $stmt = $koneksi->prepare("SELECT * FROM penjual WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($penjual = $result->fetch_assoc()) {
        $_SESSION['user'] = $penjual['username'];
        $_SESSION['id'] = $penjual['id'];
        $_SESSION['role'] = 'penjual';
        header("Location: Pemilik/homePenjual.php");
        exit;
    }
    
    $stmt = $koneksi->prepare("SELECT * FROM pembeli WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($pembeli = $result->fetch_assoc()) {
        $_SESSION['user'] = $pembeli['username'];
        $_SESSION['id'] = $pembeli['id'];
        $_SESSION['role'] = 'pembeli';
        header("Location: Pembeli/homePembeli.php");
        exit;
    }
    
    $err = "Login gagal. Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Adeeva Kitchen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('makanan.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: -1;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 40px;
            max-width: 450px;
            margin: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .brand-logo {
            font-size: 3rem;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .brand-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .brand-subtitle {
            color: #7f8c8d;
            font-weight: 500;
            margin-bottom: 30px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 15px;
            padding: 12px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }

        .form-floating label {
            color: #6c757d;
            font-weight: 500;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-demo {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
            border: none;
            border-radius: 15px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-demo:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(54, 209, 220, 0.3);
            color: white;
        }

        .btn-register {
            background: transparent;
            border: 2px solid #667eea;
            border-radius: 15px;
            padding: 12px;
            font-weight: 600;
            color: #667eea;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-register:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 500;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }

        .password-toggle {
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 25px 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }

        .divider span {
            background: white;
            padding: 0 20px;
            color: #6c757d;
            font-weight: 500;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 10px;
                padding: 25px;
            }
            
            .brand-title {
                font-size: 1.8rem;
            }
            
            .brand-logo {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container text-center shadow-lg">
        <div class="brand-logo">🍽️</div>
        <h1 class="brand-title">Adeeva Kitchen</h1>
        <p class="brand-subtitle">Makanan Selalu Fresh Setiap Harinya</p>
        
        <?php if ($err): ?>
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $err ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mb-4">
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="username" placeholder="Nama pengguna" required>
                <label for="username"><i class="fas fa-user me-2"></i>Nama Pengguna</label>
            </div>
            
            <div class="form-floating mb-3 position-relative">
                <input type="password" name="password" class="form-control" id="password" placeholder="Kata sandi" required>
                <label for="password"><i class="fas fa-lock me-2"></i>Kata Sandi</label>
                <i class="fas fa-eye input-icon password-toggle" onclick="togglePassword()"></i>
            </div>
            
            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i>
                Masuk ke Akun
            </button>
        </form>

        <div class="divider">
            <span>atau</span>
        </div>

        <form method="post" class="mb-3">
            <input type="hidden" name="demo_login" value="1">
            <button type="submit" class="btn btn-demo w-100">
                <i class="fas fa-shopping-cart me-2"></i>
                Masuk Tanpa Login
            </button>
        </form>

        <a href="daftar.php" class="btn btn-register w-100">
            <i class="fas fa-user-plus me-2"></i>
            Belum Punya Akun? Daftar
        </a>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-control, .btn');
            formElements.forEach((element, index) => {
                element.style.animationDelay = `${index * 0.1}s`;
                element.classList.add('animate__animated', 'animate__fadeInUp');
            });
        });

        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    </script>

    <style>
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</body>
</html>
