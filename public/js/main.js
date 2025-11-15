// ============================================
// TrainHub - Main JavaScript
// Global scripts untuk semua halaman
// ============================================

// === AUTH CHECK & REDIRECT ===
function requireAuth() {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
        alert('Anda harus login dulu!');
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

// === NAVBAR FUNCTIONALITY ===
class Navbar {
    constructor() {
        this.mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        this.mobileMenu = document.getElementById('mobile-menu');
        this.logoutBtns = document.querySelectorAll('[id^="logout-btn"]');
        this.usernameDisplays = document.querySelectorAll('[id^="username-display"]');
        
        this.init();
    }
    
    init() {
        this.setupMobileMenu();
        this.displayUsername();
        this.setupLogout();
        this.highlightActiveNav();
    }
    
    // Mobile menu toggle
    setupMobileMenu() {
        if (!this.mobileMenuToggle || !this.mobileMenu) return;
        
        // Toggle menu saat klik hamburger
        this.mobileMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            this.mobileMenu.classList.toggle('active');
        });
        
        // Close menu saat klik link
        const menuLinks = this.mobileMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                this.mobileMenu.classList.remove('active');
            });
        });
        
        // Close menu saat klik di luar
        document.addEventListener('click', (e) => {
            if (!this.mobileMenu.contains(e.target) && 
                !this.mobileMenuToggle.contains(e.target)) {
                this.mobileMenu.classList.remove('active');
            }
        });
        
        // Close menu saat ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.mobileMenu.classList.remove('active');
            }
        });
    }
    
    // Display username dari localStorage
    displayUsername() {
        const username = localStorage.getItem('username') || 'User';
        this.usernameDisplays.forEach(el => {
            el.textContent = username;
        });
    }
    
    // Setup logout functionality
    setupLogout() {
        this.logoutBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.logout();
            });
        });
    }
    
    logout() {
        if (confirm('Yakin mau logout?')) {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('username');
            localStorage.removeItem('user_id');
            
            // Redirect ke login
            window.location.href = 'login.html';
        }
    }
    
    // Highlight active nav link berdasarkan current page
    highlightActiveNav() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const navLinks = document.querySelectorAll('nav a[href]');
        
        navLinks.forEach(link => {
            const linkPage = link.getAttribute('href');
            
            // Remove active classes
            link.classList.remove('text-orange-500', 'font-semibold', 'active');
            
            // Add active class jika match
            if (linkPage === currentPage) {
                link.classList.add('text-orange-500', 'font-semibold', 'active');
            }
        });
    }
}

// === API HELPER ===
class API {
    constructor() {
        this.baseURL = window.location.origin + '/trainhub/api';
        this.token = localStorage.getItem('jwt_token');
    }
    
    // Get token terbaru (kalo di-refresh)
    getToken() {
        return localStorage.getItem('jwt_token');
    }
    
    // Generate headers dengan auth
    getHeaders(includeContentType = true) {
        const headers = {
            'Authorization': `Bearer ${this.getToken()}`
        };
        
        if (includeContentType) {
            headers['Content-Type'] = 'application/json';
        }
        
        return headers;
    }
    
    // Fetch dengan error handling
    async request(endpoint, options = {}) {
        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, {
                ...options,
                headers: {
                    ...this.getHeaders(),
                    ...options.headers
                }
            });
            
            // Jika 401 (Unauthorized), logout paksa
            if (response.status === 401) {
                alert('Sesi Anda telah habis. Silakan login kembali.');
                localStorage.clear();
                window.location.href = 'login.html';
                return null;
            }
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP Error ${response.status}`);
            }
            
            return data;
            
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
    
    // Shorthand methods
    async get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    }
    
    async post(endpoint, body) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(body)
        });
    }
    
    async put(endpoint, body) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(body)
        });
    }
    
    async delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
}

// === TOAST NOTIFICATIONS ===
class Toast {
    static show(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Icon berdasarkan type
        const icons = {
            success: '✓',
            error: '✕',
            warning: '⚠',
            info: 'ℹ'
        };
        
        toast.innerHTML = `
            <span class="toast-icon">${icons[type]}</span>
            <span class="toast-message">${message}</span>
        `;
        
        // Styling
        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '1rem 1.5rem',
            borderRadius: '0.5rem',
            color: 'white',
            fontWeight: '500',
            fontSize: '0.875rem',
            zIndex: '10000',
            display: 'flex',
            alignItems: 'center',
            gap: '0.5rem',
            boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.3)',
            animation: 'slideIn 0.3s ease-out',
            maxWidth: '90vw'
        });
        
        // Color berdasarkan type
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        toast.style.background = colors[type];
        
        // Append ke body
        document.body.appendChild(toast);
        
        // Auto remove setelah 3 detik
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    static success(message) {
        this.show(message, 'success');
    }
    
    static error(message) {
        this.show(message, 'error');
    }
    
    static warning(message) {
        this.show(message, 'warning');
    }
    
    static info(message) {
        this.show(message, 'info');
    }
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .toast-icon {
        font-size: 1.25rem;
        line-height: 1;
    }
    
    @media (max-width: 640px) {
        .toast {
            left: 20px !important;
            right: 20px !important;
            max-width: calc(100vw - 40px) !important;
        }
    }
`;
document.head.appendChild(style);

// === LOADING OVERLAY ===
class Loading {
    static show(message = 'Loading...') {
        // Remove existing overlay kalo ada
        this.hide();
        
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.innerHTML = `
            <div class="loading-content">
                <div class="spinner"></div>
                <p>${message}</p>
            </div>
        `;
        
        Object.assign(overlay.style, {
            position: 'fixed',
            inset: '0',
            background: 'rgba(0, 0, 0, 0.8)',
            backdropFilter: 'blur(4px)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: '9999',
            animation: 'fadeIn 0.2s ease-out'
        });
        
        const content = overlay.querySelector('.loading-content');
        Object.assign(content.style, {
            textAlign: 'center',
            color: 'white'
        });
        
        const spinner = overlay.querySelector('.spinner');
        Object.assign(spinner.style, {
            width: '50px',
            height: '50px',
            border: '4px solid rgba(255, 255, 255, 0.2)',
            borderTopColor: '#f97316',
            borderRadius: '50%',
            animation: 'spin 0.8s linear infinite',
            margin: '0 auto 1rem'
        });
        
        document.body.appendChild(overlay);
    }
    
    static hide() {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) {
            overlay.style.animation = 'fadeOut 0.2s ease-out';
            setTimeout(() => overlay.remove(), 200);
        }
    }
}

// === UTILITY FUNCTIONS ===
const Utils = {
    // Format tanggal
    formatDate(dateString, options = {}) {
        const defaultOptions = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        
        return new Date(dateString).toLocaleDateString('id-ID', {
            ...defaultOptions,
            ...options
        });
    },
    
    // Format durasi (seconds to readable)
    formatDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        
        if (hours > 0) {
            return `${hours}j ${minutes}m`;
        }
        return `${minutes} menit`;
    },
    
    // Truncate text
    truncate(text, maxLength = 100) {
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    },
    
    // Debounce function
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Copy to clipboard
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            Toast.success('Berhasil disalin!');
        } catch (err) {
            Toast.error('Gagal menyalin');
        }
    }
};

// === INITIALIZATION ===
// Inisialisasi navbar saat DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Init navbar (kalo ada)
    if (document.querySelector('nav')) {
        window.navbar = new Navbar();
    }
    
    // Init API helper
    window.api = new API();
    
    // Make Toast & Utils globally accessible
    window.Toast = Toast;
    window.Utils = Utils;
    window.Loading = Loading;
});

// Export untuk digunakan di file lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { Navbar, API, Toast, Utils, Loading, requireAuth };
}