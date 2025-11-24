<?php
session_start();
include '../config.php';
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - TrainHub</title>
    <link href="<?php echo asset('/views/css/tailwind.css'); ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    </noscript>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .team-card {
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-8px);
        }

        .social-link {
            transition: all 0.2s ease;
        }

        .social-link:hover {
            transform: scale(1.1);
        }

        .gradient-border {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            padding: 2px;
            border-radius: 1rem;
        }

        .avatar-placeholder {
            background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            color: white;
        }
    </style>
</head>

<body class="bg-black text-gray-100 min-h-screen">

    <!-- Navigation -->
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50 backdrop-blur-sm bg-opacity-90">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="<?= url('/'); ?>" class="text-2xl font-bold text-white tracking-tight">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                    <div class="hidden md:flex items-center gap-2 text-sm text-gray-300">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                        <span>Meet The Team</span>
                    </div>
                </div>
                <a href="javascript:history.back();" class="text-gray-300 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-white mb-4">
                Tentang <span class="text-orange-500">Kami</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Tim kecil dengan semangat besar untuk membangun solusi fitness yang inovatif
            </p>
        </div>

        <!-- Team Members -->
        <div class="grid md:grid-cols-2 gap-8 mb-16">

            <!-- Member 1: Bintang -->
            <div class="team-card gradient-border">
                <div class="bg-gray-900 rounded-[14px] p-8 h-full">
                    <div class="flex flex-col items-center text-center">
                        <!-- Avatar Placeholder (foto menyusul) -->
                        <img src="<?php echo asset('/images/bintang.webp'); ?>" alt="Bintang Ramadhan"
                            class="w-32 h-32 rounded-full mb-6 object-cover border-2 border-orange-500 shadow-lg">

                        <!-- Name & Role -->
                        <h2 class="text-2xl font-bold text-white mb-2">Bintang Ramadhan</h2>
                        <div class="flex flex-wrap justify-center gap-2 mb-4">
                            <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-semibold">Project Manager</span>
                            <span class="bg-orange-500/20 text-orange-400 px-3 py-1 rounded-full text-xs font-semibold">Backend Developer</span>
                            <span class="bg-cyan-500/20 text-cyan-400 px-3 py-1 rounded-full text-xs font-semibold">AI Specialist</span>
                        </div>

                        <!-- Bio -->
                        <p class="text-gray-300 mb-6 italic">"Di laptop gw jalan kok, sumpah."</p>

                        <!-- Social Media -->
                        <div class="flex gap-4">
                            <a href="https://www.linkedin.com/in/tang-ramadhan" target="_blank"
                                class="social-link bg-[#0077b5] hover:bg-[#006396] p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                </svg>
                            </a>
                            <a href="https://github.com/TangRmdhn" target="_blank"
                                class="social-link bg-gray-800 hover:bg-gray-700 p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                </svg>
                            </a>
                            <a href="https://instagram.com/tangrmdhn" target="_blank"
                                class="social-link bg-gradient-to-br from-purple-600 to-pink-500 p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </a>
                            <a href="mailto:bintangramadhan0710@gmail.com"
                                class="social-link bg-red-600 hover:bg-red-700 p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member 2: Arsyadi -->
            <div class="team-card gradient-border">
                <div class="bg-gray-900 rounded-[14px] p-8 h-full">
                    <div class="flex flex-col items-center text-center">
                        <!-- Avatar -->
                        <img src="<?php echo asset('/images/arsyadi.webp'); ?>" alt="Arsyadi Indra Hasan Prihambada"
                            class="w-32 h-32 rounded-full mb-6 object-cover border-2 border-orange-500 shadow-lg">

                        <!-- Name & Role -->
                        <h2 class="text-2xl font-bold text-white mb-2">Arsyadi Indra Hasan Prihambada</h2>
                        <div class="flex flex-wrap justify-center gap-2 mb-4">
                            <span class="bg-orange-500/20 text-orange-400 px-3 py-1 rounded-full text-xs font-semibold">Fullstack Developer</span>
                            <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-semibold">Quality Assurance</span>
                            <span class="bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full text-xs font-semibold">Web Administrator</span>
                        </div>

                        <!-- Bio -->
                        <p class="text-gray-300 mb-6 italic">"Niat benerin satu bug, malah nambah lima."</p>

                        <!-- Social Media -->
                        <div class="flex gap-4">
                            <a href="https://www.linkedin.com/in/arsyadi-indra-hasan-prihambada-991768392" target="_blank"
                                class="social-link bg-[#0077b5] hover:bg-[#006396] p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                </svg>
                            </a>
                            <a href="https://github.com/indraprhmbd" target="_blank"
                                class="social-link bg-gray-800 hover:bg-gray-700 p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                                </svg>
                            </a>
                            <a href="https://instagram.com/indraprhmbd_" target="_blank"
                                class="social-link bg-gradient-to-br from-purple-600 to-pink-500 p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </a>
                            <a href="mailto:indraprhmbd@gmail.com"
                                class="social-link bg-red-600 hover:bg-red-700 p-3 rounded-lg transition">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Project Info -->
        <div class="bg-gradient-to-r from-orange-500/10 to-red-500/10 border border-orange-500/20 rounded-xl p-8 text-center">
            <h3 class="text-2xl font-bold text-white mb-3">TrainHub Project</h3>
            <p class="text-gray-300 mb-6 max-w-2xl mx-auto">
                Platform fitness berbasis AI yang membantu pengguna mencapai tujuan kesehatan mereka dengan program latihan yang dipersonalisasi.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="https://github.com/TangRmdhn/TrainHub" target="_blank"
                    class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    View Repository
                </a>
                <a href="<?= url('/changelog'); ?>"
                    class="inline-flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    View Changelog
                </a>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800 py-8 mt-16">
        <div class="max-w-6xl mx-auto px-4 text-center text-gray-400 text-sm">
            <p>© 2025 TrainHub</p>
        </div>
    </footer>

</body>

</html>