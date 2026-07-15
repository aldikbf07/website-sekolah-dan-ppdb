</main>

<!-- Footer Modern -->
<footer class="footer-modern" id="kontak">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="brand-icon-modern" style="width:40px;height:40px;font-size:1rem;">
                        <img src="assets/images/icon/logo_miyasa.png" alt="Logo MI Muhammadiyah Bojongsana"
                            style="width:35px;height:35px;object-fit:contain;">
                    </div>
                    <h5 class="mb-0">MI Muhammadiyah Bojongsana</h5>
                </div>
                <p class="mb-3" style="font-size:0.9rem;">Madrasah Ibtidaiyah Muhammadiyah Bojongsana berkomitmen
                    mencetak generasi Qurani, berakhlak mulia, dan berprestasi unggul.</p>
                <div class="d-flex gap-2">
                    <a href="https://www.facebook.com/mim.bojongsana.7" class="btn btn-sm"
                        style="background:var(--gray-700);color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/miyasaceria?igsh=bG41YXdwc3lha3dx" class="btn btn-sm"
                        style="background:var(--gray-700);color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;" target="_blank" rel="noopener">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com/@miyasa6377?si=asfR9FDj7gbq9bIV" class="btn btn-sm"
                        style="background:var(--gray-700);color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;" target="_blank" rel="noopener">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://www.tiktok.com/@miyasa637?is_from_webapp=1&sender_device=pc" class="btn btn-sm"
                        style="background:var(--gray-700);color:white;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;" target="_blank" rel="noopener">
                        <i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5>Link Cepat</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php"><i class="fa-solid fa-chevron-right me-2"
                                style="font-size:0.6rem;"></i>Beranda</a></li>
                    <li class="mb-2"><a href="profil.php"><i class="fa-solid fa-chevron-right me-2"
                                style="font-size:0.6rem;"></i>Profil Sekolah</a></li>
                    <li class="mb-2"><a href="guru.php"><i class="fa-solid fa-chevron-right me-2"
                                style="font-size:0.6rem;"></i>Guru & Staff</a></li>
                    <li class="mb-2"><a href="berita.php"><i class="fa-solid fa-chevron-right me-2"
                                style="font-size:0.6rem;"></i>Berita</a></li>
                    <li class="mb-2"><a href="ppdb.php"><i class="fa-solid fa-chevron-right me-2"
                                style="font-size:0.6rem;"></i>PPDB</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h5>Kontak Kami</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i>
                        <?php echo h(getPengaturan('alamat')); ?></li>
                    <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> <?php echo h(getPengaturan('telepon')); ?>
                    </li>
                    <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> <?php echo h(getPengaturan('email')); ?>
                    </li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5>Lokasi</h5>
                <a href="profil.php#lokasi" class="btn btn-sm"
                    style="background:var(--primary);color:white;border-radius:var(--radius-lg);">
                    <i class="fa-solid fa-map-marker-alt me-1"></i> Lihat Peta
                </a>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> MI Muhammadiyah Bojongsana. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="assets/js/modern-main.js"></script>
<script>
    AOS.init({
        duration: 600,
        easing: 'ease-out-cubic',
        once: false,
        mirror: false,
        anchorPlacement: 'top-bottom',
        offset: 30
    });
    // Navbar scroll effect
    window.addEventListener('scroll', function () {
        var navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>
</body>

</html>