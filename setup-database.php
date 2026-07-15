<?php
/**
 * Database Setup Script
 * MI Muhammadiyah Bojongsana
 * 
 * Cara menggunakan:
 * 1. Buka browser
 * 2. Akses: http://localhost/school-profile/setup-database.php
 * 3. Ikuti instruksi
 */

$page_title = 'Setup Database';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - MI Muhammadiyah Bojongsana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f6f9; }
        .setup-card { max-width: 800px; margin: 50px auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-card">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="fas fa-database fa-3x mb-3"></i>
                    <h3>Setup Database</h3>
                    <p class="mb-0">MI Muhammadiyah Bojongsana</p>
                </div>
                <div class="card-body p-5">
                    <?php
                    $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
                    
                    if ($step === 1):
                    ?>
                        <h5 class="mb-4">Konfigurasi Database</h5>
                        <form method="POST" action="?step=2">
                            <div class="mb-3">
                                <label for="host" class="form-label">Host</label>
                                <input type="text" class="form-control" id="host" name="host" value="localhost" required>
                            </div>
                            <div class="mb-3">
                                <label for="user" class="form-label">Username</label>
                                <input type="text" class="form-control" id="user" name="user" value="root" required>
                            </div>
                            <div class="mb-3">
                                <label for="pass" class="form-label">Password</label>
                                <input type="text" class="form-control" id="pass" name="pass" placeholder="Kosongkan jika tidak ada">
                            </div>
                            <div class="mb-3">
                                <label for="dbname" class="form-label">Nama Database</label>
                                <input type="text" class="form-control" id="dbname" name="dbname" value="db_sekolah_mi" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 rounded-pill">
                                <i class="fas fa-arrow-right me-2"></i>Lanjutkan
                            </button>
                        </form>
                    <?php
                    elseif ($step === 2 && $_SERVER['REQUEST_METHOD'] === 'POST'):
                        $host = $_POST['host'] ?? 'localhost';
                        $user = $_POST['user'] ?? 'root';
                        $pass = $_POST['pass'] ?? '';
                        $dbname = $_POST['dbname'] ?? 'db_sekolah_mi';
                        
                        try {
                            // Create connection without database
                            $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                            ]);
                            
                            // Create database
                            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            $pdo->exec("USE `$dbname`");
                            
                            // Read and execute SQL file
                            $sql = file_get_contents(__DIR__ . '/database.sql');
                            
                            // Replace database name in SQL
                            $sql = str_replace('db_sekolah_mi', $dbname, $sql);
                            
                            // Execute SQL
                            $pdo->exec($sql);
                            
                            // Generate password hash for admin
                            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
                            
                            // Create admin user
                            $stmt = $pdo->prepare("INSERT INTO users (username, password, nama) VALUES (?, ?, ?)");
                            $stmt->execute(['admin', $admin_password, 'Administrator']);
                            
                            // Update config file
                            $config_content = "<?php
define('DB_HOST', '$host');
define('DB_USER', '$user');
define('DB_PASS', '$pass');
define('DB_NAME', '$dbname');
";
                            
                            // Show success message
                            echo '<div class="alert alert-success">
                                    <i class="fas fa-check-circle fa-2x mb-3 d-block"></i>
                                    <h5>Database berhasil dibuat!</h5>
                                    <hr>
                                    <p><strong>Detail Login Admin:</strong></p>
                                    <ul>
                                        <li>Username: <strong>admin</strong></li>
                                        <li>Password: <strong>admin123</strong></li>
                                    </ul>
                                    <p class="mb-0"><strong>PENTING:</strong> Segera ganti password admin setelah login!</p>
                                  </div>';
                            
                            echo '<div class="text-center mt-4">
                                    <a href="admin/login.php" class="btn btn-success rounded-pill">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login Admin
                                    </a>
                                    <a href="index.php" class="btn btn-outline-success rounded-pill ms-2">
                                        <i class="fas fa-home me-2"></i>Ke Website
                                    </a>
                                  </div>';
                            
                            echo '<div class="alert alert-warning mt-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Keamanan:</strong> Hapus file setup-database.php ini setelah instalasi selesai!
                                  </div>';
                            
                        } catch (PDOException $e) {
                            echo '<div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-2"></i>
                                    Gagal membuat database: ' . $e->getMessage() . '
                                  </div>';
                            echo '<a href="?step=1" class="btn btn-warning rounded-pill">
                                    <i class="fas fa-redo me-2"></i>Coba Lagi
                                  </a>';
                        }
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>