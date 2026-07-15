<?php
$is_home = ($current_page == 'home' || $current_page == 'beranda');
// Semua halaman menggunakan navbar transparan yang menyatu dengan header
$navbar_transparent = 'navbar-transparent';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MI Muhammadiyah Bojongsana - Madrasah Ibtidaiyah Unggulan">
    <title><?php echo isset($page_title) ? h($page_title) . ' - ' : ''; ?>MI Muhammadiyah Bojongsana</title>
      <link rel="icon" type="image/x-icon" href="assets/images/icon/logo_miyasa.png">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
       <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Raleway:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Modern CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>body{visibility:visible;}</style>
</head>
<body>

<!-- Navbar Transparan - Menyatu dengan Header -->
<nav class="navbar navbar-expand-lg navbar-modern sticky-top <?php echo $navbar_transparent; ?>" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand brand-wrapper" href="index.php">
            <img src="assets/images/icon/logo_miyasa.png" alt="Logo MI Muhammadiyah Bojongsana" style="width:35px;height:35px;object-fit:contain;">      
            <div class="brand-text-modern">
                <span class="brand-name">MI MUHAMMADIYAH BOJONGSANA</span>
                <span class="brand-sub">Kec.Rembang Kab.Purbalingga</span>
            </div>
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link nav-link-modern <?php echo ($current_page=='home')?'active':''; ?>" href="index.php">
                        <i class="fa-solid fa-house"></i><span>Beranda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-modern <?php echo ($current_page=='profil')?'active':''; ?>" href="profil.php">
                        <i class="fa-solid fa-address-card"></i><span>Profil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-modern <?php echo ($current_page=='guru')?'active':''; ?>" href="guru.php">
                        <i class="fa-solid fa-chalkboard-user"></i><span>Guru</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-modern <?php echo ($current_page=='berita')?'active':''; ?>" href="berita.php">
                        <i class="fa-solid fa-newspaper"></i><span>Berita</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-modern <?php echo ($current_page=='galeri')?'active':''; ?>" href="galeri.php">
                        <i class="fa-solid fa-images"></i><span>Galeri</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-btn-modern <?php echo ($current_page=='ppdb')?'active':''; ?>" href="ppdb.php">
                        <i class="fa-solid fa-graduation-cap"></i><span>PPDB</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main>