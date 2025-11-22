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
// 2. Ambil data profil user
$sql = "SELECT u.*, p.* FROM users u LEFT JOIN user_profiles p ON u.id = p.user_id WHERE u.id = '$user_id'";
$result = $koneksi->query($sql);
if ($result->num_rows == 0) {
    header("Location: " . url("/logout"));
    exit;
}
$user = $result->fetch_assoc();

// 3. Siapkan data profil untuk dikirim ke API Python (untuk context awal)
$profile_for_api = [
    'user_id' => (int)$user['id'], // Penting untuk session ID di Python
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
    <title>AI Coach - TrainHub</title>

    <link href="<?php echo asset('/views/css/tailwind.css'); ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Chat Bubble Styles */
        .chat-bubble {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 12px;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
        }

        .chat-bubble.user {
            background-color: #ea580c;
            /* Orange-600 */
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }

        .chat-bubble.ai {
            background-color: #1f2937;
            /* Gray-800 */
            color: #e5e7eb;
            /* Gray-200 */
            align-self: flex-start;
            border-bottom-left-radius: 2px;
            border: 1px solid #374151;
        }

        .typing-indicator span {
            display: inline-block;
            width: 6px;
            height: 6px;
            background-color: #9ca3af;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out both;
            margin: 0 2px;
        }

        .typing-indicator span:nth-child(1) {
            animation-delay: -0.32s;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes typing {

            0%,
            80%,
            100% {
                transform: scale(0);
            }

            40% {
                transform: scale(1);
            }
        }
        
        /* Markdown Styles inside Chat */
        .chat-bubble.ai ul { list-style-type: disc; margin-left: 1.5rem; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .chat-bubble.ai ol { list-style-type: decimal; margin-left: 1.5rem; margin-top: 0.5rem; margin-bottom: 0.5rem; }
        .chat-bubble.ai strong { font-weight: 700; color: white; }
        .chat-bubble.ai p { margin-bottom: 0.5rem; }
        .chat-bubble.ai p:last-child { margin-bottom: 0; }
    </style>
</head>

<body class="bg-black text-gray-100 min-h-screen flex flex-col">

    <!-- Navbar -->
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
                    <a href="<?php echo url('/app'); ?>" class="text-gray-300 hover:text-white transition">Dashboard</a>
                    <a href="<?php echo url('/plans'); ?>" class="text-gray-300 hover:text-white transition">My Plans</a>
                    <a href="<?php echo url('/calendar'); ?>" class="text-gray-300 hover:text-white transition">Calendar</a>
                    <a href="<?php echo url('/stats'); ?>" class="text-gray-300 hover:text-white transition">Statistics</a>
                    <a href="<?php echo url('/chat'); ?>" class="text-orange-500 font-semibold">AI Coach</a>
                </div>

                <!-- Right: User/Logout (Desktop) -->
                <div class="hidden md:flex items-center gap-4">
                    <div class="text-right leading-tight">
                        <div class="text-sm font-medium text-white"><?php echo htmlspecialchars($user['username']); ?></div>
                        <div class="text-xs text-gray-300"><?php echo htmlspecialchars($user['fitness_goal']); ?></div>
                    </div>
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
                <a href="<?php echo url('/plans'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">My Plans</a>
                <a href="<?php echo url('/calendar'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Calendar</a>
                <a href="<?php echo url('/stats'); ?>" class="block px-3 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition">Statistics</a>
                <a href="<?php echo url('/chat'); ?>" class="block px-3 py-2 rounded-lg text-orange-500 font-semibold bg-gray-900">AI Coach</a>
                <div class="pt-3 border-t border-gray-700">
                    <div class="px-3 py-2 text-sm font-medium text-white"><?php echo htmlspecialchars($user['username']); ?></div>
                    <div class="px-3 pb-2 text-xs text-gray-300"><?php echo htmlspecialchars($user['fitness_goal']); ?></div>
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

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-grow w-full flex flex-col h-[calc(100vh-64px)]">

        <div class="flex-grow flex flex-col bg-gray-900 rounded-xl border border-gray-800 shadow-xl overflow-hidden mb-4">
            
            <!-- Chat Header -->
            <div class="bg-gray-800 p-4 border-b border-gray-700 flex items-center gap-3">
                <div class="bg-orange-600/20 p-2 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">Personal AI Coach</h1>
                    <p class="text-xs text-green-400 flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online
                    </p>
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div id="chat-container" class="flex-grow overflow-y-auto p-4 flex flex-col gap-2 custom-scrollbar">
                <!-- Welcome Message -->
                <div class="chat-bubble ai">
                    <p>Halo <strong><?php echo htmlspecialchars($user['username']); ?></strong>! 👋</p>
                    <p class="mt-2">Saya AI Coach pribadi kamu. Saya sudah mempelajari profil fisik dan goal kamu (<strong><?php echo htmlspecialchars($user['fitness_goal']); ?></strong>).</p>
                    <p class="mt-2">Ada yang bisa saya bantu hari ini? Tanya soal latihan, nutrisi, atau motivasi!</p>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-4 bg-gray-800 border-t border-gray-700">
                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="user-input" 
                        class="flex-grow bg-gray-900 border border-gray-700 text-white text-sm rounded-lg p-3 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                        placeholder="Tanya sesuatu..." autocomplete="off">
                    <button type="submit" id="send-btn" 
                        class="bg-orange-600 hover:bg-orange-500 text-white p-3 rounded-lg transition shadow-lg flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </main>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const chatForm = document.getElementById('chat-form');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');

        // User Profile Data from PHP
        const userProfile = <?php echo $user_profile_json; ?>;
        
        // API URL HuggingFace
        // const API_URL = "https://indraprhmbd-trainhub-ai.hf.space/chat";

        // API URL Local
        const API_URL = "http://127.0.0.1:8000/chat";

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = userInput.value.trim();
            if (!message) return;

            // 1. Add User Message
            addMessage(message, 'user');
            userInput.value = '';
            userInput.focus();

            // 2. Show Loading
            const loadingId = addLoadingIndicator();
            scrollToBottom();

            try {
                // 3. Call API
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userProfile.user_id, // Send ID for session tracking
                        message: message,
                        user_profile: userProfile
                    })
                });

                if (!response.ok) {
                    throw new Error("Gagal menghubungi AI Coach.");
                }

                const data = await response.json();
                
                // 4. Remove Loading & Add AI Response
                removeMessage(loadingId);
                addMessage(data.response, 'ai');

            } catch (error) {
                removeMessage(loadingId);
                addMessage("Maaf, terjadi kesalahan saat menghubungi server. Coba lagi nanti.", 'ai');
                console.error(error);
            }
            
            scrollToBottom();
        });

        function addMessage(text, sender) {
            const div = document.createElement('div');
            div.className = `chat-bubble ${sender}`;
            
            // Simple Markdown parsing for AI messages
            if (sender === 'ai') {
                // Convert newlines to <br> first? No, let's keep it simple or use a library if needed.
                // For now, just basic text. If API returns markdown, we might want a parser.
                // Let's do basic formatting: **bold** -> <strong>
                let formattedText = text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\n/g, '<br>');
                div.innerHTML = formattedText;
            } else {
                div.textContent = text;
            }
            
            chatContainer.appendChild(div);
        }

        function addLoadingIndicator() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'chat-bubble ai';
            div.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
            chatContainer.appendChild(div);
            return id;
        }

        function removeMessage(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    </script>
</body>
</html>
