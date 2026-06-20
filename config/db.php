<?php
// config/db.php
// Tự động nhận diện host: Nếu có cấu hình Docker thì lấy, không thì mặc định là XAMPP (127.0.0.1)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : ''; // XAMPP mặc định pass rỗng
$charset = 'utf8mb4';

function getDBConnection($dbname = null) {
    global $host, $user, $pass, $charset;
    
    $dsn = "mysql:host=$host;charset=$charset";
    if ($dbname) {
        $dsn .= ";dbname=$dbname";
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
?>
