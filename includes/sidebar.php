<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-store"></i>
        <span>Q.L.C.H</span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="<?= BASE_URL ?>/pages/dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>"><i class="fas fa-home"></i> Tổng quan</a></li>
        <li><a href="<?= BASE_URL ?>/pages/pos.php" class="<?= ($current_page == 'pos.php') ? 'active' : '' ?>"><i class="fas fa-cash-register"></i> Bán hàng POS</a></li>
        <li><a href="<?= BASE_URL ?>/pages/orders.php" class="<?= ($current_page == 'orders.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Đơn hàng</a></li>
        <li><a href="<?= BASE_URL ?>/pages/products.php" class="<?= ($current_page == 'products.php') ? 'active' : '' ?>"><i class="fas fa-box"></i> Sản phẩm</a></li>
        <li><a href="<?= BASE_URL ?>/pages/customers.php" class="<?= ($current_page == 'customers.php') ? 'active' : '' ?>"><i class="fas fa-users"></i> Khách hàng</a></li>
        <?php if($_SESSION['role'] === 'admin'): ?>
            <li><a href="<?= BASE_URL ?>/pages/employees.php" class="<?= ($current_page == 'employees.php') ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Nhân viên</a></li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/pages/auth/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </div>
</aside>
