<?php
require_once '../config/auth.php';

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi';
    } else {
        if (loginAdmin($username, $password)) {
            header('Location: dashboard/');
            exit();
        } else {
            $error = 'Username atau password salah';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - MI Muhammadiyah Bojongsana</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Raleway:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #1a365d;
            --primary-dark: #0f2440;
            --accent: #c8903e;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(26, 54, 93, 0.04) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(200, 144, 62, 0.04) 0%, transparent 50%);
        }
        
        .login-container {
            width: 100%;
            max-width: 440px;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 12px 40px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #1a365d, #2563eb);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.6rem;
            margin-bottom: 16px;
            box-shadow: 0 4px 15px rgba(26, 54, 93, 0.3);
        }
        
        .login-header h3 {
            font-family: 'Raleway', sans-serif;
            font-weight: 700;
            color: #0f172a;
            font-size: 1.4rem;
            margin-bottom: 4px;
        }
        
        .login-header p {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            font-size: 0.88rem;
            color: #334155;
            margin-bottom: 6px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: white;
            outline: none;
        }
        
        .input-group {
            display: flex;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
            transition: all 0.2s ease;
        }
        
        .input-group:focus-within {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: white;
        }
        
        .input-group .form-control {
            border: none;
            background: transparent;
            flex: 1;
        }
        
        .input-group .form-control:focus {
            box-shadow: none;
        }
        
        .input-group-text {
            display: flex;
            align-items: center;
            padding: 0 16px;
            color: #94a3b8;
            font-size: 1rem;
            background: transparent;
            border: none;
        }
        
        .btn-toggle-password {
            display: flex;
            align-items: center;
            padding: 0 14px;
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .btn-toggle-password:hover {
            color: #475569;
        }
        
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #1a365d, #1e4d8c);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(26, 54, 93, 0.25);
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #0f2440, #1a365d);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 54, 93, 0.35);
        }
        
        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #fecaca;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        
        .back-link a:hover {
            color: #1a365d;
        }
        
        @media (max-width: 575px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 20px;
            }
            
            .login-logo {
                width: 52px;
                height: 52px;
                font-size: 1.3rem;
                border-radius: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            
            <!-- Header -->
            <div class="login-header">
            <img src="../assets/images/icon/logo_miyasa.png" alt="Logo MI Muhammadiyah Bojongsana" style="width:60px;height:60px;object-fit:contain;margin-bottom:16px;"  >

                <h3>Login Admin</h3>
                <p>MI Muhammadiyah Bojongsana</p>
            </div>
            
            <!-- Error -->
            <?php if ($error): ?>
            <div class="alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <!-- Form -->
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" 
                               value="<?php echo h($username); ?>" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" id="password" 
                               placeholder="Masukkan password" required>
                        <button type="button" class="btn-toggle-password" onclick="togglePassword()">
                            <i class="fa-regular fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk
                </button>
            </form>
            
            <!-- Back -->
            <div class="back-link">
                <a href="../index.php"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Website</a>
            </div>
            
        </div>
    </div>
    
    <script>
    function togglePassword() {
        const password = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>