<?php
session_start();
// Cek login, kalau belum login lempar balik
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: /login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Profil - TrainHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Radio Button Style */
        .radio-card:checked+div {
            border-color: #ea580c;
            /* orange-600 */
            background-color: rgba(234, 88, 12, 0.1);
        }
    </style>
</head>

<body class="bg-black text-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-2xl mb-8">
        <div class="flex justify-between text-xs uppercase text-gray-500 font-semibold tracking-wider mb-2">
            <span id="step-label">Langkah 1 dari 4</span>
            <span id="step-percent">25%</span>
        </div>
        <div class="w-full bg-gray-800 rounded-full h-2">
            <div id="progress-bar" class="bg-orange-600 h-2 rounded-full transition-all duration-500" style="width: 25%"></div>
        </div>
    </div>

    <div class="bg-gray-900 p-8 rounded-2xl border border-gray-800 shadow-2xl w-full max-w-2xl">

        <form id="screeningForm" action="../controllers/screening_controller.php" method="POST">

            <div class="step-content active" data-step="1">
                <h2 class="text-2xl font-bold text-white mb-2">Ceritakan Sedikit Tentang Diri Anda</h2>
                <p class="text-gray-400 mb-6">Data ini membantu AI menghitung kebutuhan kalori dan batas aman latihan Anda.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jenis Kelamin</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="gender" value="Laki-laki" class="hidden radio-card" required>
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-4 text-center hover:border-gray-500 transition-all">
                                    👨 Laki-laki
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="gender" value="Perempuan" class="hidden radio-card">
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-4 text-center hover:border-gray-500 transition-all">
                                    👩 Perempuan
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Usia (thn)</label>
                            <input type="number" name="age" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:border-orange-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Berat (kg)</label>
                            <input type="number" name="weight" step="0.1" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:border-orange-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Tinggi (cm)</label>
                            <input type="number" name="height" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:border-orange-500 focus:outline-none" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-content" data-step="2">
                <h2 class="text-2xl font-bold text-white mb-2">Apa Target Utama Anda?</h2>
                <p class="text-gray-400 mb-6">Pilih target yang paling prioritas saat ini.</p>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="fitness_goal" value="Fat Loss" class="hidden radio-card" required>
                            <div class="border border-gray-700 bg-gray-800 rounded-lg p-4 hover:border-orange-500 transition-all">
                                <div class="font-semibold text-white">🔥 Fat Loss</div>
                                <div class="text-xs text-gray-400">Turunkan berat badan & bakar lemak</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="fitness_goal" value="Muscle Gain" class="hidden radio-card">
                            <div class="border border-gray-700 bg-gray-800 rounded-lg p-4 hover:border-orange-500 transition-all">
                                <div class="font-semibold text-white">💪 Muscle Gain</div>
                                <div class="text-xs text-gray-400">Bangun massa otot & kekuatan</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="fitness_goal" value="Endurance" class="hidden radio-card">
                            <div class="border border-gray-700 bg-gray-800 rounded-lg p-4 hover:border-orange-500 transition-all">
                                <div class="font-semibold text-white">🏃 Endurance</div>
                                <div class="text-xs text-gray-400">Tingkatkan stamina & kardio</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="fitness_goal" value="Keep Fit" class="hidden radio-card">
                            <div class="border border-gray-700 bg-gray-800 rounded-lg p-4 hover:border-orange-500 transition-all">
                                <div class="font-semibold text-white">🧘 Keep Fit</div>
                                <div class="text-xs text-gray-400">Jaga kesehatan umum & mobilitas</div>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">Tingkat Pengalaman</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="fitness_level" value="Beginner" class="hidden radio-card" required>
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-3 text-center hover:border-orange-500 transition-all text-sm">
                                    🌱 Pemula
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="fitness_level" value="Intermediate" class="hidden radio-card">
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-3 text-center hover:border-orange-500 transition-all text-sm">
                                    ⚡ Menengah
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="fitness_level" value="Advanced" class="hidden radio-card">
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-3 text-center hover:border-orange-500 transition-all text-sm">
                                    🔥 Mahir
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-content" data-step="3">
                <h2 class="text-2xl font-bold text-white mb-2">Ketersediaan & Fasilitas</h2>
                <p class="text-gray-400 mb-6">Sesuaikan dengan jadwal dan alat yang Anda punya.</p>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tempat / Alat Latihan</label>
                        <select name="equipment_access" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white focus:border-orange-500 outline-none">
                            <option value="Gym Lengkap">🏢 Gym Komersial (Alat Lengkap)</option>
                            <option value="Home Dumbbell">🏠 Di Rumah (Cuma ada Dumbbell)</option>
                            <option value="Bodyweight Only">🌳 Tanpa Alat (Bodyweight)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Berapa hari seminggu Anda bisa latihan?</label>
                        <input type="range" name="days_per_week" min="1" max="7" value="3" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer accent-orange-500" oninput="document.getElementById('daysOutput').innerText = this.value + ' Hari'">
                        <div class="text-right text-orange-500 font-bold mt-1" id="daysOutput">3 Hari</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Durasi per sesi (Menit)?</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="minutes_per_session" value="30" class="hidden radio-card">
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-3 text-center hover:border-orange-500 text-sm">30 Mins</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="minutes_per_session" value="60" class="hidden radio-card" checked>
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-3 text-center hover:border-orange-500 text-sm">60 Mins</div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="minutes_per_session" value="90" class="hidden radio-card">
                                <div class="border border-gray-700 bg-gray-800 rounded-lg p-3 text-center hover:border-orange-500 text-sm">90+ Mins</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="step-content" data-step="4">
                <h2 class="text-2xl font-bold text-white mb-2">Terakhir, Ada Cedera?</h2>
                <p class="text-gray-400 mb-6">Penting agar AI tidak memberikan gerakan yang membahayakan.</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tuliskan cedera atau kondisi medis (jika ada)</label>
                        <textarea name="injuries" rows="4" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 focus:border-orange-500 focus:outline-none placeholder-gray-600" placeholder="Contoh: Sakit pinggang bawah, lutut kanan sering bunyi, asma... (Kosongkan jika sehat)"></textarea>
                    </div>

                    <div class="bg-orange-900/20 border border-orange-800/50 rounded-lg p-4 flex items-start gap-3 mt-4">
                        <span class="text-xl">🤖</span>
                        <p class="text-xs text-orange-200/80">
                            Setelah ini, data Anda akan diproses untuk menyiapkan Dashboard AI Personal Anda.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-8 pt-6 border-t border-gray-800">
                <button type="button" id="prevBtn" class="text-gray-400 hover:text-white font-medium px-4 py-2 hidden">
                    Kembali
                </button>
                <button type="button" id="nextBtn" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-8 rounded-full transition-all shadow-lg shadow-orange-700/20">
                    Lanjut
                </button>
                <button type="submit" id="submitBtn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-8 rounded-full transition-all shadow-lg shadow-green-700/20 hidden">
                    Selesai & Masuk
                </button>
            </div>

        </form>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;

        const stepLabel = document.getElementById('step-label');
        const stepPercent = document.getElementById('step-percent');
        const progressBar = document.getElementById('progress-bar');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const steps = document.querySelectorAll('.step-content');

        function updateUI() {
            // Update Steps Visibility
            steps.forEach(step => {
                if (step.dataset.step == currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Update Progress Bar
            const percent = (currentStep / totalSteps) * 100;
            progressBar.style.width = percent + '%';
            stepPercent.innerText = percent + '%';
            stepLabel.innerText = `Langkah ${currentStep} dari ${totalSteps}`;

            // Update Buttons
            if (currentStep === 1) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }

            if (currentStep === totalSteps) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        nextBtn.addEventListener('click', () => {
            // Simple Validation (Optional: Bisa diperketat)
            const currentInputs = document.querySelector(`.step-content[data-step="${currentStep}"]`).querySelectorAll('input[required], select[required]');
            let valid = true;
            currentInputs.forEach(input => {
                if (!input.value || (input.type === 'radio' && !document.querySelector(`input[name="${input.name}"]:checked`))) {
                    valid = false;
                }
            });

            if (!valid) {
                alert('Mohon lengkapi data dahulu!');
                return;
            }

            if (currentStep < totalSteps) {
                currentStep++;
                updateUI();
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        });

        // Initialize
        updateUI();
    </script>
</body>

</html>