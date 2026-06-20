<?php
// pages/api/checkout.php
require_once '../../includes/init.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    $pdo = getDBConnection('quan_ly_cua_hang');
    $pdo->beginTransaction();

    $customer_id = !empty($data['customer_id']) ? $data['customer_id'] : null;
    $total = $data['total_amount'];
    $paid = $data['amount_paid'];
    $user_id = $_SESSION['user_id'];

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_id, total_amount, amount_paid, status) VALUES (?, ?, ?, ?, 'completed')");
    $stmt->execute([$user_id, $customer_id, $total, $paid]);
    $order_id = $pdo->lastInsertId();

    // Insert order details & update stock
    foreach ($data['cart'] as $item) {
        $subtotal = $item['selling_price'] * $item['qty'];
        
        $stmtDetails = $pdo->prepare("INSERT INTO order_details (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtDetails->execute([$order_id, $item['id'], $item['qty'], $item['selling_price'], $subtotal]);

        $stmtStock = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        $stmtStock->execute([$item['qty'], $item['id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
