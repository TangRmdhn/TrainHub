<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - TrainHub</title>
    <link href="<?php echo asset('/views/css/tailwind.css'); ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    </noscript>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #ea580c;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="bg-black text-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-gray-900 p-8 rounded-lg border border-gray-800 shadow-xl w-full max-w-md">
        <h1 class="text-3xl font-bold text-center text-white mb-2">
            Train<span class="text-orange-500">Hub</span>
        </h1>
        <p class="text-center text-gray-300 mb-6">Buat akun baru</p>

        <form id="register-form" class="space-y-4" action="<?= url('/controllers/regist_controller.php'); ?>" method="POST">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
                <input type="text" id="username" name="username" required
                    class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md p-3 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required
                    class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md p-3 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required
                        class="mt-1 block w-full bg-gray-800 border border-gray-700 rounded-md p-3 pr-10 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

                    <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-300 transition">
                        <svg id="eye" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-slash" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.947 9.947 0 012.293-3.95m5.02.02a3 3 0 013.96 3.96M6.18 6.18l12.64 12.64" />
                        </svg>
                    </button>
                </div>
                <div id="password-strength" class="text-xs mt-1"></div>
            </div>

            <button type="submit" id="submit-btn"
                class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 transform hover:scale-[1.02]">
                Daftar
            </button>

            <div id="message" class="text-center text-sm mt-4"></div>
        </form>

        <p class="text-center text-sm text-gray-300 mt-6">
            Sudah punya akun?
            <a href="<?= url('/login'); ?>" class="font-medium text-orange-500 hover:text-orange-400 transition">Masuk di sini</a>
        </p>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const input = document.getElementById("password");
            const eye = document.getElementById("eye");
            const eyeSlash = document.getElementById("eye-slash");

            eye.classList.toggle("hidden");
            eyeSlash.classList.toggle("hidden");
            input.type = input.type === "password" ? "text" : "password";
        }

        // Show message with styling
        function showMessage(text, type = 'info') {
            const messageDiv = document.getElementById('message');
            const colors = {
                success: 'text-green-500',
                error: 'text-red-500',
                warning: 'text-yellow-500',
                info: 'text-blue-500'
            };

            messageDiv.textContent = text;
            messageDiv.className = `${colors[type]} text-center text-sm font-medium mt-4`;
        }

        // Password strength checker
        function getPasswordStrength(password) {
            if (password.length === 0) return {
                score: 0,
                text: '',
                color: ''
            };
            if (password.length < 6) return {
                score: 1,
                text: 'Lemah',
                color: 'text-red-500'
            };

            let score = 1;
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^a-zA-Z0-9]/.test(password)) score++;

            const levels = [{
                    score: 1,
                    text: 'Lemah',
                    color: 'text-red-500'
                },
                {
                    score: 2,
                    text: 'Sedang',
                    color: 'text-yellow-500'
                },
                {
                    score: 3,
                    text: 'Bagus',
                    color: 'text-green-500'
                },
                {
                    score: 4,
                    text: 'Kuat',
                    color: 'text-green-400'
                },
                {
                    score: 5,
                    text: 'Sangat Kuat',
                    color: 'text-green-300'
                }
            ];

            return levels.find(l => l.score === score) || levels[0];
        }

        // Show password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthDiv = document.getElementById('password-strength');

        passwordInput.addEventListener('input', () => {
            const strength = getPasswordStrength(passwordInput.value);

            if (strength.score === 0) {
                strengthDiv.textContent = '';
            } else {
                strengthDiv.className = `text-xs mt-1 font-medium ${strength.color}`;
                strengthDiv.textContent = `Kekuatan password: ${strength.text}`;
            }
        });

        // Auto-focus username field
        document.getElementById('username').focus();
    </script>
</body>

</html>