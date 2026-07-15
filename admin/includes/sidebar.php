<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <a href="dashboard.php" style="text-decoration: none;">
                <img src="../../assets/images/icon/logo_miyasa.png" alt="Logo MI Muhammadiyah Bojongsana" style="width:50px;height:50px;object-fit:contain;margin-bottom: 5px;">  
            <h5>Admin Panel</h5>
            <small>MI Muhammadiyah Bojongsana</small>
        </a>
    </div>
    
    <!-- Sidebar Navigation -->
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'dashboard') ? 'active' : ''; ?>" href="../dashboard">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'guru') ? 'active' : ''; ?>" href="../guru/">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Guru & Staff</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'berita') ? 'active' : ''; ?>" href="../berita/">
                <i class="fa-solid fa-newspaper"></i>
                <span>Berita</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'galeri') ? 'active' : ''; ?>" href="../galeri/">
                <i class="fa-solid fa-images"></i>
                <span>Galeri</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'prestasi') ? 'active' : ''; ?>" href="../prestasi/">
                <i class="fa-solid fa-trophy"></i>
                <span>Prestasi</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'fasilitas') ? 'active' : ''; ?>" href="../fasilitas/">
                <i class="fa-solid fa-building"></i>
                <span>Fasilitas</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_admin_page == 'ppdb') ? 'active' : ''; ?>" href="../ppdb/">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>PPDB</span>
                <?php if (isset($pending_ppdb) && $pending_ppdb > 0): ?>
                <span class="sidebar-badge"><?php echo $pending_ppdb; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
    <a class="nav-link <?php echo ($current_admin_page == 'pengaturan') ? 'active' : ''; ?>" href="../pengaturan/">
        <i class="fa-solid fa-gear"></i>
        <span>Pengaturan</span>
    </a>
</li>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                <?php echo strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
            </div>
            <div class="sidebar-user-info">
                <span class="sidebar-user-name"><?php echo h($_SESSION['admin_nama'] ?? $_SESSION['admin_username'] ?? 'Admin'); ?></span>
                <span class="sidebar-user-role">Administrator</span>
            </div>
        </div>
        <a href="../logout.php" class="sidebar-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
    
</aside>

<!-- Mobile Sidebar Toggle -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="fa-solid fa-bars"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>