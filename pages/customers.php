<?php
// pages/customers.php
require_once '../includes/init.php';
$page_title = 'Quản lý Khách hàng';
$pdo = getDBConnection('quan_ly_cua_hang');

// Xử lý Xóa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: customers.php?msg=deleted");
    exit;
}

// Xử lý Thêm / Sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE customers SET name=?, phone=?, address=? WHERE id=?");
        $stmt->execute([$name, $phone, $address, $id]);
        header("Location: customers.php?msg=updated");
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
        $stmt->execute([$name, $phone, $address]);
        header("Location: customers.php?msg=added");
    }
    exit;
}

$customers = $pdo->query("SELECT * FROM customers ORDER BY id DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Khách hàng</h3>
        <button class="btn btn-primary" data-modal="modal-customer" onclick="resetForm()">
            <i class="fas fa-plus"></i> Thêm Khách hàng
        </button>
    </div>
    
    <?php if(isset($_GET['msg'])): ?>
        <?php 
            $msgs = ['added'=>'Thêm thành công!', 'updated'=>'Cập nhật thành công!', 'deleted'=>'Đã xóa!'];
            echo '<div class="alert alert-success">'.$msgs[$_GET['msg']].'</div>';
        ?>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Tên KH</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Ngày đăng ký</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['phone']) ?></td>
                    <td><?= htmlspecialchars($c['address']) ?></td>
                    <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                    <td>
                        <button class="btn-icon" style="color: var(--warning);" onclick='editCustomer(<?= json_encode($c) ?>)'><i class="fas fa-edit"></i></button>
                        <a href="?delete=<?= $c['id'] ?>" class="btn-icon" style="color: var(--danger);" onclick="return confirm('Bạn chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa -->
<div id="modal-customer" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Thêm Khách hàng</h3>
            <span class="close-modal">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="id" id="form-id">
                <div class="form-group">
                    <label>Tên Khách hàng</label>
                    <input type="text" name="name" id="form-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <input type="text" name="phone" id="form-phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" id="form-address" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Lưu lại</button>
            </div>
        </form>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modal-title').innerText = 'Thêm Khách hàng';
    document.getElementById('form-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-phone').value = '';
    document.getElementById('form-address').value = '';
}

function editCustomer(customer) {
    document.getElementById('modal-title').innerText = 'Sửa Khách hàng';
    document.getElementById('form-id').value = customer.id;
    document.getElementById('form-name').value = customer.name;
    document.getElementById('form-phone').value = customer.phone;
    document.getElementById('form-address').value = customer.address;
    
    document.getElementById('modal-customer').classList.add('show');
}
</script>

<?php include '../includes/footer.php'; ?>
