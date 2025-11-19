<?php
session_start();
include '../koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// 2. Ambil data profil user untuk Header
$sql = "SELECT username, fitness_goal FROM users WHERE id = '$user_id'";
$result = $koneksi->query($sql);
if ($result->num_rows == 0) {
    header("Location: ../controllers/logout.php");
    exit;
}
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender Latihan - TrainHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Calendar Grid Styles */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #374151;
            /* Border color */
            border: 1px solid #374151;
        }

        .calendar-cell {
            background-color: #111827;
            /* Cell bg */
            min-height: 120px;
            padding: 8px;
            position: relative;
            transition: background-color 0.2s;
        }

        .calendar-cell:hover {
            background-color: #1f2937;
        }

        .calendar-cell.today {
            background-color: #1f2937;
        }

        .calendar-cell.other-month {
            background-color: #0f131a;
            opacity: 0.5;
        }

        .calendar-cell .date-num {
            font-weight: 600;
            margin-bottom: 4px;
            display: inline-block;
            width: 24px;
            height: 24px;
            text-align: center;
            line-height: 24px;
            border-radius: 50%;
        }

        .calendar-cell.today .date-num {
            background-color: #ea580c;
            color: white;
        }

        /* Event Pill */
        .event-pill {
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-bottom: 2px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            border-left: 3px solid transparent;
        }

        .event-pill.workout {
            background-color: rgba(234, 88, 12, 0.2);
            color: #fdba74;
            border-left-color: #ea580c;
        }

        .event-pill.completed {
            background-color: #22c55e;
            /* Green-500 */
            color: white;
            border-left-color: #15803d;
            /* Green-700 */
        }

        /* Ensure calendar has minimum width on mobile */
        @media (max-width: 640px) {

            .calendar-grid,
            .grid.grid-cols-7.bg-gray-800 {
                min-width: 480px;
                /* Force minimum width to prevent crushing */
            }
        }

        @media (max-width: 480px) {

            .calendar-grid,
            .grid.grid-cols-7.bg-gray-800 {
                min-width: 420px;
                /* Slightly smaller but still readable */
            }
        }

        /* Modal */
        .modal {
            transition: opacity 0.25s ease;
        }

        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
    </style>
</head>

<body class="bg-black text-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50 backdrop-blur-md bg-opacity-80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left: Logo -->
                <div class="flex items-center">
                    <a href="app.php" class="text-2xl font-bold text-white tracking-tight">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                </div>

                <!-- Center: Desktop Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="app.php" class="text-gray-300 hover:text-white transition">Dashboard</a>
                    <a href="plans.php" class="text-gray-300 hover:text-white transition">My Plans</a>
                    <a href="calendar.php" class="text-orange-500 font-semibold">Calendar</a>
                    <a href="stats.php" class="text-gray-300 hover:text-white transition">Statistics</a>
                </div>

                <!-- Right: Logout (Desktop) -->
                <div class="hidden md:flex">
                    <a href="../controllers/logout.php" class="bg-gray-800 hover:bg-red-900/30 text-gray-300 hover:text-red-400 px-4 py-2 rounded-lg text-sm font-medium transition-all border border-gray-700 hover:border-red-800">
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
                <a href="app.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Dashboard</a>
                <a href="plans.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">My Plans</a>
                <a href="calendar.php" class="block px-3 py-2 rounded-lg text-orange-500 font-semibold bg-gray-900">Calendar</a>
                <a href="stats.php" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Statistics</a>
                <div class="pt-3 border-t border-gray-700">
                    <a href="../controllers/logout.php" class="block px-3 py-2 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 transition text-center font-medium">
                        Logout
                    </a>
                </div>
            </div>
        </div>
        </div>
    </nav>

    <script>
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuBtn?.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    </script>


    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">

        <div class="flex flex-col items-center mb-6 gap-4 w-full">
            <h1 class="text-2xl md:text-3xl font-bold text-white text-center">Kalender Latihan</h1>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto justify-center">
                <button id="todayBtn" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition border border-gray-700 w-full sm:w-auto">
                    Hari ini
                </button>
                <div class="flex items-center bg-gray-800 rounded-lg p-1 w-full sm:w-auto justify-center">
                    <button id="prevMonth" class="p-2 hover:bg-gray-700 rounded-md text-gray-400 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <span id="currentMonthLabel" class="px-3 sm:px-4 font-semibold text-white min-w-[120px] sm:min-w-[140px] text-center text-sm sm:text-base">
                        <!-- Month Year -->
                    </span>
                    <button id="nextMonth" class="p-2 hover:bg-gray-700 rounded-md text-gray-400 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-gray-900 rounded-xl border border-gray-800 shadow-xl overflow-x-auto">
            <table class="w-full border-collapse" style="min-width: 600px;">
                <!-- Days Header -->
                <thead>
                    <tr class="bg-gray-800 border-b border-gray-700">
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Minggu</th>
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Senin</th>
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Selasa</th>
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Rabu</th>
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Kamis</th>
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Jumat</th>
                        <th class="py-3 text-center text-xs font-semibold text-gray-400 uppercase" style="width: 14.28%;">Sabtu</th>
                    </tr>
                </thead>
                <!-- Calendar Grid -->
                <tbody id="calendarGrid" class="calendar-grid">
                    <!-- Cells will be generated by JS -->
                </tbody>
            </table>
        </div>

    </main>

    <!-- Workout Detail Modal -->
    <div id="workoutModal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-75"></div>

        <div class="modal-container bg-gray-800 w-11/12 md:max-w-md mx-auto rounded-xl shadow-2xl z-50 overflow-y-auto transform scale-95 transition-transform duration-300 border border-gray-700">

            <div class="modal-content py-4 text-left px-6">
                <!-- Title -->
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <div>
                        <p class="text-2xl font-bold text-white" id="modal-plan-name">Plan Name</p>
                        <p class="text-sm text-gray-400" id="modal-date">Date</p>
                    </div>
                    <div class="modal-close cursor-pointer z-50 p-2 hover:bg-gray-700 rounded-full transition">
                        <svg class="fill-current text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                            <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Body -->
                <div class="my-5">
                    <div class="mb-4">
                        <p class="text-sm text-gray-400 uppercase tracking-wider font-bold mb-2">Status</p>
                        <div id="modal-status">
                            <!-- Status Badge -->
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-400 uppercase tracking-wider font-bold mb-2">Latihan</p>
                        <div id="modal-exercises" class="text-gray-300">
                            <!-- Exercises List -->
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-2 border-t border-gray-700">
                    <!-- Buttons injected by JS -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const calendarGrid = document.getElementById('calendarGrid');
        const currentMonthLabel = document.getElementById('currentMonthLabel');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        const todayBtn = document.getElementById('todayBtn');

        // Modal Elements
        const modal = document.getElementById('workoutModal');
        const modalOverlay = document.querySelector('.modal-overlay');
        const modalCloseBtns = document.querySelectorAll('.modal-close');
        const modalPlanName = document.getElementById('modal-plan-name');
        const modalDate = document.getElementById('modal-date');
        const modalStatus = document.getElementById('modal-status');
        const modalExercises = document.getElementById('modal-exercises');

        let currentDate = new Date();
        let events = {}; // Store fetched events here: { 'YYYY-MM-DD': [eventData] }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            renderCalendar();
            fetchEvents();
        });

        prevMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
            fetchEvents();
        });

        nextMonthBtn.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
            fetchEvents();
        });

        todayBtn.addEventListener('click', () => {
            currentDate = new Date();
            renderCalendar();
            fetchEvents();
        });

        // Modal Logic
        function toggleModal() {
            const body = document.querySelector('body');
            const container = modal.querySelector('.modal-container');

            modal.classList.toggle('opacity-0');
            modal.classList.toggle('pointer-events-none');
            body.classList.toggle('modal-active');

            if (!modal.classList.contains('opacity-0')) {
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            } else {
                container.classList.remove('scale-100');
                container.classList.add('scale-95');
            }
        }

        modalOverlay.addEventListener('click', toggleModal);
        modalCloseBtns.forEach(btn => btn.addEventListener('click', toggleModal));

        async function fetchEvents() {
            // Calculate start and end date of current view (including padding days)
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);

            // Adjust to get full grid range
            const startPadding = firstDay.getDay();
            const startDate = new Date(year, month, 1 - startPadding);

            const endPadding = 6 - lastDay.getDay();
            const endDate = new Date(year, month + 1, 0 + endPadding);

            const startStr = startDate.toISOString().split('T')[0];
            const endStr = endDate.toISOString().split('T')[0];

            try {
                const response = await fetch(`../controllers/api_calendar.php?start=${startStr}&end=${endStr}`);
                const data = await response.json();

                // Reset events
                events = {};

                if (Array.isArray(data)) {
                    data.forEach(event => {
                        // New API returns events with 'start' property (YYYY-MM-DD)
                        if (!events[event.start]) {
                            events[event.start] = [];
                        }
                        events[event.start].push(event);
                    });
                }

                // Re-render to show events
                renderCalendar();

            } catch (error) {
                console.error('Error fetching events:', error);
            }
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            // Update Label
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            currentMonthLabel.innerText = `${monthNames[month]} ${year}`;

            calendarGrid.innerHTML = '';

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startDay = firstDay.getDay(); // 0 = Sunday

            // Previous Month Padding
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            for (let i = startDay; i > 0; i--) {
                const day = prevMonthLastDay - i + 1;
                // Better date string construction
                const d = new Date(year, month - 1, day);
                const dStr = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;

                createCell(day, true, dStr);
            }

            // Current Month
            const today = new Date();
            for (let i = 1; i <= daysInMonth; i++) {
                const isToday = i === today.getDate() && month === today.getMonth() && year === today.getFullYear();
                const dStr = `${year}-${String(month+1).padStart(2,'0')}-${String(i).padStart(2,'0')}`;
                createCell(i, false, dStr, isToday);
            }

            // Next Month Padding
            const totalCells = startDay + daysInMonth;
            const nextMonthPadding = 42 - totalCells; // 6 rows * 7 cols = 42
            for (let i = 1; i <= nextMonthPadding; i++) {
                const d = new Date(year, month + 1, i);
                const dStr = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
                createCell(i, true, dStr);
            }
        }

        function createCell(dayNum, isOtherMonth, dateStr, isToday = false) {
            const cell = document.createElement('div');
            cell.className = `calendar-cell ${isOtherMonth ? 'other-month' : ''} ${isToday ? 'today' : ''}`;

            const dateEl = document.createElement('div');
            dateEl.className = 'date-num';
            dateEl.innerText = dayNum;
            cell.appendChild(dateEl);

            // Render Events
            if (events[dateStr]) {
                events[dateStr].forEach(event => {
                    const pill = document.createElement('div');
                    const isCompleted = event.extendedProps && event.extendedProps.is_completed;
                    pill.className = `event-pill ${isCompleted ? 'completed' : 'workout'}`;
                    pill.innerText = event.title; // Use session title

                    pill.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openModal(event);
                    });

                    cell.appendChild(pill);
                });
            }

            calendarGrid.appendChild(cell);
        }

        function openModal(eventData) {
            // Parse Date
            const dateObj = new Date(eventData.start);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            modalDate.innerText = dateObj.toLocaleDateString('id-ID', options);

            modalPlanName.innerText = eventData.title; // Use session title as header

            // Status
            const isCompleted = eventData.extendedProps.is_completed;
            if (isCompleted) {
                modalStatus.innerHTML = '<span class="text-green-400 font-bold flex items-center gap-1">✅ Completed</span>';
            } else {
                modalStatus.innerHTML = '<span class="text-orange-400 font-medium">⏳ Scheduled</span>';
            }

            // Parse Exercises from extendedProps
            let exercisesHtml = '';
            let exercises = [];

            if (eventData.extendedProps && eventData.extendedProps.details && eventData.extendedProps.details.exercises) {
                exercises = eventData.extendedProps.details.exercises;
            }

            if (exercises.length > 0) {
                exercisesHtml = '<ul class="space-y-3">';
                exercises.forEach(ex => {
                    exercisesHtml += `
                        <li class="bg-gray-800 p-3 rounded-lg border border-gray-700">
                            <div class="flex justify-between">
                                <span class="font-semibold text-white">${ex.name}</span>
                                <span class="text-xs text-gray-400 bg-gray-900 px-2 py-1 rounded">${ex.rest || 'Rest -'}</span>
                            </div>
                            <div class="text-sm text-gray-400 mt-1">
                                ${ex.sets} Sets x ${ex.reps} Reps
                            </div>
                        </li>
                    `;
                });
                exercisesHtml += '</ul>';
            } else {
                exercisesHtml = '<p class="text-gray-500 italic">Tidak ada detail latihan.</p>';
            }

            modalExercises.innerHTML = exercisesHtml;

            // Footer Buttons
            const footer = document.querySelector('.modal-content .flex.justify-end');
            // Remove old buttons except Close
            footer.innerHTML = '<button class="modal-close px-4 py-2 bg-gray-800 p-3 rounded-lg text-white hover:bg-gray-700 transition mr-2">Tutup</button>';

            // Re-attach close listener
            footer.querySelector('.modal-close').addEventListener('click', toggleModal);

            if (!isCompleted) {
                const completeBtn = document.createElement('button');
                completeBtn.className = 'bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg flex items-center gap-2';
                completeBtn.innerHTML = '<span>Selesai Latihan</span>';
                completeBtn.onclick = () => markAsCompleted(eventData);
                footer.appendChild(completeBtn);
            }

            toggleModal();
        }

        async function markAsCompleted(eventData) {
            const planId = eventData.extendedProps.plan_id;
            const dateStr = eventData.start;

            if (!confirm('Tandai latihan ini sebagai selesai?')) return;

            try {
                const response = await fetch('../controllers/mark_complete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_id: planId,
                        date: dateStr
                    })
                });

                const result = await response.json();

                if (result.success) {
                    toggleModal();
                    // Refresh events
                    fetchEvents();
                } else {
                    alert('Gagal: ' + result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan koneksi.');
            }
        }
    </script>
</body>

</html>