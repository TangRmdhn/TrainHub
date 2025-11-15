<?php
session_start();
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Mingguan - TrainHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .week-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr); /* Default 1 kolom di HP */
            min-height: 400px;
        }
        @media (min-width: 1024px) { /* 7 kolom di desktop */
            .week-grid {
                grid-template-columns: repeat(7, 1fr);
            }
        }
        .day-card {
            border-right: 1px solid #374151; /* gray-700 */
            border-bottom: 1px solid #374151; /* gray-700 */
            background: #111827; /* gray-900 */
            min-height: 150px;
        }
        .day-card:first-child { border-left: 1px solid #374151; }
        
        .day-card.today {
            /* border: 2px solid #f97316; */
            background: #1f2937; /* gray-800 */
        }
        .day-card-header {
            border-bottom: 1px solid #374151;
        }
        .plan-badge {
            display: block;
            margin-top: 8px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            background-color: #f97316;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .plan-badge:hover { background-color: #ea580c; }
        /* Style untuk modal */
        #planModal table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        #planModal th, #planModal td { padding: 8px 10px; border: 1px solid #4b5563; }
        #planModal th { background-color: #374151; }
        input[type="date"] { color-scheme: dark; }
    </style>
</head>
<body class="bg-black text-gray-100 min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="app.php" class="text-2xl font-bold text-white">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="app.php" class="text-gray-300 hover:text-white transition">Dashboard</a>
                        <a href="plans.php" class="text-gray-300 hover:text-white transition">My Plans</a>
                        <a href="calendar.php" class="text-orange-500 font-semibold">Calendar</a>
                    </div>
                </div>
                <a href="logout.php" class="text-sm text-red-500 hover:text-red-400 font-medium">Logout</a>
            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA: WEEKLY PLANNER -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header & Week Selector -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold text-white">Kalender Mingguan</h1>
            
            <div class="flex-shrink-0">
                <label for="week-selector" class="text-sm text-gray-400 mr-2">Tampilkan Minggu:</label>
                <select id="week-selector" class="bg-gray-800 border border-gray-700 text-white rounded-lg p-2 focus:border-orange-500 focus:ring-orange-500">
                    <!-- Opsi minggu di-generate JS -->
                </select>
            </div>
        </div>

        <!-- Grid 7 Hari (Weekly View) -->
        <div id="week-grid-container" class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden shadow-xl">
            <div class="week-grid" id="week-grid">
                <!-- Card hari di-generate JS -->
                <div class="flex items-center justify-center p-10 col-span-1 md:col-span-7 text-gray-500">
                    Memuat jadwal...
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detail Plan (Sama kayak kemarin, gak berubah) -->
    <div id="planModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-[100] p-4">
        <div class="bg-gray-900 p-6 rounded-lg border border-gray-700 max-w-2xl w-full max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-2xl font-bold text-white">Detail Rencana</h3>
                <button id="closeModal" class="text-gray-400 hover:text-white text-3xl">&times;</button>
            </div>
            
            <div id="modalBody" class="text-gray-300 overflow-y-auto mb-6">
                <!-- Detail plan akan diisi JS -->
            </div>
            
            <div class="mt-auto pt-4 border-t border-gray-700">
                <div id="modal-error" class="text-red-400 text-sm mb-4 hidden"></div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 flex items-center gap-2">
                        <input type="date" id="new-date-input" class="w-full sm:w-auto bg-gray-700 border-gray-600 rounded p-2">
                        <button id="move-plan-btn" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                            Pindah
                        </button>
                    </div>
                    <button id="delete-plan-btn" class="bg-red-600/20 hover:bg-red-600 text-red-500 hover:text-white font-medium py-2 px-4 rounded-lg transition">
                        Hapus Sesi Ini
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weekSelector = document.getElementById('week-selector');
            const weekGrid = document.getElementById('week-grid');
            const modal = document.getElementById('planModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            const closeModal = document.getElementById('closeModal');
            const modalError = document.getElementById('modal-error');
            const newDateInput = document.getElementById('new-date-input');
            const movePlanBtn = document.getElementById('move-plan-btn');
            const deletePlanBtn = document.getElementById('delete-plan-btn');

            let currentPlanId = null; // Simpan ID plan yg lagi dibuka
            const today = new Date();
            const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const dayNamesFull = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

            // --- FUNGSI BARU: Generate Pilihan Minggu ---
            function populateWeekSelector() {
                const todayDay = today.getDay(); // 0 = Min, 1 = Sen
                // Kita set hari pertama (Senin) di minggu ini
                const firstDayOfWeek = new Date(today.getTime());
                // Mundur ke hari Senin (jika hari Minggu, mundur 6 hari, jika Senin 0 hari, jika Selasa 1 hari)
                const diff = todayDay === 0 ? 6 : todayDay - 1;
                firstDayOfWeek.setDate(today.getDate() - diff);
                
                // Cek highlight dari URL
                const urlParams = new URLSearchParams(window.location.search);
                const highlightDate = urlParams.get('highlight'); // Ini tanggal Senin

                for (let i = 0; i < 8; i++) { // Tampilkan 8 minggu (Minggu Ini + 7 Minggu Ke Depan)
                    const weekStartDate = new Date(firstDayOfWeek.getTime());
                    weekStartDate.setDate(firstDayOfWeek.getDate() + (i * 7));
                    
                    const weekEndDate = new Date(weekStartDate.getTime());
                    weekEndDate.setDate(weekStartDate.getDate() + 6); // Hari Minggu

                    const startStr = weekStartDate.toISOString().split('T')[0];
                    const endStr = weekEndDate.toISOString().split('T')[0];
                    
                    let label = '';
                    if (i === 0) label = "Minggu Ini";
                    else if (i === 1) label = "Minggu Depan";
                    else label = `${i} Minggu Lagi`;
                    
                    label += ` (${weekStartDate.getDate()} ${weekStartDate.toLocaleString('id-ID', { month: 'short' })} - ${weekEndDate.getDate()} ${weekEndDate.toLocaleString('id-ID', { month: 'short' })})`;

                    const option = document.createElement('option');
                    option.value = `${startStr}_${endStr}`; // value-nya rentang tanggal
                    option.innerText = label;
                    
                    // Set selected berdasarkan highlight URL
                    if (highlightDate && highlightDate === startStr) {
                        option.selected = true;
                    } else if (!highlightDate && i === 0) {
                        option.selected = true; // Default "Minggu Ini"
                    }
                    
                    weekSelector.appendChild(option);
                }
            }
            
            // --- FUNGSI BARU: Load Jadwal per Minggu ---
            async function loadWeekSchedule(startDate, endDate) {
                weekGrid.innerHTML = `<div class="flex items-center justify-center p-10 col-span-1 lg:col-span-7 text-gray-500">Memuat jadwal...</div>`;
                
                // 1. Fetch data dari API baru
                const response = await fetch(`api_calendar.php?start=${startDate}&end=${endDate}`);
                const plans = await response.json();
                
                weekGrid.innerHTML = ''; // Kosongkan grid
                const todayString = today.toISOString().split('T')[0];

                // 2. Loop 7 hari (Senin - Minggu)
                for (let i = 0; i < 7; i++) {
                    const date = new Date(startDate);
                    date.setDate(date.getDate() + i);
                    
                    const dateString = date.toISOString().split('T')[0];
                    const dayName = dayNamesFull[date.getDay()];
                    const dateNum = date.getDate();
                    
                    let cardClasses = "day-card flex flex-col p-3";
                    if (dateString === todayString) {
                        cardClasses += " today";
                    }

                    // 3. Cari plan untuk hari ini
                    const plan = plans.find(p => p.plan_date === dateString);
                    let planHTML = '';
                    if (plan) {
                        // Di database kita simpen JSON per hari
                        const planJson = plan.plan_json; // Ini udah di-decode sama PHP
                        planHTML = `<span class="plan-badge" 
                                        data-plan-id="${plan.id}" 
                                        data-plan-date="${plan.plan_date}"
                                        data-plan-name="${plan.plan_name}"
                                        data-plan-json='${JSON.stringify(planJson)}'>
                                        ${plan.plan_name}
                                    </span>`;
                    }
                    
                    // 4. Render Card Hari
                    weekGrid.innerHTML += `
                        <div class="${cardClasses}">
                            <div class="day-card-header pb-2">
                                <div class="font-bold text-lg ${dateString === todayString ? 'text-orange-500' : ''}">${dayName}</div>
                                <div class="text-sm text-gray-400">${dateNum} ${date.toLocaleString('id-ID', { month: 'short' })}</div>
                            </div>
                            <div class="pt-2">
                                ${planHTML || '<span class="text-xs text-gray-600">Rest Day</span>'}
                            </div>
                        </div>
                    `;
                }
                
                // 5. Pasang listener ke badge baru
                attachBadgeListeners();
            }

            // --- INI FUNGSI-FUNGSI LAMA (Modal, Hapus, Pindah) ---
            function attachBadgeListeners() {
                document.querySelectorAll('.plan-badge').forEach(badge => {
                    badge.addEventListener('click', function() {
                        const planId = this.dataset.planId;
                        const planName = this.dataset.planName;
                        const planDate = this.dataset.planDate;
                        const planJson = JSON.parse(this.dataset.planJson); // Ini JSON per hari
                        
                        currentPlanId = planId; // Simpan ID plan
                        modalTitle.innerText = planName;
                        modalError.classList.add('hidden');
                        newDateInput.value = planDate;
                        
                        let bodyHtml = '';
                        if (planJson.exercises && planJson.exercises.length > 0) {
                            bodyHtml += '<div class="overflow-x-auto"><table class="text-sm">';
                            bodyHtml += '<thead><tr><th>Latihan</th><th>Set</th><th>Rep</th><th>Rest</th></tr></thead>';
                            bodyHtml += '<tbody>';
                            planJson.exercises.forEach(ex => {
                                bodyHtml += `<tr><td>${ex.name}</td><td>${ex.sets}</td><td>${ex.reps}</td><td>${ex.rest}</td></tr>`;
                            });
                            bodyHtml += '</tbody></table></div>';
                        } else {
                            bodyHtml = `<p>Detail latihan tidak ditemukan.</p>`;
                        }
                        
                        modalBody.innerHTML = bodyHtml;
                        modal.classList.remove('hidden');
                    });
                });
            }

            async function handleMovePlan() {
                const newDate = newDateInput.value;
                if (!currentPlanId || !newDate) {
                    modalError.innerText = 'Tanggal baru tidak valid.';
                    modalError.classList.remove('hidden');
                    return;
                }
                
                try {
                    const response = await fetch('update_plan_date.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ plan_id: currentPlanId, new_date: newDate })
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        modal.classList.add('hidden');
                        const [start, end] = weekSelector.value.split('_');
                        loadWeekSchedule(start, end); // Refresh minggu ini
                    } else {
                        modalError.innerText = result.message;
                        modalError.classList.remove('hidden');
                    }
                } catch (err) {
                    modalError.innerText = 'Gagal terhubung ke server.';
                    modalError.classList.remove('hidden');
                }
            }

            async function handleDeletePlan() {
                if (!currentPlanId) return;
                
                if (!confirm(`Yakin mau hapus sesi latihan ini, Tang?`)) {
                    return;
                }
                
                try {
                    const response = await fetch('delete_plan.php', {
                        method: 'POST', 
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ plan_id: currentPlanId })
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        modal.classList.add('hidden');
                        const [start, end] = weekSelector.value.split('_');
                        loadWeekSchedule(start, end); // Refresh minggu ini
                    } else {
                        modalError.innerText = result.message;
                        modalError.classList.remove('hidden');
                    }
                } catch (err) {
                    modalError.innerText = 'Gagal terhubung ke server.';
                    modalError.classList.remove('hidden');
                }
            }

            // --- Attach listener baru ---
            movePlanBtn.addEventListener('click', handleMovePlan);
            deletePlanBtn.addEventListener('click', handleDeletePlan);
            
            // --- Listener navigasi & modal ---
            weekSelector.addEventListener('change', (e) => {
                const [start, end] = e.target.value.split('_');
                loadWeekSchedule(start, end);
            });
            closeModal.addEventListener('click', () => modal.classList.add('hidden'));
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.classList.add('hidden');
            });

            // --- Inisialisasi ---
            populateWeekSelector();
            const [initStart, initEnd] = weekSelector.value.split('_');
            loadWeekSchedule(initStart, initEnd);
        });
    </script>
</body>
</html>