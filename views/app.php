<?php
session_start();
include '../koneksi.php';
include '../config.php';

// 1. Cek Login
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: " . url("/login"));
    exit;
}
$user_id = $_SESSION['user_id'];

// 2. Ambil data profil user
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = $koneksi->query($sql);
if ($result->num_rows == 0) {
    header("Location: " . url("/logout"));
    exit;
}
$user = $result->fetch_assoc();

// Cek kalau belum screening
if (empty($user['fitness_goal'])) {
    header("Location: " . url("/screening"));
    exit;
}

// 3. Siapkan data profil untuk dikirim ke API Python
// Pastikan format field sama persis Pydantic model di Python
$profile_for_api = [
    'username' => $user['username'],
    'gender' => $user['gender'],
    'age' => (int)$user['age'],
    'weight' => (float)$user['weight'],
    'height' => (int)$user['height'],
    'fitness_goal' => $user['fitness_goal'],
    'fitness_level' => $user['fitness_level'],
    'equipment_access' => $user['equipment_access'],
    'days_per_week' => (int)$user['days_per_week'],
    'minutes_per_session' => (int)$user['minutes_per_session'],
    'injuries' => $user['injuries'] ?? ''
];
$user_profile_json = json_encode($profile_for_api);
?>
<!DOCTYPE html>
<html lang="id" class="dark">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TrainHub</title>
    <link rel="shortcut icon" href="../favicon.ico" type="image/x-icon">
    <link href="<?php echo asset('/views/css/tailwind.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .plan-coach-note {
            background-color: #1f2937;
            border-left: 4px solid #ea580c;
            padding: 1rem;
            border-radius: 0 0.5rem 0.5rem 0;
            font-style: italic;
            color: #d1d5db;
            margin-bottom: 1.5rem;
        }

        /* Tampilan Card Hari */
        .day-plan-card {
            background: #111827;
            border: 1px solid #374151;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .day-plan-card:hover {
            border-color: #f97316;
            transform: translateY(-2px);
        }

        .day-plan-card.off-day {
            background: #1f2937;
            opacity: 0.6;
            border-style: dashed;
        }

        .day-plan-header {
            padding: 16px;
            border-bottom: 1px solid #374151;
            background: rgba(255, 255, 255, 0.02);
        }

        .day-plan-list {
            padding: 16px;
            font-size: 14px;
            flex-grow: 1;
        }

        .day-plan-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .day-plan-list li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #374151;
        }

        .day-plan-list li:last-child {
            border-bottom: none;
        }

        /* Week Selector */
        .week-selector {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            margin-bottom: 1rem;
        }

        .week-selector-btn {
            padding: 12px 4px;
            border: 1px solid #374151;
            background: #1f2937;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .week-selector-btn:hover:not([disabled]) {
            background: #374151;
            border-color: #9ca3af;
        }

        .week-selector-btn.selected {
            background: #ea580c;
            color: white;
            border-color: #f97316;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
        }

        .week-selector-btn[disabled] {
            opacity: 0.4;
            cursor: not-allowed;
            background: #111827;
        }

        .day-name {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .week-selector-btn.selected .day-name {
            color: #fed7aa;
        }

        .date-num {
            font-size: 16px;
            font-weight: 700;
            margin-top: 2px;
        }

        .week-label {
            grid-column: 1 / -1;
            font-size: 12px;
            font-weight: 600;
            color: #9ca3af;
            padding: 8px 0 4px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .week-label::after {
            content: '';
            height: 1px;
            background: #374151;
            flex-grow: 1;
        }
    </style>
</head>

<body class="bg-black text-gray-100 min-h-screen flex flex-col">


    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50 backdrop-blur-md bg-opacity-80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo -->
                <div class="flex items-center">
                    <a href="<?php echo url('/app'); ?>" class="text-2xl font-bold text-white tracking-tight">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                </div>

                <!-- Center: Desktop Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="<?php echo url('/app'); ?>" class="text-orange-500 font-semibold">Dashboard</a>
                    <a href="<?php echo url('/plans'); ?>" class="text-gray-300 hover:text-white transition">My Plans</a>
                    <a href="<?php echo url('/calendar'); ?>" class="text-gray-300 hover:text-white transition">Calendar</a>
                    <a href="<?php echo url('/stats'); ?>" class="text-gray-300 hover:text-white transition">Statistics</a>
                </div>

                <!-- Right: User/Logout (Desktop) -->
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-right leading-tight">
                        <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($user['username']); ?></div>
                        <div class="text-xs text-gray-400"><?php echo htmlspecialchars($user['fitness_goal']); ?></div>
                    </div>
                    <a href="<?php echo url('/logout'); ?>" class="bg-gray-800 hover:bg-red-900/30 text-gray-300 hover:text-red-400 px-4 py-2 rounded-lg text-sm font-medium transition-all border border-gray-700 hover:border-red-800">
                        Logout
                    </a>
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
                <a href="<?php echo url('/app'); ?>" class="block px-3 py-2 rounded-lg text-orange-500 font-semibold bg-gray-900">Dashboard</a>
                <a href="<?php echo url('/plans'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">My Plans</a>
                <a href="<?php echo url('/calendar'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Calendar</a>
                <a href="<?php echo url('/stats'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Statistics</a>
                <div class="pt-3 border-t border-gray-700">
                    <div class="px-3 py-2 text-sm font-medium text-white"><?php echo htmlspecialchars($user['username']); ?></div>
                    <div class="px-3 pb-2 text-xs text-gray-400"><?php echo htmlspecialchars($user['fitness_goal']); ?></div>
                    <a href="<?php echo url('/logout'); ?>" class="block px-3 py-2 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 transition text-center font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>


    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4 border-b border-gray-800 pb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Dashboard Latihan</h1>
                <p class="text-gray-400 mt-2">Selamat datang, <span class="text-orange-400 font-semibold"><?php echo htmlspecialchars($user['username']); ?></span>! AI siap bantu goal <span class="text-white"><?php echo htmlspecialchars($user['fitness_goal']); ?></span> kamu.</p>
            </div>
            <a href="<?php echo url('/screening'); ?>" class="text-xs font-medium text-gray-500 hover:text-orange-400 transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                </svg>
                Edit Profil Fisik
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-4 space-y-6">

                <div class="bg-gray-900 rounded-xl border border-gray-800 p-6 shadow-lg">
                    <h3 class="text-sm uppercase font-bold text-gray-500 tracking-wider mb-4">Parameter Anda</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-800">
                            <span class="text-gray-400 text-sm">Frekuensi</span>
                            <span class="text-white font-medium"><?php echo $user['days_per_week']; ?> Hari/Minggu</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-800">
                            <span class="text-gray-400 text-sm">Durasi</span>
                            <span class="text-white font-medium"><?php echo $user['minutes_per_session']; ?> Menit</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-800">
                            <span class="text-gray-400 text-sm">Alat</span>
                            <span class="text-white font-medium text-right truncate w-1/2"><?php echo htmlspecialchars($user['equipment_access']); ?></span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-400 text-sm">Level</span>
                            <span class="bg-gray-800 text-gray-300 px-2 py-1 rounded text-xs font-bold uppercase"><?php echo htmlspecialchars($user['fitness_level']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <div class="absolute -inset-0.5 bg-gradient-to-r from-orange-600 to-red-600 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-500"></div>
                    <div class="relative bg-gray-900 rounded-xl border border-gray-800 p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-orange-600/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">AI Plan Generator</h3>
                        </div>
                        <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                            Klik tombol di bawah untuk meminta AI meracik program latihan 7 hari yang baru.
                        </p>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Durasi Program</label>
                                <select id="plan-duration" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg p-2.5 focus:ring-orange-500 focus:border-orange-500">
                                    <option value="1">1 Minggu</option>
                                    <option value="2">2 Minggu</option>
                                    <option value="3">3 Minggu</option>
                                    <option value="4">4 Minggu</option>
                                    <option value="5">5 Minggu</option>
                                    <option value="6">6 Minggu</option>
                                    <option value="7">7 Minggu</option>
                                    <option value="8">8 Minggu</option>
                                    <option value="9">9 Minggu</option>
                                    <option value="10">10 Minggu</option>
                                    <option value="11">11 Minggu</option>
                                    <option value="12">12 Minggu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Mulai Tanggal</label>
                                <input type="date" id="plan-start-date" class="w-full bg-gray-800 border border-gray-700 text-white text-sm rounded-lg p-2.5 focus:ring-orange-500 focus:border-orange-500" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <button id="btn-generate-ai" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition-all flex items-center justify-center gap-2">
                            <svg id="loading-spinner" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="btn-generate-text">Buat Jadwal Baru</span>
                        </button>
                        <div id="loading-text" class="hidden mt-3 text-center">
                            <p class="text-xs text-orange-400 animate-pulse font-medium">AI sedang berpikir...</p>
                            <p class="text-[10px] text-gray-500 mt-1">Bisa memakan waktu 5-10 detik</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div id="result-container" class="bg-gray-900/50 rounded-xl border-2 border-dashed border-gray-800 min-h-[500px] flex flex-col p-6 transition-all relative">

                    <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                        <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <span class="text-5xl">🤖</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-300 mb-2">Belum Ada Data</h3>
                        <p class="text-gray-500 max-w-sm mx-auto">Hasil racikan AI akan muncul di sini setelah Kamu menekan tombol generate.</p>
                    </div>

                    <div id="ai-response-content" class="hidden w-full animate-fade-in">
                    </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        // Data dari PHP
        const userProfile = <?php echo $user_profile_json; ?>;
        // URL API Python FASTAPI
        const API_URL = "<?php echo API_URL; ?>";

        // Variabel Hari 
        const days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];

        // DOM Elements
        const btnGenerate = document.getElementById('btn-generate-ai');
        const btnGenerateText = document.getElementById('btn-generate-text');
        const loadingSpinner = document.getElementById('loading-spinner');
        const loadingText = document.getElementById('loading-text');
        const resultContainer = document.getElementById('result-container');
        const emptyState = document.getElementById('empty-state');
        const aiResponseContent = document.getElementById('ai-response-content');

        // Event Listener
        btnGenerate.addEventListener('click', generatePlan);

        async function generatePlan() {
            const duration = document.getElementById('plan-duration').value;
            const startDate = document.getElementById('plan-start-date').value;

            // Update user profile with new duration
            userProfile.duration_weeks = parseInt(duration);

            showLoading(true);
            try {
                console.log("Sending data to Python:", userProfile);

                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userProfile)
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({
                        detail: 'Unknown Error'
                    }));
                    throw new Error(errorData.detail || `HTTP error! Status: ${response.status}`);
                }

                const planData = await response.json();
                console.log("Data received from Python:", planData);
                // ambil data start date & durasi buat render 
                renderPlan(planData, startDate, duration);

            } catch (error) {
                console.error(error);
                renderError(error.message);
            } finally {
                showLoading(false);
            }
        }

        function showLoading(isLoading) {
            if (isLoading) {
                btnGenerate.disabled = true;
                btnGenerate.classList.add('opacity-50', 'cursor-not-allowed');
                loadingSpinner.classList.remove('hidden');
                loadingText.classList.remove('hidden');
                btnGenerateText.innerText = "Sedang Memproses...";

                // Reset UI
                emptyState.classList.add('hidden');
                aiResponseContent.classList.add('hidden');
                aiResponseContent.innerHTML = '';
                resultContainer.classList.remove('border-red-500/50', 'bg-red-900/10');
                resultContainer.classList.add('border-gray-800', 'bg-gray-900/50');
            } else {
                btnGenerate.disabled = false;
                btnGenerate.classList.remove('opacity-50', 'cursor-not-allowed');
                loadingSpinner.classList.add('hidden');
                loadingText.classList.add('hidden');
                btnGenerateText.innerText = "Buat Jadwal Baru";
            }
        }

        function renderPlan(planData, startDateInput, durationWeeks) {
            // 1. Setup Container
            resultContainer.classList.remove('border-dashed', 'flex', 'flex-col', 'items-center', 'justify-center');
            resultContainer.classList.add('block', 'border-solid');
            aiResponseContent.classList.remove('hidden');

            const planDataString = JSON.stringify(planData).replace(/'/g, "&apos;");

            let html = `
                <div class="mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-3xl font-bold text-white">${planData.plan_name}</h2>
                            <p class="text-gray-400 text-sm mt-1">Mulai: ${startDateInput} (${durationWeeks} Minggu)</p>
                        </div>
                    </div>
                    <div class="plan-coach-note">
                        <p class="font-semibold text-orange-500 mb-1">💬 Coach Note:</p>
                        "${planData.coach_note}"
                    </div>
                </div>
            `;

            // 2. Grid Latihan (Scrollable jika banyak)
            html += `<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">`;

            if (planData.weekly_schedule && Array.isArray(planData.weekly_schedule)) {
                planData.weekly_schedule.forEach((day, index) => {
                    const isOff = day.is_off_day;
                    const weekNum = day.week_number || Math.ceil((index + 1) / 7);

                    html += `<div class="day-plan-card ${isOff ? 'off-day' : ''}">`;
                    html += `<div class="day-plan-header flex justify-between items-center">`;
                    html += `<div>
                                <span class="text-xs text-gray-500 block">HARI KE-${day.day_number}</span>
                                <span class="font-bold text-lg ${isOff ? 'text-gray-500' : 'text-white'}">${day.day_name}</span>
                             </div>`;
                    html += `<span class="text-xs font-bold px-2 py-1 rounded ${isOff ? 'bg-gray-800 text-gray-500' : 'bg-orange-900/30 text-orange-400'}">${isOff ? 'REST' : 'WORKOUT'}</span>`;
                    html += `</div>`;

                    html += `<div class="day-plan-list">`;
                    html += `<div class="text-sm font-medium text-gray-300 mb-3 min-h-[20px]">${day.session_title}</div>`;

                    if (!isOff && day.exercises && day.exercises.length > 0) {
                        html += `<ul>`;
                        day.exercises.forEach(ex => {
                            html += `<li class="text-gray-400">
                                <div class="text-white font-medium">${ex.name}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    ${ex.sets} Sets x ${ex.reps} Reps <span class="mx-1">•</span> Rest: ${ex.rest || '-'}
                                </div>
                            </li>`;
                        });
                        html += `</ul>`;
                    } else {
                        html += `<div class="h-full flex items-center justify-center text-gray-600 text-sm italic py-4">Pemulihan Otot</div>`;
                    }
                    html += `</div></div>`;
                });
            }
            html += `</div>`;

            // 3. Form Save Plan
            html += `
                <form id="savePlanForm" action="<?php echo url('/controllers/save_plan.php'); ?>" method="POST" class="mt-8 p-6 bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Simpan Program Ini?</h3>
                            <p class="text-sm text-gray-400">Program akan disimpan ke kalender mulai tanggal <strong class="text-white">${startDateInput}</strong> selama <strong class="text-white">${durationWeeks} Minggu</strong>.</p>
                        </div>
                    </div>
                    
                    <input type="hidden" name="plan_name" value="${planData.plan_name}">
                    <input type="hidden" name="plan_json" value='${planDataString}'>
                    <input type="hidden" name="start_date" value="${startDateInput}">
                    <input type="hidden" name="duration_weeks" value="${durationWeeks}">
                    
                    <div class="space-y-4">
                        <div class="flex justify-end pt-4 border-t border-gray-700">
                            <button type="submit" id="save-plan-btn" class="w-full md:w-auto bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-8 rounded-lg transition-all shadow-lg">
                                Terapkan Jadwal Ini 📅
                            </button>
                        </div>
                        <p id="save-error" class="text-red-400 text-sm mt-2 hidden text-center"></p>
                    </div>
                </form>
            `;

            aiResponseContent.innerHTML = html;
            // generateWeekSelector(); // HAPUS: Gak perlu selector lagi karena udah input date di awal

            // Handle Form Submit
            document.getElementById('savePlanForm').addEventListener('submit', handleSavePlan);
        }

        function generateWeekSelector() {
            const container = document.getElementById('week-selector-container');
            const hiddenDateInput = document.getElementById('start_date_hidden');
            const saveBtn = document.getElementById('save-plan-btn');

            const today = new Date();
            const currentDay = today.getDay(); // 0=Min, 1=Sen

            // Cari hari SENIN minggu ini (Mundur ke belakang)
            // Jika hari ini Minggu (0), mundur 6 hari. Jika Senin (1), mundur 0 hari.
            const diffToMonday = currentDay === 0 ? 6 : currentDay - 1;
            const mondayStart = new Date(today);
            mondayStart.setDate(today.getDate() - diffToMonday);

            let html = '';

            // Generate 4 Minggu ke depan
            for (let w = 0; w < 4; w++) {
                // Awal minggu (Senin) untuk blok ini
                const weekStart = new Date(mondayStart);
                weekStart.setDate(mondayStart.getDate() + (w * 7));

                // Label Minggu
                const weekLabel = w === 0 ? "Minggu Ini" : (w === 1 ? "Minggu Depan" : `${w} Minggu Lagi`);
                html += `<div class="week-label"><span>${weekLabel}</span> <span class="text-[10px] font-normal text-gray-500">Mulai ${weekStart.getDate()} ${weekStart.toLocaleString('id-ID', { month: 'short' })}</span></div>`;

                html += `<div class="week-selector">`;

                // Loop 7 hari dalam minggu tersebut
                for (let d = 0; d < 7; d++) {
                    const dayDate = new Date(weekStart);
                    dayDate.setDate(weekStart.getDate() + d);

                    const dateString = dayDate.toISOString().split('T')[0]; // YYYY-MM-DD
                    const dayNum = dayDate.getDay(); // 0-6

                    // Logika Tombol: Cuma hari SENIN yang bisa diklik buat 'Start Plan'
                    const isMonday = dayNum === 1;
                    const isPast = dayDate < new Date(today.setHours(0, 0, 0, 0)); // Cek kalau tanggal udah lewat

                    let btnClass = "week-selector-btn";
                    let disabledAttr = "disabled";
                    let titleAttr = "Pilih hari Senin untuk mulai";

                    if (isMonday && !isPast) {
                        // Hari Senin masa depan/hari ini -> BISA DIKLIK
                        disabledAttr = "";
                        titleAttr = "Mulai program dari tanggal ini";
                    } else if (isMonday && isPast) {
                        // Hari Senin tapi udah lewat
                        btnClass += " opacity-50 cursor-not-allowed";
                        titleAttr = "Tanggal sudah lewat";
                    } else {
                        // Bukan hari Senin
                        btnClass += " opacity-30 cursor-not-allowed";
                    }

                    html += `
                        <button type="button" 
                            class="${btnClass}" 
                            data-date="${dateString}" 
                            title="${titleAttr}"
                            ${disabledAttr}>
                            <span class="day-name">${days[dayNum]}</span>
                            <span class="date-num">${dayDate.getDate()}</span>
                        </button>
                    `;
                }
                html += `</div>`;
            }

            container.innerHTML = html;

            // Add Click Listeners
            container.querySelectorAll('.week-selector-btn:not([disabled])').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Hapus seleksi lama
                    container.querySelectorAll('.selected').forEach(el => el.classList.remove('selected'));

                    // Tambah seleksi baru
                    btn.classList.add('selected');

                    // Update hidden input
                    hiddenDateInput.value = btn.dataset.date;
                    saveBtn.disabled = false;
                    saveBtn.innerText = `Mulai Latihan Tgl ${btn.querySelector('.date-num').innerText}`;
                });
            });
        }

        async function handleSavePlan(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('save-plan-btn');
            const errorMsg = document.getElementById('save-error');

            // UI Loading state
            btn.disabled = true;
            btn.innerText = "Menyimpan ke Database...";
            errorMsg.classList.add('hidden');

            try {
                const formData = new FormData(form);
                const response = await fetch('<?php echo url("/controllers/save_plan.php"); ?>', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = '<?php echo url("/calendar"); ?>?highlight=' + formData.get('start_date');
                } else {
                    throw new Error(result.message || "Gagal menyimpan.");
                }
            } catch (err) {
                errorMsg.innerText = `Error: ${err.message}`;
                errorMsg.classList.remove('hidden');
                btn.disabled = false;
                btn.innerText = "Coba Simpan Lagi";
            }
        }

        function renderError(msg) {
            emptyState.classList.add('hidden');
            resultContainer.classList.remove('border-gray-800', 'bg-gray-900/50');
            resultContainer.classList.add('border-red-500/50', 'bg-red-900/10');
            aiResponseContent.classList.remove('hidden');

            aiResponseContent.innerHTML = `
                <div class="text-center p-8">
                    <div class="text-4xl mb-4">⚠️</div>
                    <h3 class="text-xl font-bold text-red-400 mb-2">Terjadi Kesalahan</h3>
                    <p class="text-gray-300 mb-4">${msg}</p>
                    <div class="text-sm text-gray-500 bg-black/30 p-4 rounded text-left overflow-auto max-h-32 font-mono">
                        Cek terminal Python untuk log error yang lebih detail.
                    </div>
                </div>
            `;
        }
    </script>
</body>

</html>