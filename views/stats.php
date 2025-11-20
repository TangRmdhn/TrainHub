<?php
session_start();
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - TrainHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-black text-gray-100 min-h-screen">

    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo -->
                <div class="flex items-center">
                    <a href="app.php" class="text-2xl font-bold text-white">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                </div>

                <!-- Center: Desktop Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="app.php" class="text-gray-300 hover:text-white transition">Dashboard</a>
                    <a href="plans.php" class="text-gray-300 hover:text-white transition">My Plans</a>
                    <a href="calendar.php" class="text-gray-300 hover:text-white transition">Calendar</a>
                    <a href="stats.php" class="text-orange-500 font-semibold">Statistics</a>
                </div>

                <!-- Right: Logout (Desktop) -->
                <div class="hidden md:flex">
                    <a href="../controllers/logout.php" class="bg-gray-800 hover:bg-red-900/30 text-gray-300 hover:text-red-400 px-4 py-2 rounded-lg text-sm font-medium transition-all border border-gray-700 hover:border-red-800">
                        Logout</a>
                </div>

                <!-- Mobile: Hamburger Button -->
                <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-gray-800 border-t border-gray-700">
            <div class="px-4 py-3 space-y-3">
                <a href="app.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Dashboard</a>
                <a href="plans.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">My Plans</a>
                <a href="calendar.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Calendar</a>
                <a href="stats.php" class="block px-3 py-2 rounded-lg text-orange-500 font-semibold bg-gray-900">Statistics</a>
                <div class="pt-3 border-t border-gray-700">
                    <a href="../controllers/logout.php" class="block px-3 py-2 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 transition text-center font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuBtn?.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    </script>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Statistik Latihan</h1>
            <p class="text-gray-400 mt-2">Pantau progress dan konsistensi latihanmu disini.</p>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Streak Card -->
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 flex items-center">
                <div class="p-4 bg-orange-900/20 rounded-full text-orange-500 mr-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium uppercase tracking-wider">Current Streak</p>
                    <h3 class="text-3xl font-bold text-white" id="streak-count">...</h3>
                    <p class="text-xs text-gray-500 mt-1">Hari berturut-turut</p>
                </div>
            </div>

            <!-- Total Workouts Card -->
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 flex items-center">
                <div class="p-4 bg-blue-900/20 rounded-full text-blue-500 mr-5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 font-medium uppercase tracking-wider">Total Workouts</p>
                    <h3 class="text-3xl font-bold text-white" id="total-workouts">...</h3>
                    <p class="text-xs text-gray-500 mt-1">Sesi selesai</p>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h3 class="text-xl font-bold text-white mb-6">Aktivitas 30 Hari Terakhir</h3>
            <div class="relative h-80 w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            const streakEl = document.getElementById('streak-count');
            const totalEl = document.getElementById('total-workouts');
            const ctx = document.getElementById('activityChart').getContext('2d');

            try {
                const response = await fetch('../controllers/api_stats.php');
                const data = await response.json();

                if (data.error) {
                    console.error(data.error);
                    return;
                }

                // Update Stats
                streakEl.innerText = data.current_streak;
                totalEl.innerText = data.total_workouts;

                // Render Chart
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.chart_labels,
                        datasets: [{
                            label: 'Sesi Latihan',
                            data: data.chart_data,
                            borderColor: '#f97316', // Orange-500
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#f97316',
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: '#1f2937',
                                titleColor: '#f3f4f6',
                                bodyColor: '#d1d5db',
                                borderColor: '#374151',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#9ca3af'
                                },
                                grid: {
                                    color: '#374151'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9ca3af',
                                    maxTicksLimit: 10
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });

            } catch (error) {
                console.error('Error fetching stats:', error);
                streakEl.innerText = '-';
                totalEl.innerText = '-';
            }
        });
    </script>
</body>

</html>