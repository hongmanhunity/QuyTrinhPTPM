<?php
// pages/orders.php
require_once '../includes/init.php';
$page_title = 'Quản lý Đơn hàng';
$pdo = getDBConnection('quan_ly_cua_hang');

$orders = $pdo->query("
    SELECT o.*, c.name as customer_name, u.full_name as user_name 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.id DESC
")->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Đơn hàng</h3>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Người bán</th>
                    <th>Ngày tạo</th>
                    <th>Tổng tiền</th>
                    <th>Khách đưa</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td><strong>#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                    <td><?= htmlspecialchars($o['customer_name'] ?? 'Khách lẻ') ?></td>
                    <td><?= htmlspecialchars($o['user_name'] ?? 'Hệ thống') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                    <td style="color: var(--primary); font-weight: bold;"><?= number_format($o['total_amount']) ?>đ</td>
                    <td><?= number_format($o['amount_paid']) ?>đ</td>
                    <td>
                        <?php if($o['status'] == 'completed'): ?>
                            <span class="badge badge-success">Hoàn thành</span>
                        <?php else: ?>
                            <span class="badge badge-warning"><?= $o['status'] ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
