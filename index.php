<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TrainHub - Platform latihan fitness berbasis AI yang membuat rencana workout personal sesuai goal, level, dan peralatan Anda. Gratis dan mudah digunakan!">
    <meta name="keywords" content="workout planner, AI fitness, rencana latihan, personal trainer, gym workout, fitness app, latihan gym">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <title>TrainHub - Rencana Latihan Personal Berbasis AI</title>

    <link href="<?= asset('/views/css/bootstrap-landing.min.css'); ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    </noscript>
</head>

<body>
    <!-- ===== Header / Navigasi ===== -->
    <header class="fixed-top header-blur border-bottom border-gray-800">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="container-fluid row max-w-7xl mx-auto px-4">
                <!-- Logo -->
                <a class="navbar-brand fw-bold text-white" href="" style="font-size: 1.5rem;">
                    Train<span class="text-orange-500">Hub</span>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="justify-content: flex-end;">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Nav Links -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto gap-3 align-items-center" style="margin: auto 0">
                        <li class="nav-item">
                            <a class="nav-link" href="#fitur">Fitur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/login'); ?>">Masuk</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-orange px-4 py-2 rounded-lg" href="<?= url('/register'); ?>">
                                Daftar Gratis
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- ===== Main Content ===== -->
    <main>
        <!-- ===== Hero Section ===== -->
        <section class="pt-5 pb-5" style="padding-top: 10rem; padding-bottom: 8rem;">
            <!-- Background Gradient -->
            <div class="position-absolute top-0 start-0 w-100 h-100" style="z-index: -1; overflow: hidden;">
                <div class="position-absolute top-0 start-0 bg-gradient-orange" style="width: 24rem; height: 24rem; opacity: 0.2; filter: blur(100px);"></div>
                <div class="position-absolute bottom-0 end-0 bg-gradient-orange-dark" style="width: 24rem; height: 24rem; opacity: 0.2; filter: blur(100px);"></div>
            </div>

            <div class="container max-w-7xl">
                <div class="row g-5 align-items-center">

                    <!-- Kolom Teks (Kiri) -->
                    <div class="col-lg-6 text-center text-lg-start">
                        <h1 class="display-5 fw-bold text-white mb-4">
                            Rencana Latihan Personal,
                            <span class="d-block text-gradient-orange">Dibuat oleh AI</span>
                        </h1>
                        <p class="lead text-gray-300 mb-5">
                            Stop menebak-nebak di gym. TrainHub menganalisis target Anda untuk membuat jadwal latihan paling efektif, khusus untuk Anda, setiap hari.
                        </p>
                        <div class="d-flex justify-content-center justify-content-lg-start gap-3">
                            <a href="<?= url('/app'); ?>" class="btn btn-orange btn-lg px-5 rounded-pill shadow-lg">
                                Mulai Gratis
                            </a>
                            <a href="#fitur" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                                Lihat Fitur
                            </a>
                        </div>
                    </div>

                    <!-- Kolom App Mockup (Kanan) -->
                    <div class="col-lg-6 mt-5 mt-lg-0">
                        <div class="card bg-gray-900 border-gray-800 shadow-2xl position-relative">
                            <!-- Efek glow -->
                            <div class="position-absolute top-0 start-0 bg-gradient-orange" style="width: 6rem; height: 6rem; opacity: 0.1; filter: blur(50px); z-index: -1;"></div>

                            <div class="card-body p-4">
                                <!-- Bagian QuickStat -->
                                <h3 class="text-white fw-semibold fs-5 mb-1">Ringkasan Cepat</h3>
                                <p class="text-gray-400 small mb-4">Progres Anda minggu ini</p>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2 small">
                                        <span class="text-gray-300">Workouts Selesai</span>
                                        <span class="text-white fw-medium">3 / 5</span>
                                    </div>
                                    <div class="progress bg-gray-700" style="height: 8px;">
                                        <div class="progress-bar bg-orange" role="progressbar" style="width: 60%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3 small">
                                        <span class="text-gray-300">Total Durasi:</span>
                                        <span class="text-white fw-medium">1j 45m</span>
                                    </div>
                                </div>

                                <hr class="border-gray-700 my-4">

                                <!-- Bagian Calendar -->
                                <h3 class="text-white fw-semibold fs-5 mb-3">Kalender Latihan</h3>
                                <div class="calendar-grid">
                                    <!-- Header Hari -->
                                    <span class="text-gray-500 fw-medium small">SN</span>
                                    <span class="text-gray-500 fw-medium small">SL</span>
                                    <span class="text-gray-500 fw-medium small">RB</span>
                                    <span class="text-gray-500 fw-medium small">KM</span>
                                    <span class="text-gray-500 fw-medium small">JM</span>
                                    <span class="text-gray-500 fw-medium small">SB</span>
                                    <span class="text-gray-500 fw-medium small">MG</span>

                                    <!-- Baris Tanggal -->
                                    <span class="text-gray-600 p-1">26</span>
                                    <span class="text-gray-600 p-1">27</span>
                                    <div class="calendar-day completed">28</div>
                                    <div class="calendar-day today">29</div>
                                    <div class="calendar-day planned">30</div>
                                    <span class="text-gray-300 p-1">31</span>
                                    <span class="text-gray-300 p-1">1</span>
                                    <span class="text-gray-300 p-1">2</span>
                                    <div class="calendar-day completed">3</div>
                                    <span class="text-gray-300 p-1">4</span>
                                    <span class="text-gray-300 p-1">5</span>
                                    <div class="calendar-day planned">6</div>
                                    <span class="text-gray-300 p-1">7</span>
                                    <span class="text-gray-300 p-1">8</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ===== Fitur Section ===== -->
        <section id="fitur" class="py-5 bg-black" style="background-color: rgba(0, 0, 0, 0.5) !important;">
            <div class="container max-w-7xl">
                <div class="text-center mb-5">
                    <span class="text-orange-500 fw-semibold text-uppercase" style="letter-spacing: 0.1em;">Fitur Unggulan</span>
                    <h2 class="display-5 fw-bold text-white mt-2">
                        Semua yang Anda Butuhkan
                    </h2>
                    <p class="lead text-gray-400 mt-4 mx-auto" style="max-width: 42rem;">
                        Dari perencanaan cerdas hingga pelacakan mendetail, kami ada untuk Anda.
                    </p>
                </div>

                <div class="row g-4">
                    <!-- Fitur 1: AI Planner -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card bg-gray-900 border-gray-800 shadow-lg h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-center text-orange-500 rounded-3 mb-3" style="width: 3rem; height: 3rem; background-color: rgba(234, 88, 12, 0.15);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5M12 19.5V21M12 8.25v7.5M15.75 3v1.5M15.75 19.5V21M19.5 8.25h1.5M19.5 12h1.5M19.5 15.75h1.5M15.75 8.25v7.5M8.25 8.25v7.5" />
                                    </svg>
                                </div>
                                <h3 class="fs-5 fw-semibold text-white mb-2">AI Plan Generator</h3>
                                <p class="text-gray-400 mb-0">
                                    Dapatkan rencana mingguan yang dibuat khusus untuk target, level, dan ketersediaan alat Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fitur 2: My Plans -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card bg-gray-900 border-gray-800 shadow-lg h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-center text-orange-500 rounded-3 mb-3" style="width: 3rem; height: 3rem; background-color: rgba(234, 88, 12, 0.15);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                                <h3 class="fs-5 fw-semibold text-white mb-2">My Plans</h3>
                                <p class="text-gray-400 mb-0">
                                    Simpan, kelola, dan akses semua rencana latihan yang Anda terima dari AI, kapan saja.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fitur 3: Calendar Panel -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card bg-gray-900 border-gray-800 shadow-lg h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-center text-orange-500 rounded-3 mb-3" style="width: 3rem; height: 3rem; background-color: rgba(234, 88, 12, 0.15);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <h3 class="fs-5 fw-semibold text-white mb-2">Kalender Interaktif</h3>
                                <p class="text-gray-400 mb-0">
                                    Rencanakan dan tandai latihan Anda sebagai 'selesai' dalam tampilan kalender visual.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fitur 4: Progress Tracking -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card bg-gray-900 border-gray-800 shadow-lg h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-center text-orange-500 rounded-3 mb-3" style="width: 3rem; height: 3rem; background-color: rgba(234, 88, 12, 0.15);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h15.75c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 19.875v-6.75zM3 8.625C3 8.004 3.504 7.5 4.125 7.5h15.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 9.375v-.75zM3 4.125C3 3.504 3.504 3 4.125 3h15.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 4.875v-.75z" />
                                    </svg>
                                </div>
                                <h3 class="fs-5 fw-semibold text-white mb-2">Lacak Progres</h3>
                                <p class="text-gray-400 mb-0">
                                    Lihat kemajuan Anda dari waktu ke waktu dengan grafik total durasi, streak, dan volume latihan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Cara Kerja Section ===== -->
        <section id="cara-kerja" class="py-5">
            <div class="container max-w-7xl">
                <div class="text-center mb-5">
                    <span class="text-orange-500 fw-semibold text-uppercase" style="letter-spacing: 0.1em;">Cara Kerja</span>
                    <h2 class="display-5 fw-bold text-white mt-2">
                        Mulai Dalam 3 Langkah Mudah
                    </h2>
                </div>

                <div class="row g-5">
                    <!-- Step 1 -->
                    <div class="col-12 col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center bg-gray-900 text-orange-500 rounded-circle mx-auto mb-4 border border-2" style="width: 4rem; height: 4rem; font-size: 1.5rem; font-weight: 700; border-color: var(--orange-700) !important;">
                            1
                        </div>
                        <h3 class="fs-4 fw-semibold text-white mb-2">Tentukan Target Anda</h3>
                        <p class="text-gray-400">
                            Beri tahu AI tujuan Anda: apakah itu bulking, cutting, atau sekadar tetap bugar.
                        </p>
                    </div>
                    <!-- Step 2 -->
                    <div class="col-12 col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center bg-gray-900 text-orange-500 rounded-circle mx-auto mb-4 border border-2" style="width: 4rem; height: 4rem; font-size: 1.5rem; font-weight: 700; border-color: var(--orange-700) !important;">
                            2
                        </div>
                        <h3 class="fs-4 fw-semibold text-white mb-2">Dapatkan Rencana</h3>
                        <p class="text-gray-400">
                            AI akan meracik rencana latihan mingguan yang dipersonalisasi, lengkap dengan latihan dan durasi.
                        </p>
                    </div>
                    <!-- Step 3 -->
                    <div class="col-12 col-md-4 text-center">
                        <div class="d-flex align-items-center justify-content-center bg-gray-900 text-orange-500 rounded-circle mx-auto mb-4 border border-2" style="width: 4rem; height: 4rem; font-size: 1.5rem; font-weight: 700; border-color: var(--orange-700) !important;">
                            3
                        </div>
                        <h3 class="fs-4 fw-semibold text-white mb-2">Latihan & Lacak</h3>
                        <p class="text-gray-400">
                            Ikuti sesi latihan Anda, tandai sebagai selesai, dan lihat progres Anda bertambah di kalender.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CTA Section ===== -->
        <section class="py-5 bg-gray-900">
            <div class="container text-center" style="max-width: 56rem;">
                <h2 class="display-5 fw-bold text-white">
                    Siap Mengubah Rutinitas Latihan Anda?
                </h2>
                <p class="lead text-gray-300 mt-4 mb-4">
                    Bergabunglah dengan TrainHub hari ini dan biarkan AI memandu perjalanan fitness Anda.
                </p>
                <a href="<?= url('/register'); ?>" class="btn btn-orange btn-lg px-5 rounded-pill shadow-lg">
                    Daftar Sekarang, Gratis
                </a>
            </div>
        </section>
    </main>

    <!-- ===== Footer ===== -->
    <footer class="py-5 bg-gray-950 border-top" style="border-color: rgba(55, 65, 81, 0.5) !important;">
        <div class="container max-w-7xl">
            <div class="row g-4">
                <!-- Kolom 1: Logo & Social -->
                <div class="col-12 col-md-4">
                    <a href="#" class="fw-bold text-white text-decoration-none" style="font-size: 1.5rem;">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                    <p class="text-gray-400 mt-2 small">
                        Rencana latihan personal berbasis AI.
                    </p>
                </div>

                <!-- Kolom 2: Produk -->
                <div class="col-12 col-md-4">
                    <h4 class="small fw-semibold text-gray-200 text-uppercase mb-3" style="letter-spacing: 0.05em;">Produk</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#fitur" class="text-gray-400 hover:text-white text-decoration-none small">Fitur</a></li>
                        <li class="mb-2"><a href="<?= url('/changelog'); ?>" class="text-gray-400 hover:text-white text-decoration-none small">Changelog</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: KREATOR -->
                <div class="col-12 col-md-4">
                    <h4 class="small fw-semibold text-gray-200 text-uppercase mb-3" style="letter-spacing: 0.05em;">KREATOR</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= url('/about'); ?>" class="text-gray-400 hover:text-white text-decoration-none small">Tentang Kami</a></li>
                        <li class="mb-2"><a href="<?= url('/about'); ?>" class="text-gray-400 hover:text-white text-decoration-none small">Kontak</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-5 pt-4 border-top text-center" style="border-color: rgba(55, 65, 81, 0.5) !important;">
                <p class="small text-gray-500 mb-0">
                    &copy; 2025 TrainHub
                </p>
            </div>
        </div>
    </footer>

    <!-- Vanilla JS for Navbar Toggle (No Bootstrap JS needed!) -->
    <script>
        // Navbar collapse toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.querySelector('.navbar-toggler');
            const collapse = document.querySelector('.navbar-collapse');

            if (toggler && collapse) {
                toggler.addEventListener('click', function() {
                    // Toggle 'show' class
                    collapse.classList.toggle('show');

                    // Toggle aria-expanded
                    const isExpanded = collapse.classList.contains('show');
                    toggler.setAttribute('aria-expanded', isExpanded);
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInside = toggler.contains(event.target) || collapse.contains(event.target);

                    if (!isClickInside && collapse.classList.contains('show')) {
                        collapse.classList.remove('show');
                        toggler.setAttribute('aria-expanded', 'false');
                    }
                });

                // Close menu when clicking on nav links (mobile only)
                const navLinks = collapse.querySelectorAll('.nav-link');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) { // Only on mobile
                            collapse.classList.remove('show');
                            toggler.setAttribute('aria-expanded', 'false');
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>