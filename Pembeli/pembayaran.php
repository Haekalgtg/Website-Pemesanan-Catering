<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pembeli') {
    header("Location: ../index.php");
    exit();
}

if (empty($_SESSION['keranjang'])) {
    header("Location: dashboard.php");
    exit();
}

$id_pembeli = $_SESSION['id'];
$error = '';
$success = '';
$pesanan_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    $metode_pembayaran = trim($_POST['metode_pembayaran']);
    $catatan = trim($_POST['catatan']);
    
    if (empty($metode_pembayaran)) {
        $error = "Pilih metode pembayaran.";
    } else {
        $koneksi->begin_transaction();
        
        try {
            $total = 0;
            foreach ($_SESSION['keranjang'] as $item) {
                $total += $item['price'] * $item['jumlah'];
            }
 
            $stmt = $koneksi->prepare("INSERT INTO pesanan (id_user, tanggal_pesan, status, total_harga, metode_pembayaran, bukti_pembayaran, created_at) VALUES (?, NOW(), 'pending', ?, ?, ?, NOW())");
            $stmt->bind_param("iiss", $id_pembeli, $total, $metode_pembayaran, $catatan);
            $stmt->execute();
            
            $id_pesanan = $koneksi->insert_id;
            $stmt_item = $koneksi->prepare("INSERT INTO pesanan_detail (id_pesanan, id_menu, jumlah, subtotal) VALUES (?, ?, ?, ?)");
            
            foreach ($_SESSION['keranjang'] as $item) {
                $subtotal = $item['price'] * $item['jumlah'];
                $stmt_item->bind_param("iiii", 
                    $id_pesanan, 
                    $item['id_menu'], 
                    $item['jumlah'], 
                    $subtotal
                );
                $stmt_item->execute();
            }
 
            $koneksi->commit();
            
            $_SESSION['completed_order_id'] = $id_pesanan;
            
            $_SESSION['keranjang'] = [];
            
            $success = "Pesanan berhasil dibuat dengan ID Pesanan: #" . $id_pesanan;
            $pesanan_id = $id_pesanan;
            
        } catch (Exception $e) {
            $koneksi->rollback();
            $error = "Terjadi kesalahan saat memproses pesanan: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dana_payment_status'])) {
    $payment_status = $_POST['dana_payment_status'];
    $transaction_id = $_POST['transaction_id'];
    
    if ($payment_status === 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Pembayaran DANA berhasil!']);
    } else {
        echo json_encode(['status' => 'failed', 'message' => 'Pembayaran DANA gagal!']);
    }
    exit;
}

$total_keranjang = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total_keranjang += $item['price'] * $item['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Sistem Pemesanan Makanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dana-qr-container {
            background: linear-gradient(135deg, #008CFF 0%, #0066CC 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            text-align: center;
        }
        
        .qr-code {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin: 15px auto;
            max-width: 200px;
        }
        
        .payment-timer {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b6b;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-pending { background-color: #ffc107; }
        .status-success { background-color: #28a745; }
        .status-failed { background-color: #dc3545; }
        
        .dana-logo {
            height: 30px;
            vertical-align: middle;
        }
        
        .payment-step {
            background: #f8f9fa;
            border-left: 4px solid #008CFF;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 0 8px 8px 0;
        }

        .success-animation {
            animation: successPulse 2s ease-in-out;
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .review-prompt {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-credit-card me-2"></i>Pembayaran</h2>
                    <a href="pesanMenu.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show success-animation" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            
            <!-- Review Prompt Section -->
            <div class="review-prompt">
                <div class="mb-3">
                    <i class="fas fa-star" style="font-size: 48px; margin-bottom: 15px;"></i>
                    <h4>Pesanan Anda Berhasil!</h4>
                    <p class="mb-3">Terima kasih telah memesan. Bagaimana pengalaman pemesanan Anda hari ini?</p>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="ulasan.php?pesanan_id =<?= $pesanan_id ?>" class="btn btn-light btn-lg me-md-2">
                        <i class="fas fa-star me-2"></i>Berikan Ulasan
                    </a>
                    <a href="homePembeli.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-home me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
                <div class="mt-3">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Ulasan Anda membantu meningkatkan kualitas layanan kami
                    </small>
                </div>
            </div>

            <script>
                let redirectTimer = setTimeout(function() {
                    if (confirm('Apakah Anda ingin memberikan ulasan untuk pesanan ini?')) {
                        window.location.href = 'ulasan.php?order_id=<?= $pesanan_id ?>';
                    } else {
                        window.location.href = 'homePembeli.php';
                    }
                }, 5000);

                document.querySelectorAll('.review-prompt a').forEach(function(link) {
                    link.addEventListener('click', function() {
                        clearTimeout(redirectTimer);
                    });
                });
            </script>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($_SESSION['keranjang'])): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Menu</th>
                                                <th>Jumlah</th>
                                                <th>Harga</th>
                                                <th>Tanggal Kirim</th>
                                                <th>Alamat</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($_SESSION['keranjang'] as $item): 
                                                $subtotal = $item['price'] * $item['jumlah'];
                                            ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($item['name']) ?></strong>
                                                    </td>
                                                    <td><?= $item['jumlah'] ?></td>
                                                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                                    <td><?= date('d/m/Y', strtotime($item['tanggal_kirim'])) ?></td>
                                                    <td>
                                                        <small><?= htmlspecialchars($item['alamat']) ?></small>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-dark">
                                                <th colspan="5">Total Keseluruhan</th>
                                                <th class="text-end">Rp <?= number_format($total_keranjang, 0, ',', '.') ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-money-check-alt me-2"></i>Detail Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" id="payment-form">
                                <div id="dana-payment" class="mt-3" style="display: none;">
                                    <div class="dana-qr-container">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Logo_dana_blue.svg/200px-Logo_dana_blue.svg.png" alt="DANA" class="dana-logo me-2">
                                            <h5 class="mb-0">Pembayaran DANA</h5>
                                        </div>
                                        
                                        <div class="qr-code" id="qr-code-container">
                                            <div class="text-center">
                                                <i class="fas fa-qrcode" style="font-size: 100px; color: #008CFF;"></i>
                                                <p class="mt-2 mb-0 text-dark"><strong>QR Code</strong></p>
                                                <small class="text-muted">Scan dengan aplikasi DANA</small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="mb-2">Total Pembayaran:</p>
                                            <h4><strong>Rp <?= number_format($total_keranjang, 0, ',', '.') ?></strong></h4>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="mb-1">Waktu tersisa:</p>
                                            <div class="payment-timer" id="countdown">15:00</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="status-indicator status-pending" id="payment-status-indicator"></div>
                                            <span id="payment-status-text">Menunggu pembayaran...</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6><i class="fas fa-list-ol me-2"></i>Cara Pembayaran:</h6>
                                        <div class="payment-step">
                                            <small><strong>1.</strong> Buka aplikasi DANA di smartphone Anda</small>
                                        </div>
                                        <div class="payment-step">
                                            <small><strong>2.</strong> Pilih menu "Scan" atau "Bayar"</small>
                                        </div>
                                        <div class="payment-step">
                                            <small><strong>3.</strong> Scan QR Code di atas</small>
                                        </div>
                                        <div class="payment-step">
                                            <small><strong>4.</strong> Konfirmasi pembayaran di aplikasi DANA</small>
                                        </div>
                                        <div class="payment-step">
                                            <small><strong>5.</strong> Tunggu konfirmasi pembayaran berhasil</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 text-center">
                                        <button type="button" class="btn btn-sm btn-outline-light" onclick="simulatePayment('success')">
                                            <i class="fas fa-check me-1"></i>Simulasi Berhasil
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light ms-2" onclick="simulatePayment('failed')">
                                            <i class="fas fa-times me-1"></i>Simulasi Gagal
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-credit-card me-1"></i>Metode Pembayaran *
                                    </label>
                                    <select name="metode_pembayaran" class="form-select" required onchange="toggleDanaPayment(this.value)">
                                        <option value="">Pilih Metode Pembayaran</option>
                                        <option value="cod">Cash on Delivery (COD)</option>
                                        <option value="dana">DANA</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-sticky-note me-1"></i>Catatan (Opsional)
                                    </label>
                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan khusus untuk pesanan Anda..."></textarea>
                                </div>

                                <hr>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>Rp <?= number_format($total_keranjang, 0, ',', '.') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Biaya Pengiriman:</span>
                                        <span class="text-success">Gratis</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Pembayaran:</strong>
                                        <strong class="text-primary">Rp <?= number_format($total_keranjang, 0, ',', '.') ?></strong>
                                    </div>
                                </div>

                                <button type="submit" name="bayar" class="btn btn-success w-100 btn-lg" id="confirm-payment">
                                    <i class="fas fa-check me-2"></i>Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-info-circle me-1"></i>Informasi Pembayaran
                            </h6>
                            <small class="text-muted">
                                • Pesanan akan diproses setelah pembayaran dikonfirmasi<br>
                                • <strong>COD:</strong> Bayar tunai saat makanan diantar<br>
                                • <strong>DANA:</strong> Pembayaran real-time melalui QR Code<br>
                                • Hubungi customer service jika ada kendala<br>
                                • Waktu pembayaran DANA: 15 menit<br>
                                • <strong>Setelah pembayaran selesai, Anda dapat memberikan ulasan!</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let countdownTimer;
        let paymentStatusCheck;
        let danaPaymentActive = false;
        
        function toggleDanaPayment(metode) {
            const danaSection = document.getElementById('dana-payment');
            const confirmButton = document.getElementById('confirm-payment');
            
            if (metode === 'dana') {
                danaSection.style.display = 'block';
                confirmButton.innerHTML = '<i class="fas fa-wallet me-2"></i>Bayar dengan DANA';
                confirmButton.className = 'btn btn-info w-100 btn-lg';
                danaPaymentActive = true;
                
                generateQRCode();
                
                startCountdown(15 * 60); 

                startPaymentStatusCheck();
                
            } else {
                danaSection.style.display = 'none';
                confirmButton.innerHTML = '<i class="fas fa-check me-2"></i>Konfirmasi Pembayaran';
                confirmButton.className = 'btn btn-success w-100 btn-lg';
                danaPaymentActive = false;
                
                clearInterval(countdownTimer);
                clearInterval(paymentStatusCheck);
            }
        }
        
        function generateQRCode() {
            const qrContainer = document.getElementById('qr-code-container');
            const transactionId = 'TXN_' + Date.now();
            const amount = <?= $total_keranjang ?>;
            
            qrContainer.innerHTML = `
                <div class="text-center">
                    <div style="border: 2px solid #008CFF; padding: 10px; background: #f0f8ff; border-radius: 8px;">
                        <div style="font-family: monospace; font-size: 10px; word-break: break-all; color: #008CFF;">
                            DANA:${transactionId}:${amount}:<?= time() ?>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(10, 1fr); gap: 2px; margin: 10px 0;">
                            ${generateQRPattern()}
                        </div>
                    </div>
                    <p class="mt-2 mb-0 text-dark"><strong>QR Code Pembayaran</strong></p>
                    <small class="text-muted">ID: ${transactionId}</small>
                </div>
            `;
        }
        
        function generateQRPattern() {
            let pattern = '';
            for (let i = 0; i < 100; i++) {
                const color = Math.random() > 0.5 ? '#008CFF' : '#ffffff';
                pattern += `<div style="width: 8px; height: 8px; background: ${color};"></div>`;
            }
            return pattern;
        }
        
        function startCountdown(duration) {
            let timeLeft = duration;
            const countdownElement = document.getElementById('countdown');
            
            countdownTimer = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    updatePaymentStatus('expired', 'Waktu pembayaran habis');
                    countdownElement.textContent = '00:00';
                }
                
                timeLeft--;
            }, 1000);
        }
        
        function startPaymentStatusCheck() {
            paymentStatusCheck = setInterval(() => {
                console.log('Checking payment status...');
            }, 5000);
        }
        
        function updatePaymentStatus(status, message) {
            const indicator = document.getElementById('payment-status-indicator');
            const statusText = document.getElementById('payment-status-text');
            const confirmButton = document.getElementById('confirm-payment');
            
            indicator.className = 'status-indicator status-' + status;
            statusText.textContent = message;
            
            if (status === 'success') {
                confirmButton.innerHTML = '<i class="fas fa-check me-2"></i>Pembayaran Berhasil - Lanjutkan Pesanan';
                confirmButton.className = 'btn btn-success w-100 btn-lg';
                clearInterval(countdownTimer);
                clearInterval(paymentStatusCheck);
                
                setTimeout(() => {
                    document.getElementById('payment-form').submit();
                }, 2000);
                
            } else if (status === 'failed' || status === 'expired') {
                confirmButton.innerHTML = '<i class="fas fa-times me-2"></i>Pembayaran Gagal';
                confirmButton.className = 'btn btn-danger w-100 btn-lg';
                confirmButton.disabled = true;
                clearInterval(countdownTimer);
                clearInterval(paymentStatusCheck);
            }
        }
        
        function simulatePayment(result) {
            if (result === 'success') {
                updatePaymentStatus('success', 'Pembayaran berhasil!');
                
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'dana_payment_status=success&transaction_id=TXN_' + Date.now()
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Payment confirmed:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
                
            } else {
                updatePaymentStatus('failed', 'Pembayaran gagal. Silakan coba lagi.');
            }
        }
        
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            const metode = document.querySelector('select[name="metode_pembayaran"]').value;
            
            if (metode === 'dana') {
                if (!document.getElementById('payment-status-indicator').classList.contains('status-success')) {
                    e.preventDefault();
                    alert('Harap selesaikan pembayaran DANA terlebih dahulu sebelum melanjutkan.');
                    return false;
                }
            }
        });
        
        setInterval(() => {
            if (danaPaymentActive) {
                generateQRCode();
            }
        }, 5 * 60 * 1000);
        
        window.addEventListener('beforeunload', function() {
            clearInterval(countdownTimer);
            clearInterval(paymentStatusCheck);
        });
    </script>
</body>
</html>