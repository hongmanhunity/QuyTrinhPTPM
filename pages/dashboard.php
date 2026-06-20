<?php
// pages/dashboard.php
require_once '../includes/init.php';
$page_title = 'Tổng quan hệ thống';

try {
    $pdo = getDBConnection('quan_ly_cua_hang');
    
    // Thống kê cơ bản
    $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $total_customers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn();
    $revenue = $revenue ? $revenue : 0;

    // Đơn hàng gần đây
    $recent_orders = $pdo->query("SELECT o.id, o.total_amount, o.status, o.created_at, c.name as customer_name 
                                  FROM orders o 
                                  LEFT JOIN customers c ON o.customer_id = c.id 
                                  ORDER BY o.id DESC LIMIT 5")->fetchAll();
                                  
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../includes/header.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Tổng doanh thu</h3>
            <h2><?= number_format($revenue, 0, ',', '.') ?>đ</h2>
        </div>
        <div class="stat-icon blue"><i class="fas fa-wallet"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Đơn hàng</h3>
            <h2><?= $total_orders ?></h2>
        </div>
        <div class="stat-icon green"><i class="fas fa-shopping-bag"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Sản phẩm</h3>
            <h2><?= $total_products ?></h2>
        </div>
        <div class="stat-icon orange"><i class="fas fa-box"></i></div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Khách hàng</h3>
            <h2><?= $total_customers ?></h2>
        </div>
        <div class="stat-icon red"><i class="fas fa-users"></i></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Đơn hàng gần đây</h3>
        <a href="orders.php" class="btn btn-sm btn-primary">Xem tất cả</a>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Ngày tạo</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($recent_orders)): ?>
                    <tr><td colspan="5" style="text-align: center;">Chưa có đơn hàng nào</td></tr>
                <?php else: ?>
                    <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td>#<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($order['customer_name'] ?? 'Khách lẻ') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td style="font-weight: 600;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php if($order['status'] == 'completed'): ?>
                                    <span class="badge badge-success">Hoàn thành</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= $order['status'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
