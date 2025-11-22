<?php
session_start();
include '../config.php';
?>
<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changelog - TrainHub</title>
    <link href="<?php echo asset('/views/css/tailwind.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .commit-card {
            transition: all 0.3s ease;
        }

        .commit-card:hover {
            transform: translateX(4px);
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid #ea580c;
            border-radius: 50%;
            width: 40px;
            height: 40px;
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

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-feat {
            background: #10b981;
            color: white;
        }

        .badge-fix {
            background: #ef4444;
            color: white;
        }

        .badge-chore {
            background: #6b7280;
            color: white;
        }

        .badge-docs {
            background: #3b82f6;
            color: white;
        }

        .badge-style {
            background: #8b5cf6;
            color: white;
        }

        .badge-refactor {
            background: #f59e0b;
            color: white;
        }

        .badge-default {
            background: #374151;
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
                    <a href="<?php echo url('/'); ?>" class="text-2xl font-bold text-white tracking-tight">
                        Train<span class="text-orange-500">Hub</span>
                    </a>
                    <div class="hidden md:flex items-center gap-2 text-sm text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span>Version History</span>
                    </div>
                </div>
                <a href="javascript:history.back();" class="text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Header -->
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-white mb-3">📝 Changelog</h1>
            <p class="text-gray-400 text-lg">Riwayat perubahan dan update TrainHub</p>
            <div class="mt-4 flex items-center gap-3 text-sm">
                <a href="https://github.com/TangRmdhn/Praktikum-Web" target="_blank"
                    class="flex items-center gap-2 text-gray-400 hover:text-orange-500 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    <span>TangRmdhn/Praktikum-Web</span>
                </a>
                <span class="text-gray-600">•</span>
                <span class="text-gray-500" id="commit-count">Loading...</span>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loading" class="flex flex-col items-center justify-center py-20">
            <div class="spinner mb-4"></div>
            <p class="text-gray-400">Mengambil data dari GitHub...</p>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden bg-red-900/20 border border-red-800 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-lg font-semibold text-white mb-2">Gagal Memuat Data</h3>
            <p class="text-gray-400 mb-4" id="error-message"></p>
            <button onclick="loadChangelog()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition">
                Coba Lagi
            </button>
        </div>

        <!-- Commits List -->
        <div id="commits" class="space-y-4"></div>

    </main>

    <script>
        const GITHUB_API = 'https://api.github.com/repos/TangRmdhn/Praktikum-Web/commits';
        const CACHE_KEY = 'trainhub_changelog_cache';
        const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

        // Parse commit type from message
        function getCommitType(message) {
            const match = message.match(/^(feat|fix|chore|docs|style|refactor|perf|test):/i);
            return match ? match[1].toLowerCase() : 'default';
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;

            // Less than 1 day
            if (diff < 86400000) {
                const hours = Math.floor(diff / 3600000);
                if (hours < 1) {
                    const minutes = Math.floor(diff / 60000);
                    return `${minutes} menit yang lalu`;
                }
                return `${hours} jam yang lalu`;
            }

            // Less than 7 days
            if (diff < 604800000) {
                const days = Math.floor(diff / 86400000);
                return `${days} hari yang lalu`;
            }

            // Format as date
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        // Render commits
        function renderCommits(commits) {
            const container = document.getElementById('commits');
            container.innerHTML = '';

            commits.forEach(commit => {
                const type = getCommitType(commit.commit.message);
                const message = commit.commit.message.split('\n')[0]; // First line only
                const author = commit.commit.author.name;
                const date = formatDate(commit.commit.author.date);
                const sha = commit.sha.substring(0, 7);

                const card = document.createElement('div');
                card.className = 'commit-card bg-gray-900 border border-gray-800 rounded-lg p-5 hover:border-gray-700';
                card.innerHTML = `
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 mt-1">
                            <span class="badge badge-${type}">${type}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-medium mb-2 leading-tight">${escapeHtml(message)}</h3>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>${escapeHtml(author)}</span>
                                </div>
                                <span class="text-gray-600">•</span>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>${date}</span>
                                </div>
                                <span class="text-gray-600">•</span>
                                <a href="${commit.html_url}" target="_blank" 
                                   class="flex items-center gap-1.5 text-orange-500 hover:text-orange-400 transition font-mono">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                    <span>${sha}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });

            document.getElementById('commit-count').textContent = `${commits.length} commits`;
        }

        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Load changelog
        async function loadChangelog() {
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const commits = document.getElementById('commits');

            loading.classList.remove('hidden');
            error.classList.add('hidden');
            commits.innerHTML = '';

            try {
                // Check cache
                const cached = localStorage.getItem(CACHE_KEY);
                if (cached) {
                    const {
                        data,
                        timestamp
                    } = JSON.parse(cached);
                    if (Date.now() - timestamp < CACHE_DURATION) {
                        console.log('Using cached data');
                        renderCommits(data);
                        loading.classList.add('hidden');
                        return;
                    }
                }

                // Fetch from GitHub
                const response = await fetch(`${GITHUB_API}?per_page=30&sha=main`);

                if (!response.ok) {
                    throw new Error(`GitHub API error: ${response.status}`);
                }

                const data = await response.json();

                // Cache the data
                localStorage.setItem(CACHE_KEY, JSON.stringify({
                    data: data,
                    timestamp: Date.now()
                }));

                renderCommits(data);
                loading.classList.add('hidden');

            } catch (err) {
                console.error('Error loading changelog:', err);
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('error-message').textContent = err.message;
            }
        }

        // Load on page load
        loadChangelog();
    </script>

</body>

</html>