<?php
// pages/employees.php
require_once '../includes/init.php';

// Chỉ admin mới có quyền truy cập
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$page_title = 'Quản lý Nhân viên';
$pdo = getDBConnection('quan_ly_cua_hang');

// Xử lý Xóa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== $_SESSION['user_id']) { // Không cho phép tự xóa mình
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: employees.php?msg=deleted");
        exit;
    }
}

// Xử lý Thêm / Sửa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

    try {
        if ($id) {
            if ($password) {
                $stmt = $pdo->prepare("UPDATE users SET username=?, full_name=?, role=?, password=? WHERE id=?");
                $stmt->execute([$username, $full_name, $role, $password, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username=?, full_name=?, role=? WHERE id=?");
                $stmt->execute([$username, $full_name, $role, $id]);
            }
            header("Location: employees.php?msg=updated");
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password ? $password : password_hash('123456', PASSWORD_DEFAULT), $full_name, $role]);
            header("Location: employees.php?msg=added");
        }
    } catch (PDOException $e) {
        $error = "Tên đăng nhập đã tồn tại!";
    }
    if (!isset($error)) exit;
}

$employees = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

include '../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Nhân viên</h3>
        <button class="btn btn-primary" data-modal="modal-employee" onclick="resetForm()">
            <i class="fas fa-plus"></i> Thêm Nhân viên
        </button>
    </div>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['msg'])): ?>
        <?php 
            $msgs = ['added'=>'Thêm thành công! (Mật khẩu mặc định: 123456 nếu không nhập)', 'updated'=>'Cập nhật thành công!', 'deleted'=>'Đã xóa!'];
            echo '<div class="alert alert-success">'.$msgs[$_GET['msg']].'</div>';
        ?>
    <?php endif; ?>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Tên hiển thị</th>
                    <th>Tên đăng nhập</th>
                    <th>Vai trò</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($employees as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['full_name']) ?></td>
                    <td><?= htmlspecialchars($e['username']) ?></td>
                    <td>
                        <?php if($e['role'] == 'admin'): ?>
                            <span class="badge badge-danger">Quản lý</span>
                        <?php else: ?>
                            <span class="badge badge-success">Nhân viên</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn-icon" style="color: var(--warning);" onclick='editEmployee(<?= json_encode($e) ?>)'><i class="fas fa-edit"></i></button>
                        <?php if($e['id'] !== $_SESSION['user_id']): ?>
                            <a href="?delete=<?= $e['id'] ?>" class="btn-icon" style="color: var(--danger);" onclick="return confirm('Bạn chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Thêm/Sửa -->
<div id="modal-employee" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Thêm Nhân viên</h3>
            <span class="close-modal">&times;</span>
        </div>
        <form method="POST" action="">
            <div class="modal-body">
                <input type="hidden" name="id" id="form-id">
                <div class="form-group">
                    <label>Tên hiển thị</label>
                    <input type="text" name="full_name" id="form-fullname" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <input type="text" name="username" id="form-username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu (Để trống nếu không đổi)</label>
                    <input type="password" name="password" id="form-password" class="form-control">
                </div>
                <div class="form-group">
                    <label>Vai trò</label>
                    <select name="role" id="form-role" class="form-control">
                        <option value="staff">Nhân viên bán hàng</option>
                        <option value="admin">Quản lý (Admin)</option>
                    </select>
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
    document.getElementById('modal-title').innerText = 'Thêm Nhân viên';
    document.getElementById('form-id').value = '';
    document.getElementById('form-fullname').value = '';
    document.getElementById('form-username').value = '';
    document.getElementById('form-password').value = '';
    document.getElementById('form-role').value = 'staff';
}

function editEmployee(employee) {
    document.getElementById('modal-title').innerText = 'Sửa Nhân viên';
    document.getElementById('form-id').value = employee.id;
    document.getElementById('form-fullname').value = employee.full_name;
    document.getElementById('form-username').value = employee.username;
    document.getElementById('form-password').value = '';
    document.getElementById('form-role').value = employee.role;
    
    document.getElementById('modal-employee').classList.add('show');
}
</script>

<?php include '../includes/footer.php'; ?>
