<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Quản Lý Cửa Hàng' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php include ROOT_PATH . '/includes/sidebar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <?php if(isset($_SESSION['user_id'])): ?>
                <header class="top-header">
                    <div class="header-left">
                        <button id="toggle-sidebar" class="btn-icon"><i class="fas fa-bars"></i></button>
                        <h2><?= $page_title ?? 'Dashboard' ?></h2>
                    </div>
                    <div class="header-right">
                        <div class="user-profile">
                            <div class="avatar" style="background-color: var(--primary); color: white; display:flex; align-items:center; justify-content:center; font-weight: bold;">
                                <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
                            </div>
                            <span><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                        </div>
                    </div>
                </header>
            <?php endif; ?>
            <div class="page-content">
