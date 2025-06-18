<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Pembeli - Adeeva Kitchen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

        .bg-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { width: 80px; height: 80px; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 120px; height: 120px; left: 80%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 60px; height: 60px; left: 50%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 100px; height: 100px; left: 20%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 90px; height: 90px; left: 70%; animation-delay: 3s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-info {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 500;
            margin-right: 15px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .logout-btn {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 90, 36, 0.4);
            color: white;
        }

        .main-content {
            position: relative;
            z-index: 10;
            padding: 60px 0;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 60px;
            color: white;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            animation: slideInDown 1s ease-out;
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
            animation: slideInUp 1s ease-out 0.3s both;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.8s ease-out;
            max-width: 400px;
            width: 100%;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .menu-card:hover::before {
            left: 100%;
        }

        .menu-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .menu-card:hover .menu-icon {
            transform: rotateY(180deg);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .menu-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .menu-description {
            color: #7f8c8d;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .menu-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        .menu-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .menu-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .menu-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .menu-btn:active {
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .menu-grid {
                padding: 0 15px;
            }
            
            .menu-card {
                padding: 30px 20px;
                max-width: 100%;
            }
            
            .user-info {
                display: none;
            }
        }

        .loading {
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.5s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="bg-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-custom px-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-utensils me-2"></i>Adeeva Kitchen
            </a>
            <div class="d-flex align-items-center">
                <div class="user-info">
                    <i class="fas fa-user me-2"></i>
                    <strong>Selamat datang, Pembeli!</strong>
                </div>
                <a href="../index.php" class="logout-btn text-decoration-none">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="welcome-section">
                <h1 class="welcome-title">
                    <i class="fas fa-shopping-cart me-3"></i>
                    Selamat Datang di Adeeva Kitchen
                </h1>
                <p class="welcome-subtitle">
                    Nikmati pengalaman berbelanja yang mudah dan menyenangkan dengan menu terbaik kami
                </p>
            </div>

            <div class="menu-grid loading">
                <div class="menu-card">
                    <div class="menu-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="menu-title">Pesan Menu</h3>
                    <p class="menu-description">
                        Pilih menu berdasarkan hari & atur jadwal pengiriman sesuai kebutuhan Anda
                    </p>
                    <a href="pesanMenu.php" class="menu-btn">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Pesan Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelector('.loading').style.opacity = '1';
            }, 300);

            document.querySelectorAll('.menu-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    let ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    this.appendChild(ripple);

                    let x = e.clientX - e.target.offsetLeft;
                    let y = e.clientY - e.target.offsetTop;

                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            window.addEventListener('scroll', () => {
                let scrolled = window.pageYOffset;
                document.querySelectorAll('.particle').forEach((particle, index) => {
                    let speed = 0.5 + (index * 0.1);
                    particle.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });
        });
    </script>
</body>
</html>
