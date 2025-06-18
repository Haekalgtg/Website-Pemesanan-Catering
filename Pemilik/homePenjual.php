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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Pemilik Catering</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/><circle cx="900" cy="800" r="80" fill="url(%23a)"/></svg>') no-repeat;
            background-size: cover;
            pointer-events: none;
            z-index: -1;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border: none;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 20px 20px;
            margin-bottom: 2rem;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-logout {
            background: var(--secondary-gradient);
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 4rem;
            padding: 2rem;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out;
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.2s both;
        }



        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            padding: 0 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease-out var(--delay, 0.6s) both;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
        }

        .menu-card:hover::before {
            left: 100%;
        }

        .menu-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: inherit;
        }

        .menu-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            position: relative;
            transition: all 0.3s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .icon-add { background: var(--success-gradient); }
        .icon-list { background: var(--primary-gradient); }
        .icon-money { background: var(--warning-gradient); }
        .icon-order { background: var(--secondary-gradient); }

        .menu-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: #2d3748;
        }

        .menu-description {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
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

        .menu-card:nth-child(1) { --delay: 0.6s; }
        .menu-card:nth-child(2) { --delay: 0.8s; }
        .menu-card:nth-child(3) { --delay: 1s; }
        .menu-card:nth-child(4) { --delay: 1.2s; }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-container {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-utensils me-2"></i>Adeeva Kitchen
            </a>
            <div>
                <a href="../index.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1 class="welcome-title">
                <i class="fas fa-crown me-3"></i>Selamat Datang, Chef!
            </h1>
            <p class="welcome-subtitle">
                Kelola bisnis catering Anda dengan mudah dan profesional
            </p>
            

        </div>

        <div class="menu-grid">
            <a href="upload.php" class="menu-card">
                <div class="menu-icon icon-add">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h4 class="menu-title">Tambah Menu Baru</h4>
                <p class="menu-description">Buat dan upload makanan lezat untuk menu hari ini dengan mudah</p>
            </a>

            <a href="menu.php" class="menu-card">
                <div class="menu-icon icon-list">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h4 class="menu-title">Daftar Menu Saya</h4>
                <p class="menu-description">Lihat, edit, dan kelola semua menu makanan yang tersedia</p>
            </a>

            <a href="keuangan.php" class="menu-card">
                <div class="menu-icon icon-money">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4 class="menu-title">Laporan Keuangan</h4>
                <p class="menu-description">Pantau pemasukan, pengeluaran, dan profit bisnis catering Anda</p>
            </a>

            <a href="manajemen_pesanan.php" class="menu-card">
                <div class="menu-icon icon-order">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h4 class="menu-title">Manajemen Pesanan</h4>
                <p class="menu-description">Kelola pesanan masuk dari konsumen dengan sistem yang efisien</p>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('mousemove', function(e) {
                const shapes = document.querySelectorAll('.shape');
                const x = e.clientX / window.innerWidth;
                const y = e.clientY / window.innerHeight;
                
                shapes.forEach((shape, index) => {
                    const speed = (index + 1) * 0.5;
                    const xPos = (x * speed * 50) - 25;
                    const yPos = (y * speed * 50) - 25;
                    shape.style.transform = `translate(${xPos}px, ${yPos}px) rotate(${x * 360}deg)`;
                });
            });

            const menuCards = document.querySelectorAll('.menu-card');
            menuCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    const ripple = document.createElement('div');
                    const rect = card.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        pointer-events: none;
                        animation: ripple 0.6s ease-out;
                    `;
                    
                    card.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    0% {
                        transform: scale(0);
                        opacity: 1;
                    }
                    100% {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });

    </script>
</body>
</html>
