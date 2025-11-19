<!DOCTYPE html>
<html lang="id" class="scroll-smooth dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrainHub - Rencana Latihan Personal Berbasis AI</title>

    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Konfigurasi Tailwind (Opsional, tapi bagus untuk font) -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'brand': {
                            'light': '#f97316', // orange-500
                            'DEFAULT': '#ea580c', // orange-600
                            'dark': '#c2410c', // orange-700
                        }
                    }
                }
            }
        }
    </script>

    <!-- Load Google Font (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="views/css/style.css">

    <style>
        /* Menggunakan font Inter dari config */
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Efek blur di background header */
        .header-blur {
            background-color: rgba(0, 0, 0, 0.8);
            /* bg-black with 80% opacity */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-black text-gray-100 antialiased">

    <!-- ===== Header / Navigasi ===== -->
    <header class="fixed top-0 left-0 right-0 z-50 header-blur border-b border-gray-800/50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="#" class="text-2xl font-bold text-white">
                        Train<span class="text-brand-light">Hub</span>
                    </a>
                </div>

                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="#fitur" class="text-gray-300 hover:text-white transition-colors duration-200">Fitur</a>
                    <a href="#cara-kerja" class="text-gray-300 hover:text-white transition-colors duration-200">Cara Kerja</a>
                </div>

                <!-- Tombol CTA (Desktop) -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="views/login.php" class="text-gray-300 hover:text-white transition-colors duration-200">Masuk</a>
                    <a href="views/register.php" class="bg-brand-DEFAULT hover:bg-brand-dark text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300">
                        Daftar Gratis
                    </a>
                </div>

                <!-- Tombol Menu (Mobile) -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Menu Mobile (Hidden) -->
        <div id="mobile-menu" class="md:hidden hidden bg-gray-900">
            <a href="#fitur" class="block py-2 px-4 text-gray-300 hover:bg-gray-800">Fitur</a>
            <a href="#cara-kerja" class="block py-2 px-4 text-gray-300 hover:bg-gray-800">Cara Kerja</a>
            <a href="#" class="block py-2 px-4 text-gray-300 hover:bg-gray-800">Harga</a>
            <div class="border-t border-gray-700 p-4 space-y-2">
                <a href="#" class="block w-full text-center text-gray-300 hover:text-white transition-colors duration-200">Masuk</a>
                <a href="#" class="block w-full text-center bg-brand-DEFAULT hover:bg-brand-dark text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300">
                    Daftar Gratis
                </a>
            </div>
        </div>
    </header>

    <!-- ===== Main Content ===== -->
    <main>

        <!-- ===== Hero Section ===== -->
        <section class="pt-40 pb-24 md:pt-48 md:pb-32 relative overflow-hidden">
            <!-- Background Gradient -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-0 left-0 w-96 h-96 bg-brand-DEFAULT rounded-full opacity-20 blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-dark rounded-full opacity-20 blur-3xl"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Ganti layout jadi 2 kolom, tambahkan items-center -->
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                    <!-- Kolom Teks (Kiri) -->
                    <div class="text-center lg:text-left">
                        <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-white tracking-tight mb-6">
                            Rencana Latihan Personal,
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-brand-light to-brand-DEFAULT">Dibuat oleh AI</span>
                        </h1>
                        <p class="max-w-xl mx-auto lg:mx-0 text-lg md:text-xl text-gray-300 mb-10">
                            Stop menebak-nebak di gym. TrainHub menganalisis target Anda untuk membuat jadwal latihan paling efektif, khusus untuk Anda, setiap hari.
                        </p>
                        <div class="flex justify-center lg:justify-start items-center gap-x-4">
                            <a href="views/app.php" class="bg-brand-DEFAULT hover:bg-brand-dark text-white font-semibold py-3 px-8 rounded-full text-lg transition-colors duration-300 shadow-lg shadow-brand-dark/30">
                                Mulai Gratis
                            </a>
                            <a href="#fitur" class="text-gray-300 hover:text-white font-medium py-3 px-6 rounded-full transition-colors duration-300">
                                Lihat Fitur
                            </a>
                        </div>
                    </div>

                    <!-- Kolom App Mockup (Kanan) -->
                    <div class="mt-12 lg:mt-0">
                        <!-- Ini adalah mockup 'Quick Stat' dan 'Calendar' yang lu minta -->
                        <div class="bg-gray-900/80 backdrop-blur-sm p-5 sm:p-6 rounded-2xl border border-gray-800 shadow-2xl relative">
                            <!-- Efek glow (subtle) -->
                            <div class="absolute -top-1 -left-1 w-24 h-24 bg-brand-DEFAULT opacity-10 blur-3xl -z-10"></div>

                            <!-- Bagian QuickStat -->
                            <h3 class="text-white font-semibold text-lg">Ringkasan Cepat</h3>
                            <p class="text-sm text-gray-400 mb-4">Progres Anda minggu ini</p>

                            <div class="space-y-3 mb-5">
                                <div class="text-sm">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-gray-300">Workouts Selesai</span>
                                        <span class="text-white font-medium">3 / 5</span>
                                    </div>
                                    <div class="w-full bg-gray-700 rounded-full h-2">
                                        <div class="bg-brand-DEFAULT h-2 rounded-full" style="width: 60%"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between text-sm pt-2">
                                    <span class="text-gray-300">Total Durasi:</span>
                                    <span class="text-white font-medium">1j 45m</span>
                                </div>
                            </div>

                            <hr class="border-gray-700 my-5">

                            <!-- Bagian Calendar -->
                            <h3 class="text-white font-semibold text-lg mb-3">Kalender Latihan</h3>
                            <div class="grid grid-cols-7 gap-2 text-center text-sm">
                                <!-- Header Hari -->
                                <span class="text-gray-500 font-medium">SN</span>
                                <span class="text-gray-500 font-medium">SL</span>
                                <span class="text-gray-500 font-medium">RB</span>
                                <span class="text-gray-500 font-medium">KM</span>
                                <span class="text-gray-500 font-medium">JM</span>
                                <span class="text-gray-500 font-medium">SB</span>
                                <span class="text-gray-500 font-medium">MG</span>

                                <!-- Baris Tanggal (Mockup) -->
                                <span class="text-gray-600 p-1.5">26</span> <!-- Tanggal 'off' -->
                                <span class="text-gray-600 p-1.5">27</span>

                                <!-- Latihan Selesai -->
                                <div class="bg-brand-dark/50 text-brand-light font-bold rounded-full p-1.5 aspect-square flex items-center justify-center">28</div>

                                <!-- Hari Ini / Rencana Aktif -->
                                <div class="bg-brand-DEFAULT text-white font-bold rounded-full p-1.5 aspect-square ring-2 ring-brand-light flex items-center justify-center">29</div>

                                <!-- Rencana Berikutnya -->
                                <div class="bg-gray-700 text-gray-200 rounded-full p-1.5 aspect-square flex items-center justify-center">30</div>

                                <span class="text-gray-300 p-1.5">31</span>
                                <span class="text-gray-300 p-1.5">1</span>
                                <span class="text-gray-300 p-1.5">2</span>

                                <!-- Latihan Selesai -->
                                <div class="bg-brand-dark/50 text-brand-light font-bold rounded-full p-1.5 aspect-square flex items-center justify-center">3</div>

                                <span class="text-gray-300 p-1.5">4</span>
                                <span class="text-gray-300 p-1.5">5</span>

                                <!-- Rencana Berikutnya -->
                                <div class="bg-gray-700 text-gray-200 rounded-full p-1.5 aspect-square flex items-center justify-center">6</div>

                                <span class="text-gray-300 p-1.5">7</span>
                                <span class="text-gray-300 p-1.5">8</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Mockup Aplikasi yang lama (di bawah) sekarang kita HAPUS -->
            <!-- <div class="max-w-5xl mx-auto mt-20 px-4 sm:px-6 lg:px-8"> ... </div> -->
        </section>

        <!-- ===== Fitur Section ===== -->
        <section id="fitur" class="py-24 bg-black/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-brand-light font-semibold uppercase tracking-wider">Fitur Unggulan</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mt-2">
                        Semua yang Anda Butuhkan
                    </h2>
                    <p class="text-lg text-gray-400 mt-4 max-w-2xl mx-auto">
                        Dari perencanaan cerdas hingga pelacakan mendetail, kami ada untuk Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Fitur 1: AI Planner -->
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-lg">
                        <div class="w-12 h-12 bg-brand-dark text-brand-light rounded-lg flex items-center justify-center mb-4">
                            <!-- Heroicon: cpu-chip -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5M12 19.5V21M12 8.25v7.5M15.75 3v1.5M15.75 19.5V21M19.5 8.25h1.5M19.5 12h1.5M19.5 15.75h1.5M15.75 8.25v7.5M8.25 8.25v7.5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">AI Plan Generator</h3>
                        <p class="text-gray-400">
                            Dapatkan rencana mingguan yang dibuat khusus untuk target, level, dan ketersediaan alat Anda.
                        </p>
                    </div>

                    <!-- Fitur 2: Exercise Library -->
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-lg">
                        <div class="w-12 h-12 bg-brand-dark text-brand-light rounded-lg flex items-center justify-center mb-4">
                            <!-- Heroicon: book-open -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Pustaka Latihan</h3>
                        <p class="text-gray-400">
                            Ratusan gerakan dengan instruksi jelas dan video, dikategorikan untuk pencarian mudah.
                        </p>
                    </div>

                    <!-- Fitur 3: Calendar Panel -->
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-lg">
                        <div class="w-12 h-12 bg-brand-dark text-brand-light rounded-lg flex items-center justify-center mb-4">
                            <!-- Heroicon: calendar-days -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Kalender Interaktif</h3>
                        <p class="text-gray-400">
                            Rencanakan, pindahkan, dan tandai latihan Anda sebagai 'selesai' dalam tampilan kalender visual.
                        </p>
                    </div>

                    <!-- Fitur 4: Progress Tracking -->
                    <div class="bg-gray-900 p-6 rounded-xl border border-gray-800 shadow-lg">
                        <div class="w-12 h-12 bg-brand-dark text-brand-light rounded-lg flex items-center justify-center mb-4">
                            <!-- Heroicon: chart-bar-square -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h15.75c.621 0 1.125.504 1.125 1.125v6.75c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 19.875v-6.75zM3 8.625C3 8.004 3.504 7.5 4.125 7.5h15.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 9.375v-.75zM3 4.125C3 3.504 3.504 3 4.125 3h15.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 013 4.875v-.75z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Lacak Progres</h3>
                        <p class="text-gray-400">
                            Lihat kemajuan Anda dari waktu ke waktu dengan grafik total durasi, streak, dan volume latihan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== Cara Kerja Section ===== -->
        <section id="cara-kerja" class="py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="text-brand-light font-semibold uppercase tracking-wider">Cara Kerja</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mt-2">
                        Mulai Dalam 3 Langkah Mudah
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-900 text-brand-light rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 border-2 border-brand-dark">
                            1
                        </div>
                        <h3 class="text-2xl font-semibold text-white mb-2">Tentukan Target Anda</h3>
                        <p class="text-gray-400">
                            Beri tahu AI tujuan Anda: apakah itu bulking, cutting, atau sekadar tetap bugar.
                        </p>
                    </div>
                    <!-- Step 2 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-900 text-brand-light rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 border-2 border-brand-dark">
                            2
                        </div>
                        <h3 class="text-2xl font-semibold text-white mb-2">Dapatkan Rencana</h3>
                        <p class="text-gray-400">
                            AI akan meracik rencana latihan mingguan yang dipersonalisasi, lengkap dengan latihan dan durasi.
                        </p>
                    </div>
                    <!-- Step 3 -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gray-900 text-brand-light rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4 border-2 border-brand-dark">
                            3
                        </div>
                        <h3 class="text-2xl font-semibold text-white mb-2">Latihan & Lacak</h3>
                        <p class="text-gray-400">
                            Ikuti sesi latihan Anda, tandai sebagai selesai, dan lihat progres Anda bertambah di kalender.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== CTA Section ===== -->
        <section class="py-24 bg-gray-900">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white">
                    Siap Mengubah Rutinitas Latihan Anda?
                </h2>
                <p class="text-lg text-gray-300 mt-4 mb-8">
                    Bergabunglah dengan TrainHub hari ini dan biarkan AI memandu perjalanan fitness Anda.
                    </D>
                    <a href="#" class="bg-brand-DEFAULT hover:bg-brand-dark text-white font-semibold py-3 px-8 rounded-full text-lg transition-colors duration-300 shadow-lg shadow-brand-dark/30">
                        Daftar Sekarang, Gratis
                    </a>
            </div>
        </section>
    </main>

    <!-- ===== Footer ===== -->
    <footer class="py-16 bg-gray-950 border-t border-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Kolom 1: Logo & Social -->
                <div>
                    <a href="#" class="text-2xl font-bold text-white">
                        Train<span class="text-brand-light">Hub</span>
                    </a>
                    <p class="text-gray-400 mt-2 text-sm">
                        Rencana latihan personal berbasis AI.
                    </p>
                    <!-- Social media icons bisa ditambah di sini -->
                </div>

                <!-- Kolom 2: Produk -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 uppercase tracking-wider">Produk</h4>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#fitur" class="text-gray-400 hover:text-white text-sm transition-colors">Fitur</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Harga</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Changelog</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Perusahaan -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 uppercase tracking-wider">Perusahaan</h4>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Kontak</a></li>
                    </ul>
                </div>

                <!-- Kolom 4: Legal -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-200 uppercase tracking-wider">Legal</h4>
                    <ul class="mt-4 space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-12 pt-8 border-t border-gray-800/50 text-center">
                <p class="text-sm text-gray-500">
                    &copy; 2025 TrainHub. Dibuat dengan 🔥 oleh Bintang.
                </p>
            </div>
        </div>
    </footer>

    <!-- Script untuk Mobile Menu Toggle -->
    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>

</body>

</html>