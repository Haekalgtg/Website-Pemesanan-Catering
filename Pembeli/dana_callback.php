<?php
include 'koneksi.php';

$log_file = 'dana_callback.log';
$log_data = date('Y-m-d H:i:s') . " - " . file_get_contents('php://input') . "\n";
file_put_contents($log_file, $log_data, FILE_APPEND);

$json_data = file_get_contents('php://input');
$callback_data = json_decode($json_data, true);

function verifyDanaSignature($data, $signature, $client_secret) {
    $expected_signature = hash_hmac('sha256', json_encode($data), $client_secret);
    return hash_equals($expected_signature, $signature);
}

if ($callback_data) {
    $merchant_transaction_id = $callback_data['merchantTransactionId'] ?? null;
    $payment_status = $callback_data['paymentStatus'] ?? null;
    $dana_transaction_id = $callback_data['danaTransactionId'] ?? null;
    $signature = $_SERVER['HTTP_X_DANA_SIGNATURE'] ?? null;
 
    $client_secret = 'YOUR_CLIENT_SECRET';
    
    if (verifyDanaSignature($callback_data, $signature, $client_secret)) {
        
        if ($payment_status === 'SUCCESS' && $merchant_transaction_id) {
            $stmt = $koneksi->prepare("UPDATE pesanan SET status = 'dibayar', bukti_pembayaran = ? WHERE id = ?");
            $stmt->bind_param("si", $dana_transaction_id, $merchant_transaction_id);
            
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'SUCCESS',
                    'message' => 'Payment processed successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'status' => 'ERROR',
                    'message' => 'Failed to update payment status'
                ]);
            }
        } else {
            if ($merchant_transaction_id) {
                $new_status = ($payment_status === 'FAILED') ? 'dibatalkan' : 'pending';
                $koneksi->query("UPDATE pesanan SET status = '$new_status' WHERE id = $merchant_transaction_id");
            }
            
            http_response_code(200);
            echo json_encode([
                'status' => 'RECEIVED',
                'message' => 'Payment status updated'
            ]);
        }
    } else {
        http_response_code(401);
        echo json_encode([
            'status' => 'ERROR',
            'message' => 'Invalid signature'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'ERROR',
        'message' => 'Invalid callback data'
    ]);
}
?>