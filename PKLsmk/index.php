<?php
// BOOTSTRAP UNTUK SEMUA HALAMAN - HARUS DI ATAS
require_once 'bootstrap.php';

// Routing system
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';
$section = $_GET['section'] ?? 'dashboard';

// Authentication check
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_role = $_SESSION['role'];
    $nama_lengkap = $_SESSION['nama_lengkap'];
    
    // Redirect based on role for specific pages
    if ($page == 'admin' && $user_role != 'admin') {
        header('Location: index.php?page=dashboard');
        exit();
    }
    if ($page == 'siswa' && $user_role != 'siswa') {
        header('Location: index.php?page=dashboard');
        exit();
    }
}

// Set page title
$titles = [
    'home' => 'Pendaftaran PKL - SMK Negeri 7 Batam',
    'dashboard' => 'Dashboard - PKL System',
    'admin' => 'Admin Dashboard - PKL System', 
    'siswa' => 'Dashboard Siswa - PKL System',
    'pendaftaran' => 'Form Pendaftaran PKL',
    'status' => 'Status Pendaftaran'
];
$page_title = $titles[$page] ?? 'Pendaftaran PKL - SMK Negeri 7 Batam';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (in_array($page, ['admin', 'dashboard'])): ?>
        <link rel="stylesheet" href="assets/css/admin.css">
    <?php endif; ?>
    
    <!-- External Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="antialiased">
    <?php if (in_array($page, ['home'])): ?>
        <?php include 'sections/public/header.php'; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php
        // Routing Content
        switch($page) {
            case 'home':
                include 'sections/public/home.php';
                break;
                
            case 'dashboard':
                if (!isset($_SESSION['user_id'])) {
                    header('Location: login.php');
                    exit();
                }
                if ($_SESSION['role'] == 'admin') {
                    include 'sections/admin/dashboard.php';
                } else {
                    include 'sections/siswa/dashboard.php';
                }
                break;
                
            case 'admin':
                if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
                    header('Location: index.php?page=dashboard');
                    exit();
                }
                $section_file = "sections/admin/$section.php";
                if (file_exists($section_file)) {
                    include $section_file;
                } else {
                    echo "<div class='container mx-auto px-4 py-8'>";
                    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>";
                    echo "File section tidak ditemukan: $section_file";
                    echo "</div></div>";
                }
                break;
                
            case 'siswa':
                if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
                    header('Location: index.php?page=dashboard');
                    exit();
                }
                $section_file = "sections/siswa/$section.php";
                if (file_exists($section_file)) {
                    include $section_file;
                } else {
                    echo "<div class='container mx-auto px-4 py-8'>";
                    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>";
                    echo "File section tidak ditemukan: $section_file";
                    echo "</div></div>";
                }
                break;
                
            default:
                include 'sections/public/home.php';
        }
        ?>
    </main>

    <!-- Footer hanya untuk halaman public -->
    <?php if (in_array($page, ['home'])): ?>
        <?php include 'sections/public/footer.php'; ?>
    <?php endif; ?>

    <!-- Modal Jurusan Detail -->
    <div id="modalJurusan" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="assets/js/main.js"></script>
    <?php if (in_array($page, ['admin', 'dashboard'])): ?>
        <script src="assets/js/admin.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'admin' && $section == 'manage-perusahaan'): ?>
        <script src="assets/js/manage-perusahaan.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'admin' && $section == 'manage-jurusan'): ?>
        <script src="assets/js/manage-jurusan.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'admin' && $section == 'manage-pendaftaran'): ?>
        <script src="assets/js/manage-pendaftaran.js"></script>
    <?php endif; ?>
    
    <?php if ($page == 'siswa' && $section == 'pendaftaran'): ?>
        <script src="assets/js/pendaftaran-siswa.js"></script>
    <?php endif; ?>
</body>
</html>