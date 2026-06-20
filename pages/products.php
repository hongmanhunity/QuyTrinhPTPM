<?php
// pages/products.php
require_once '../includes/init.php';
$page_title = 'Quản lý Sản phẩm';
$pdo = getDBConnection('quan_ly_cua_hang');

// Xử lý Xóa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php?msg=deleted");
    exit;
}

// Xử lý Thêm / Sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $product_code = $_POST['product_code'];
    $name = $_POST['name'];
    $purchase_price = $_POST['purchase_price'];
    $selling_price = $_POST['selling_price'];
    $stock_quantity = $_POST['stock_quantity'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE products SET product_code=?, name=?, purchase_price=?, selling_price=?, stock_quantity=? WHERE id=?");
        $stmt->execute([$product_code, $name, $purchase_price, $selling_price, $stock_quantity, $id]);
        header("Location: products.php?msg=updated");
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (product_code, name, purchase_price, selling_price, stock_quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$product_code, $name, $purchase_price, $selling_price, $stock_quantity]);
        header("Location: products.php?msg=added");
    }
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Sản phẩm</h3>
        <button class="btn btn-primary" data-modal="modal-product" onclick="resetForm()">
            <i class="fas fa-plus"></i> Thêm sản phẩm
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
                    <th>Mã SP</th>
                    <th>Tên Sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['product_code']) ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td style="color: var(--primary); font-weight: 600;"><?= number_format($p['selling_price']) ?>đ</td>
                    <td>
                        <?php if($p['stock_quantity'] <= 5): ?>
                            <span class="badge badge-danger"><?= $p['stock_quantity'] ?> (Sắp hết)</span>
                        <?php else: ?>
                            <span class="badge badge-success"><?= $p['stock_quantity'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn-icon" style="color: var(--warning);" onclick='editProduct(<?= json_encode($p) ?>)'><i class="fas fa-edit"></i></button>
                        <a href="?delete=<?= $p['id'] ?>" class="btn-icon" style="color: var(--danger);" onclick="return confirm('Bạn chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa -->
<div id="modal-product" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Thêm Sản phẩm</h3>
            <span class="close-modal">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="id" id="form-id">
                <div class="form-group">
                    <label>Mã Sản phẩm</label>
                    <input type="text" name="product_code" id="form-code" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tên Sản phẩm</label>
                    <input type="text" name="name" id="form-name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Giá nhập</label>
                    <input type="number" name="purchase_price" id="form-purchase" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Giá bán</label>
                    <input type="number" name="selling_price" id="form-selling" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Số lượng tồn</label>
                    <input type="number" name="stock_quantity" id="form-stock" class="form-control" required>
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
    document.getElementById('modal-title').innerText = 'Thêm Sản phẩm';
    document.getElementById('form-id').value = '';
    document.getElementById('form-code').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-purchase').value = '';
    document.getElementById('form-selling').value = '';
    document.getElementById('form-stock').value = '';
}

function editProduct(product) {
    document.getElementById('modal-title').innerText = 'Sửa Sản phẩm';
    document.getElementById('form-id').value = product.id;
    document.getElementById('form-code').value = product.product_code;
    document.getElementById('form-name').value = product.name;
    document.getElementById('form-purchase').value = product.purchase_price;
    document.getElementById('form-selling').value = product.selling_price;
    document.getElementById('form-stock').value = product.stock_quantity;
    
    document.getElementById('modal-product').classList.add('show');
}
</script>

<?php include '../includes/footer.php'; ?>
