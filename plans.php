<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil semua rencana user, diurut dari yang terbaru
$sql = "SELECT id, plan_name, plan_date, plan_json, is_completed 
        FROM user_plans 
        WHERE user_id = ? 
        ORDER BY plan_date DESC";
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-black text-gray-100 min-h-screen">
    
    <nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-8">
                    <a href="app.php" class="text-2xl font-bold text-white">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                    <div class="hidden md:flex space-x-6">
                        <a href="app.php" class="text-gray-300 hover:text-white transition">Dashboard</a>
                        <a href="plans.php" class="text-orange-500 font-semibold">My Plans</a>
                        <a href="calendar.php" class="text-gray-300 hover:text-white transition">Calendar</a>
                    </div>
                </div>
                <a href="logout.php" class="text-sm text-red-500 hover:text-red-400 font-medium">Logout</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white">Koleksi Rencana Latihan</h1>
            <a href="app.php" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">
                + Buat Rencana Baru
            </a>
        </div>
        
        <div id="notification-area" class="mb-6">
            <!-- Notif akan diisi JS -->
        </div>

        <div id="plans-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <?php if ($result->num_rows > 0): ?>
                <?php while($plan = $result->fetch_assoc()): ?>
                    <?php
                        // Format tanggal biar cakep
                        $date = new DateTime($plan['plan_date']);
                        $formatted_date = $date->format('l, j F Y'); // Cth: Senin, 16 November 2025
                        
                        // Ambil detail coach_note dari JSON
                        $plan_details = json_decode($plan['plan_json'], true);
                        $coach_note = $plan_details['coach_note'] ?? 'Tidak ada catatan.';
                    ?>
                    <!-- KITA TAMBAHKAN ID unik ke card -->
                    <div id="plan-card-<?php echo $plan['id']; ?>" class="bg-gray-900 rounded-xl border border-gray-800 p-6 flex flex-col justify-between hover:border-orange-500/50 transition">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span class="bg-orange-600/20 text-orange-500 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                    <?php echo htmlspecialchars($date->format('j M Y')); ?>
                                </span>
                                <?php if($plan['is_completed']): ?>
                                    <span class="bg-green-600/20 text-green-500 px-3 py-1 rounded-full text-xs font-bold">✔ Selesai</span>
                                <?php else: ?>
                                    <span class="bg-gray-700 text-gray-400 px-3 py-1 rounded-full text-xs font-bold">Pending</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                            <p class="text-sm text-gray-400 mb-4 line-clamp-2">"<?php echo htmlspecialchars($coach_note); ?>"</p>
                        </div>
                        <div class="text-xs text-gray-500 pt-4 border-t border-gray-800 flex justify-between items-center">
                            <a href="calendar.php?highlight=<?php echo $plan['plan_date']; ?>" class="text-orange-500 hover:text-orange-400 font-medium">Lihat di Kalender</a>
                            
                            <!-- TOMBOL HAPUS BARU (Pake JS) -->
                            <button data-plan-id="<?php echo $plan['id']; ?>" 
                               class="delete-plan-btn text-red-500 hover:text-red-400 font-medium">
                                Hapus
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="md:col-span-2 lg:col-span-3 text-center text-gray-500 p-10 bg-gray-900 border border-gray-800 rounded-lg">
                    <span class="text-3xl">📭</span>
                    <h3 class="text-xl font-semibold text-gray-300 mt-4">Belum Ada Rencana</h3>
                    <p class="mt-2">Lu belum nyimpen rencana latihan apapun. Buat satu di halaman Dashboard!</p>
                </div>
            <?php endif; ?>
            
        </div>
    </main>

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
                
                if (!confirm('Yakin mau hapus rencana ini, Tang? Gak bisa balik lagi loh.')) {
                    return;
                }

                try {
                    const response = await fetch('delete_plan.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ plan_id: planId })
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
    </script>
</body>
</html>