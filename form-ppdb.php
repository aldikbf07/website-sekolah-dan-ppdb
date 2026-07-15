<?php
$page_title = 'Formulir Pendaftaran';
$current_page = 'ppdb';
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<style>
/* ========================================
   MODERN CALENDAR / DATE PICKER
   ======================================== */

/* Container untuk date input */
.date-input-wrapper {
    position: relative;
}

.date-input-wrapper .form-modern {
    padding-right: 45px;
    cursor: pointer;
    background: white;
}

.date-input-wrapper .calendar-icon {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 1.1rem;
    pointer-events: none;
    transition: color 0.2s ease;
}

.date-input-wrapper .form-modern:focus ~ .calendar-icon {
    color: #2563eb;
}

/* Custom Date Input Styling */
input[type="date"].form-modern {
    position: relative;
    color: #1e293b;
    font-family: 'Outfit', sans-serif;
    font-weight: 500;
}

/* Hide default calendar icon in different browsers */
input[type="date"]::-webkit-calendar-picker-indicator {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    color: transparent;
    background: transparent;
    cursor: pointer;
    opacity: 0;
}

/* Style the calendar popup */
input[type="date"]::-webkit-datetime-edit {
    color: #1e293b;
    font-weight: 500;
}

input[type="date"]::-webkit-datetime-edit-fields-wrapper {
    padding: 0;
}

input[type="date"]::-webkit-datetime-edit-text {
    color: #94a3b8;
    padding: 0 2px;
}

input[type="date"]::-webkit-datetime-edit-day-field:focus,
input[type="date"]::-webkit-datetime-edit-month-field:focus,
input[type="date"]::-webkit-datetime-edit-year-field:focus {
    background: #eff6ff;
    color: #2563eb;
    border-radius: 3px;
    outline: none;
}

/* Placeholder style when date is empty */
input[type="date"]:not(:valid)::-webkit-datetime-edit {
    color: #94a3b8;
}

/* Firefox specific */
input[type="date"].form-modern {
    min-height: 45px;
}

/* Date picker with icon animation */
.date-input-wrapper:hover .calendar-icon {
    color: #2563eb;
    transform: translateY(-50%) scale(1.1);
}

/* Modern calendar popup styling (limited browser support) */
input[type="date"]::-webkit-calendar-picker {
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    border: 1px solid #e2e8f0;
}

/* Form group dengan label */
.form-group-modern {
    margin-bottom: 16px;
}

.form-group-modern .form-label-modern {
    display: block;
    font-weight: 600;
    font-size: 0.88rem;
    color: #334155;
    margin-bottom: 6px;
}

.form-group-modern .form-label-modern .required {
    color: #dc2626;
}

/* Error state */
.date-input-wrapper .form-modern.is-invalid {
    border-color: #dc2626;
    background: #fef2f2;
}

.date-input-wrapper .form-modern.is-invalid ~ .calendar-icon {
    color: #dc2626;
}

.invalid-feedback {
    color: #dc2626;
    font-size: 0.82rem;
    font-weight: 500;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.invalid-feedback i {
    font-size: 0.9rem;
}

/* ============ RESPONSIVE ============ */
@media (max-width: 767px) {
    input[type="date"].form-modern {
        min-height: 42px;
        font-size: 0.9rem;
    }
    
    .date-input-wrapper .calendar-icon {
        font-size: 1rem;
        right: 12px;
    }
}

@media (max-width: 575px) {
    input[type="date"].form-modern {
        min-height: 40px;
        font-size: 0.85rem;
    }
}
</style>
<!-- Page Header -->
<section class="page-header-modern page-header-ppdb">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php">Beranda</a>
            <span class="separator">›</span>
            <a href="ppdb.php">PPDB</a>
            <span class="separator">›</span>
            <span>Formulir</span>
        </div>
        <div class="header-icon" data-aos="zoom-in" data-aos-delay="100">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="200">Formulir Pendaftaran</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="300">Isi formulir di bawah untuk mendaftarkan putra-putri Anda di MI Muhammadiyah Bojongsana</p>
    </div>
</section>

<!-- Form Section -->
<section class="section-modern section-white" style="padding-top: 20px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <!-- Notifikasi Sukses -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert-modern alert-success mb-4" data-aos="fade-up">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                    <div>
                        <strong><?php echo $_SESSION['success']; ?></strong>
                        <?php if (isset($_SESSION['no_pendaftaran'])): ?>
                        <br>No. Pendaftaran: <strong><?php echo $_SESSION['no_pendaftaran']; ?></strong>
                        <br><small>Simpan nomor ini untuk cek status.</small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php unset($_SESSION['success'], $_SESSION['no_pendaftaran']); endif; ?>
                
                <!-- Notifikasi Error -->
                <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert-modern alert-danger mb-4" data-aos="fade-up">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    <div>
                        <strong>Mohon perbaiki kesalahan berikut:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($_SESSION['errors'] as $err): ?>
                            <li><?php echo $err; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php unset($_SESSION['errors']); endif; ?>
                
                <form id="formPendaftaran" method="POST" action="proses-daftar.php" enctype="multipart/form-data">
                    
                   <!-- === DATA SISWA === -->
<div class="card-modern mb-4" data-aos="fade-up">
    <div class="card-body" style="padding: 28px;">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div style="width: 44px; height: 44px; background: var(--primary-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.1rem;">
                <i class="fa-solid fa-child"></i>
            </div>
            <h4 class="mb-0" style="font-weight: 700;">Data Siswa</h4>
        </div>
        
        <div class="row g-3">
            <!-- Nama Lengkap -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-modern" required 
                           placeholder="Nama lengkap calon siswa" 
                           value="<?php echo h($_POST['nama_lengkap'] ?? ''); ?>">
                </div>
            </div>
            
            <!-- Tempat Lahir -->
            <div class="col-md-3">
                <div class="form-group-modern">
                    <label class="form-label-modern">Tempat Lahir <span class="required">*</span></label>
                    <input type="text" name="tempat_lahir" class="form-modern" required 
                           placeholder="Kota/Kab" 
                           value="<?php echo h($_POST['tempat_lahir'] ?? ''); ?>">
                </div>
            </div>
            
            <!-- Tanggal Lahir - CALENDAR MODERN -->
            <div class="col-md-3">
                <div class="form-group-modern">
                    <label class="form-label-modern">Tanggal Lahir <span class="required">*</span></label>
                    <div class="date-input-wrapper">
                        <input type="date" name="tanggal_lahir" class="form-modern" required 
                               value="<?php echo h($_POST['tanggal_lahir'] ?? ''); ?>"
                               id="tanggal_lahir">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                    </div>
                </div>
            </div>
            
            <!-- Jenis Kelamin -->
            <div class="col-md-4">
                <div class="form-group-modern">
                    <label class="form-label-modern">Jenis Kelamin <span class="required">*</span></label>
                    <select name="jenis_kelamin" class="form-modern" required>
                        <option value="">Pilih</option>
                        <option value="Laki-laki" <?php echo (($_POST['jenis_kelamin'] ?? '') == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" <?php echo (($_POST['jenis_kelamin'] ?? '') == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            
            <!-- Agama -->
            <div class="col-md-4">
                <div class="form-group-modern">
                    <label class="form-label-modern">Agama <span class="required">*</span></label>
                    <select name="agama" class="form-modern" required>
                        <option value="Islam" <?php echo (($_POST['agama'] ?? '') == 'Islam') ? 'selected' : ''; ?>>Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                    </select>
                </div>
            </div>
            
            <!-- Anak Ke -->
            <div class="col-md-4">
                <div class="form-group-modern">
                    <label class="form-label-modern">Anak Ke-</label>
                    <input type="text" name="anak_ke" class="form-modern" placeholder="1, 2, 3..."
                           value="<?php echo h($_POST['anak_ke'] ?? ''); ?>">
                </div>
            </div>
            
            <!-- Status Keluarga -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">Status dalam Keluarga</label>
                    <input type="text" name="status_keluarga" class="form-modern" placeholder="Anak Kandung"
                           value="<?php echo h($_POST['status_keluarga'] ?? ''); ?>">
                </div>
            </div>
            
            <!-- Asal TK -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">Asal TK/RA</label>
                    <input type="text" name="asal_tk" class="form-modern" placeholder="Nama TK/RA asal"
                           value="<?php echo h($_POST['asal_tk'] ?? ''); ?>">
                </div>
            </div>
            
            <!-- No STTB -->
            <div class="col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">No. STTB / Ijazah</label>
                    <input type="text" name="no_sttb" class="form-modern" placeholder="Nomor STTB/Ijazah TK"
                           value="<?php echo h($_POST['no_sttb'] ?? ''); ?>">
                </div>
            </div>
        </div>
    </div>
</div>
                    
                    <!-- === ALAMAT === -->
                    <div class="card-modern mb-4" data-aos="fade-up">
                        <div class="card-body" style="padding: 28px;">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div style="width: 44px; height: 44px; background: var(--accent-light); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.1rem;">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                </div>
                                <h4 class="mb-0" style="font-weight: 700;">Alamat</h4>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label-modern">Provinsi <span style="color: #dc2626;">*</span></label>
                                    <select name="provinsi" id="provinsi" class="form-modern" required onchange="loadKabupaten()">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-modern">Kabupaten/Kota <span style="color: #dc2626;">*</span></label>
                                    <select name="kabupaten" id="kabupaten" class="form-modern" required onchange="loadKecamatan()">
                                        <option value="">Pilih Kab/Kota</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-modern">Kecamatan <span style="color: #dc2626;">*</span></label>
                                    <select name="kecamatan" id="kecamatan" class="form-modern" required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern">Alamat Lengkap <span style="color: #dc2626;">*</span></label>
                                    <textarea name="alamat" class="form-modern" rows="2" required 
                                              placeholder="Alamat lengkap (RT/RW, Dusun, Desa)"><?php echo h($_POST['alamat'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- === DATA ORANG TUA === -->
                    <div class="card-modern mb-4" data-aos="fade-up">
                        <div class="card-body" style="padding: 28px;">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div style="width: 44px; height: 44px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #059669; font-size: 1.1rem;">
                                    <i class="fa-solid fa-people-roof"></i>
                                </div>
                                <h4 class="mb-0" style="font-weight: 700;">Data Orang Tua</h4>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-modern">Nama Ayah <span style="color: #dc2626;">*</span></label>
                                    <input type="text" name="nama_ayah" class="form-modern" required placeholder="Nama lengkap ayah"
                                           value="<?php echo h($_POST['nama_ayah'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-modern">NIK Ayah</label>
                                    <input type="text" name="nik_ayah" class="form-modern" placeholder="16 digit" maxlength="16" 
                                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                           value="<?php echo h($_POST['nik_ayah'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-modern">Pekerjaan Ayah</label>
                                    <input type="text" name="pekerjaan_ayah" class="form-modern" placeholder="Pekerjaan"
                                           value="<?php echo h($_POST['pekerjaan_ayah'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">Nama Ibu <span style="color: #dc2626;">*</span></label>
                                    <input type="text" name="nama_ibu" class="form-modern" required placeholder="Nama lengkap ibu"
                                           value="<?php echo h($_POST['nama_ibu'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-modern">NIK Ibu</label>
                                    <input type="text" name="nik_ibu" class="form-modern" placeholder="16 digit" maxlength="16"
                                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                           value="<?php echo h($_POST['nik_ibu'] ?? ''); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-modern">Pekerjaan Ibu</label>
                                    <input type="text" name="pekerjaan_ibu" class="form-modern" placeholder="Pekerjaan"
                                           value="<?php echo h($_POST['pekerjaan_ibu'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label-modern">Alamat Orang Tua (jika berbeda)</label>
                                    <textarea name="alamat_ortu" class="form-modern" rows="2" 
                                              placeholder="Isi jika berbeda dengan alamat siswa"><?php echo h($_POST['alamat_ortu'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-modern">No. HP / WhatsApp <span style="color: #dc2626;">*</span></label>
                                    <input type="text" name="no_hp" class="form-modern" required placeholder="08123456789"
                                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                           value="<?php echo h($_POST['no_hp'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- === UPLOAD BERKAS === -->
                    <div class="card-modern mb-4" data-aos="fade-up">
                        <div class="card-body" style="padding: 28px;">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div style="width: 44px; height: 44px; background: #fef2f2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #dc2626; font-size: 1.1rem;">
                                    <i class="fa-solid fa-upload"></i>
                                </div>
                                <h4 class="mb-0" style="font-weight: 700;">Upload Berkas</h4>
                            </div>
                            <p style="color: var(--gray-500); font-size: 0.9rem; margin-bottom: 16px;">
                                Format: JPG, PNG, atau PDF | Maksimal 2MB per file
                            </p>
                            
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label-modern">Pas Foto 3x4 <span style="color: #dc2626;">*</span></label>
                                    <input type="file" name="file_foto" class="form-modern" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-modern">Akta Kelahiran <span style="color: #dc2626;">*</span></label>
                                    <input type="file" name="file_akta" class="form-modern" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-modern">Kartu Keluarga <span style="color: #dc2626;">*</span></label>
                                    <input type="file" name="file_kk" class="form-modern" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tombol Submit -->
                    <div class="text-center mb-5" data-aos="fade-up">
                        <button type="submit" class="btn-modern btn-primary-modern btn-lg" style="padding: 16px 48px; font-size: 1.1rem;">
                            <i class="fa-solid fa-paper-plane me-2"></i> DAFTAR SEKARANG
                        </button>
                        <p style="color: var(--gray-400); font-size: 0.85rem; margin-top: 12px;">
                            Pastikan data yang diisi sudah benar sebelum mengirim
                        </p>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</section>

<!-- API Wilayah Script -->
<script>
const API_BASE = 'api-wilayah.php';

document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
});

function showLoading(id) {
    const el = document.getElementById(id);
    if (el) el.style.opacity = '0.5';
}

function hideLoading(id) {
    const el = document.getElementById(id);
    if (el) el.style.opacity = '1';
}

async function loadProvinces() {
    showLoading('provinsi');
    try {
        const response = await fetch(API_BASE + '?type=provinces');
        const data = await response.json();
        const select = document.getElementById('provinsi');
        
        data.forEach(prov => {
            const option = document.createElement('option');
            option.value = prov.id;
            option.textContent = prov.name;
            select.appendChild(option);
        });
        
        <?php if (!empty($_POST['provinsi'])): ?>
        select.value = "<?php echo h($_POST['provinsi']); ?>";
        loadKabupaten();
        <?php endif; ?>
    } catch (error) {
        console.error('Gagal memuat provinsi:', error);
    }
    hideLoading('provinsi');
}

async function loadKabupaten() {
    const provinsiId = document.getElementById('provinsi').value;
    const kabupatenSelect = document.getElementById('kabupaten');
    const kecamatanSelect = document.getElementById('kecamatan');
    
    kabupatenSelect.innerHTML = '<option value="">Pilih Kab/Kota</option>';
    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
    
    if (!provinsiId) return;
    
    showLoading('kabupaten');
    try {
        const response = await fetch(API_BASE + '?type=regencies&id=' + provinsiId);
        const data = await response.json();
        
        data.forEach(kab => {
            const option = document.createElement('option');
            option.value = kab.id;
            option.textContent = kab.name;
            kabupatenSelect.appendChild(option);
        });
        
        <?php if (!empty($_POST['kabupaten'])): ?>
        kabupatenSelect.value = "<?php echo h($_POST['kabupaten']); ?>";
        loadKecamatan();
        <?php endif; ?>
    } catch (error) {
        console.error('Gagal memuat kabupaten:', error);
    }
    hideLoading('kabupaten');
}

async function loadKecamatan() {
    const kabupatenId = document.getElementById('kabupaten').value;
    const kecamatanSelect = document.getElementById('kecamatan');
    
    kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
    
    if (!kabupatenId) return;
    
    showLoading('kecamatan');
    try {
        const response = await fetch(API_BASE + '?type=districts&id=' + kabupatenId);
        const data = await response.json();
        
        data.forEach(kec => {
            const option = document.createElement('option');
            option.value = kec.id;
            option.textContent = kec.name;
            kecamatanSelect.appendChild(option);
        });
        
        <?php if (!empty($_POST['kecamatan'])): ?>
        kecamatanSelect.value = "<?php echo h($_POST['kecamatan']); ?>";
        <?php endif; ?>
    } catch (error) {
        console.error('Gagal memuat kecamatan:', error);
    }
    hideLoading('kecamatan');
}

// Konfirmasi submit
document.getElementById('formPendaftaran').addEventListener('submit', function(e) {
    if (!confirm('Pastikan data sudah benar. Lanjutkan pendaftaran?')) {
        e.preventDefault();
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>