<?php
session_start();
include '../koneksi.php';
include '../config.php';
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: " . url("/login"));
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil semua rencana user, diurut dari yang terbaru
$sql = "SELECT id, plan_name, start_date, finish_date 
        FROM user_plans 
        WHERE user_id = ? 
        ORDER BY start_date ASC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id" class="dark">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Plans - TrainHub</title>

    <link href="<?php echo asset('/views/css/tailwind.css'); ?>" rel="stylesheet">
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
                    <a href="<?php echo url('/app'); ?>" class="text-2xl font-bold text-white">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                </div>

                <!-- Center: Desktop Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="<?php echo url('/app'); ?>" class="text-gray-300 hover:text-white transition">Dashboard</a>
                    <a href="<?php echo url('/plans'); ?>" class="text-orange-500 font-semibold">My Plans</a>
                    <a href="<?php echo url('/calendar'); ?>" class="text-gray-300 hover:text-white transition">Calendar</a>
                    <a href="<?php echo url('/stats'); ?>" class="text-gray-300 hover:text-white transition">Statistics</a>
                </div>

                <!-- Right: Logout (Desktop) -->
                <div class="hidden md:flex">
                    <a href="<?php echo url('/logout'); ?>" class="bg-gray-800 hover:bg-red-900/30 text-gray-300 hover:text-red-400 px-4 py-2 rounded-lg text-sm font-medium transition-all border border-gray-700 hover:border-red-800">
                        Logout
                    </a>
                </div>

                <!-- Mobile: Hamburger Button -->
                <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-800 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-gray-800 border-t border-gray-700">
            <div class="px-4 py-3 space-y-3">
                <a href="<?php echo url('/app'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Dashboard</a>
                <a href="<?php echo url('/plans'); ?>" class="block px-3 py-2 rounded-lg text-orange-500 font-semibold bg-gray-900">My Plans</a>
                <a href="<?php echo url('/calendar'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Calendar</a>
                <a href="<?php echo url('/stats'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Statistics</a>
                <div class="pt-3 border-t border-gray-700">
                    <a href="<?php echo url('/logout'); ?>" class="block px-3 py-2 rounded-lg bg-red-900/30 text-red-400 hover:bg-red-900/50 transition text-center font-medium">
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
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white">Koleksi Rencana Latihan</h1>
            <a href="<?php echo url('/app'); ?>" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
                + Buat Rencana Baru
            </a>
        </div>

        <div id="notification-area" class="mb-6">
            <!-- Notif akan diisi JS -->
        </div>

        <div id="plans-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if ($result->num_rows > 0): ?>
                <?php while ($plan = $result->fetch_assoc()): ?>
                    <?php
                    // Format tanggal
                    $start = new DateTime($plan['start_date']);
                    $finish = new DateTime($plan['finish_date']);
                    $formatted_range = $start->format('d M Y') . ' - ' . $finish->format('d M Y');

                    // Coach note sementara kita hilangkan dulu atau hardcode, karena tidak disimpan di DB saat ini
                    $coach_note = "Program latihan " . $plan['plan_name'];
                    ?>
                    <!-- KITA TAMBAHKAN ID unik ke card -->
                    <div id="plan-card-<?php echo $plan['id']; ?>" class="bg-gray-900 rounded-xl border border-gray-800 p-6 flex flex-col justify-between hover:border-orange-500/50 transition">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="bg-orange-600/20 text-orange-500 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                    <?php echo htmlspecialchars($formatted_range); ?>
                                </span>
                                <!-- Status logic could be complex for multi-week, for now just show Active -->
                                <span class="bg-green-600/20 text-green-500 px-3 py-1 rounded-full text-xs font-bold">Active</span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                            <p class="text-sm text-gray-300 mb-4 line-clamp-2">"<?php echo htmlspecialchars($coach_note); ?>"</p>
                        </div>
                        <div class="text-xs text-gray-400 pt-4 border-t border-gray-800 flex justify-between items-center">
                            <div class="flex gap-3">
                                <a href="<?php echo url('/calendar'); ?>" class="text-orange-500 hover:text-orange-400 font-medium">Lihat di Kalender</a>
                                <button onclick="openPlanDetails(<?php echo $plan['id']; ?>)" class="text-blue-500 hover:text-blue-400 font-medium">
                                    Lihat Detail
                                </button>
                            </div>

                            <!-- TOMBOL HAPUS BARU (Pake JS) -->
                            <button data-plan-id="<?php echo $plan['id']; ?>"
                                class="delete-plan-btn text-red-500 hover:text-red-400 font-medium">
                                Hapus
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="md:col-span-2 lg:col-span-3 text-center text-gray-400 p-10 bg-gray-900 border border-gray-800 rounded-lg">
                    <span class="text-3xl">📭</span>
                    <h3 class="text-xl font-semibold text-gray-300 mt-4">Belum Ada Rencana</h3>
                    <p class="mt-2">Kamu belum menyimpan rencana apapun. Buat satu di halaman Dashboard!</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Plan Details Modal -->
    <div id="planDetailsModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display:none;">
        <div class="min-h-screen flex items-center justify-center">

            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity z-40" aria-hidden="true" onclick="closePlanDetails()"></div>

            <div class="modal-content bg-gray-800 w-11/12 md:max-w-md mx-auto rounded-xl shadow-2xl z-50 relative flex flex-col max-h-[90vh] overflow-hidden">
                <div class="bg-gray-800 px-4 py-5 sm:px-6 border-b border-gray-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-white" id="modal-plan-name">
                            Loading...
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-300" id="modal-plan-date">
                            ...
                        </p>
                    </div>
                    <button type="button" class="text-gray-300 hover:text-white" onclick="closePlanDetails()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-gray-900 px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div id="modal-timeline" class="space-y-4">
                        <!-- Timeline items will be injected here -->
                        <div class="text-center py-8">
                            <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" onclick="closePlanDetails()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationArea = document.getElementById('notification-area');

            // Cek status dari URL (misal dari delete_plan.php yg LAMA)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status') === 'deleted') {
                showNotification('Rencana telah berhasil dihapus.', 'success');
            }

            document.querySelectorAll('.delete-plan-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const planId = this.dataset.planId;

                    if (!confirm('Yakin ingin menghapus rencana ini?')) {
                        return;
                    }

                    try {
                        const response = await fetch('<?php echo url('/controllers/delete_plan.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                plan_id: planId
                            })
                        });
                        const result = await response.json();

                        if (result.success) {
                            // Hapus card dari DOM
                            document.getElementById(`plan-card-${planId}`).remove();
                            showNotification(result.message, 'success');
                        } else {
                            showNotification(result.message, 'error');
                        }
                    } catch (err) {
                        showNotification('Gagal terhubung ke server.', 'error');
                    }
                });
            });

            function showNotification(message, type) {
                const bgColor = type === 'success' ? 'bg-green-900/20 border-green-700 text-green-300' : 'bg-red-900/20 border-red-700 text-red-300';
                notificationArea.innerHTML = `<div class="${bgColor} px-4 py-3 rounded-lg">${message}</div>`;
                setTimeout(() => {
                    notificationArea.innerHTML = '';
                }, 3000);
            }
        });

        // Modal Functions (Outside DOMContentLoaded to be accessible globally)
        const modal = document.getElementById('planDetailsModal');
        const modalTitle = document.getElementById('modal-plan-name');
        const modalDate = document.getElementById('modal-plan-date');
        const modalTimeline = document.getElementById('modal-timeline');

        function closePlanDetails() {
            modal.style.display = 'none';
        }

        async function openPlanDetails(planId) {
            console.log('openPlanDetails called with planId:', planId);
            modal.style.display = 'block';
            console.log('Modal display set to block');

            // Reset / Loading State
            modalTitle.innerText = "Loading...";
            modalDate.innerText = "...";
            modalTimeline.innerHTML = `
                <div class="text-center py-8">
                    <svg class="animate-spin h-8 w-8 text-orange-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;

            try {
                const response = await fetch(`<?php echo url('/controllers/get_plan_details.php'); ?>?plan_id=${planId}`);
                const data = await response.json();

                if (data.error) {
                    throw new Error(data.error);
                }

                renderPlanDetails(data);

            } catch (error) {
                modalTimeline.innerHTML = `
                    <div class="text-center py-8 text-red-400">
                        <p>Gagal memuat detail rencana.</p>
                        <p class="text-xs mt-1">${error.message}</p>
                    </div>
                `;
            }
        }

        function renderPlanDetails(data) {
            modalTitle.innerText = data.plan_name;

            // Format Date Range
            const start = new Date(data.start_date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const finish = new Date(data.finish_date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            modalDate.innerText = `${start} - ${finish}`;

            let html = '';

            data.timeline.forEach(item => {
                let statusColor = 'bg-gray-700 text-gray-300';
                let icon = '';

                switch (item.status) {
                    case 'completed':
                        statusColor = 'bg-green-900/30 text-green-400 border border-green-800';
                        icon = '✅';
                        break;
                    case 'today':
                        statusColor = 'bg-blue-900/30 text-blue-400 border border-blue-800';
                        icon = '📍';
                        break;
                    case 'missed':
                        statusColor = 'bg-red-900/30 text-red-400 border border-red-800';
                        icon = '❌';
                        break;
                    case 'rest':
                        statusColor = 'bg-gray-800 text-gray-400 border border-gray-700 border-dashed';
                        icon = '💤';
                        break;
                    default: // upcoming
                        statusColor = 'bg-gray-800 text-gray-300 border border-gray-700';
                        icon = '📅';
                }

                const dateObj = new Date(item.date);
                const dateDisplay = dateObj.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });

                html += `
                    <div class="flex items-center gap-4 p-3 rounded-lg ${statusColor}">
                        <div class="flex-shrink-0 w-12 text-center">
                            <div class="text-xs font-bold uppercase opacity-70">${item.day_name.substr(0, 3)}</div>
                            <div class="text-sm font-bold">${dateDisplay.split(' ')[0]}</div>
                        </div>
                        <div class="flex-grow">
                            <div class="font-medium text-sm">${item.session_title}</div>
                            <div class="text-xs opacity-75 mt-0.5">${item.status_label}</div>
                        </div>
                        <div class="flex-shrink-0 text-lg">
                            ${icon}
                        </div>
                    </div>
                `;
            });

            modalTimeline.innerHTML = html;
        }
    </script>
</body>

</html>
