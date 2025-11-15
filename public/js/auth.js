// ============================================
// TrainHub - Authentication
// Login & Register functionality
// ============================================

// === CONFIG ===
const API_BASE_URL = "/api";

// === DETECT PAGE ===
const isRegisterPage = !!document.getElementById('register-form');
const isLoginPage = !!document.getElementById('login-form');

// === UTILITY FUNCTIONS ===

function togglePassword() {
    const input = document.getElementById("password");
    document.getElementById("eye").classList.toggle("hidden");
    document.getElementById("eye-slash").classList.toggle("hidden");
    input.type = input.type === "password" ? "text" : "password";
}

/**
 * Show loading state pada button
 */
function setButtonLoading(button, isLoading, originalText) {
    if (isLoading) {
        button.disabled = true;
        button.innerHTML = `
            <div class="flex items-center justify-center gap-2">
                <div class="spinner"></div>
                <span>Memproses...</span>
            </div>
        `;
    } else {
        button.disabled = false;
        button.textContent = originalText;
    }
}

/**
 * Show message dengan styling
 */
function showMessage(messageDiv, text, type = 'info') {
    const colors = {
        success: 'text-green-500',
        error: 'text-red-500',
        warning: 'text-yellow-500',
        info: 'text-blue-500'
    };
    
    messageDiv.textContent = text;
    messageDiv.className = `${colors[type]} text-center text-sm font-medium mt-4`;
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validate password strength
 */
function isValidPassword(password) {
    // Min 6 karakter
    return password.length >= 6;
}

// === REGISTER PAGE ===
if (isRegisterPage) {
    const registerForm = document.getElementById('register-form');
    const messageDiv = document.getElementById('message');
    const submitButton = registerForm.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Clear previous message
        messageDiv.textContent = '';
        
        // Get form data
        const formData = new FormData(registerForm);
        const data = Object.fromEntries(formData.entries());
        
        // Validation
        if (!data.username || data.username.length < 3) {
            showMessage(messageDiv, 'Username minimal 3 karakter', 'error');
            return;
        }
        
        if (!isValidEmail(data.email)) {
            showMessage(messageDiv, 'Format email tidak valid', 'error');
            return;
        }
        
        if (!isValidPassword(data.password)) {
            showMessage(messageDiv, 'Password minimal 6 karakter', 'error');
            return;
        }
        
        // Show loading
        setButtonLoading(submitButton, true, originalButtonText);
        showMessage(messageDiv, 'Mendaftarkan akun...', 'info');

        try {
            const response = await fetch(`${API_BASE_URL}/auth/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal mendaftar');
            }
            
            // SUCCESS
            showMessage(messageDiv, '✓ Registrasi berhasil! Mengalihkan ke halaman login...', 'success');
            
            // Disable form to prevent double submission
            registerForm.querySelectorAll('input').forEach(input => input.disabled = true);
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);

        } catch (error) {
            console.error('Register error:', error);
            showMessage(messageDiv, error.message, 'error');
            setButtonLoading(submitButton, false, originalButtonText);
        }
    });
    
    // Real-time email validation
    const emailInput = registerForm.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', () => {
            if (emailInput.value && !isValidEmail(emailInput.value)) {
                emailInput.classList.add('border-red-500');
                showMessage(messageDiv, 'Format email tidak valid', 'error');
            } else {
                emailInput.classList.remove('border-red-500');
                messageDiv.textContent = '';
            }
        });
    }
    
    // Real-time password validation
    const passwordInput = registerForm.querySelector('input[name="password"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            const strength = getPasswordStrength(passwordInput.value);
            showPasswordStrength(strength);
        });
    }
}

// === LOGIN PAGE ===
if (isLoginPage) {
    const loginForm = document.getElementById('login-form');
    const messageDiv = document.getElementById('message');
    const submitButton = loginForm.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.textContent;

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Clear previous message
        messageDiv.textContent = '';
        
        // Get form data
        const formData = new FormData(loginForm);
        const data = Object.fromEntries(formData.entries());
        
        // Validation
        if (!isValidEmail(data.email)) {
            showMessage(messageDiv, 'Format email tidak valid', 'error');
            return;
        }
        
        if (!data.password) {
            showMessage(messageDiv, 'Password tidak boleh kosong', 'error');
            return;
        }
        
        // Show loading
        setButtonLoading(submitButton, true, originalButtonText);
        showMessage(messageDiv, 'Memproses login...', 'info');

        try {
            const response = await fetch(`${API_BASE_URL}/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Gagal login');
            }

            // SUCCESS - Save user data
            localStorage.setItem('jwt_token', result.token);
            localStorage.setItem('username', result.user.username);
            localStorage.setItem('user_id', result.user.id);
            
            showMessage(messageDiv, '✓ Login berhasil! Mengalihkan ke dashboard...', 'success');
            
            // Disable form to prevent double submission
            loginForm.querySelectorAll('input').forEach(input => input.disabled = true);
            
            // Redirect after 1.5 seconds
            setTimeout(() => {
                window.location.href = 'app.html';
            }, 1500);

        } catch (error) {
            console.error('Login error:', error);
            showMessage(messageDiv, error.message, 'error');
            setButtonLoading(submitButton, false, originalButtonText);
        }
    });
    
    // Auto-focus email field
    const emailInput = loginForm.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.focus();
    }
    
    // Check if just registered
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('registered') === 'true') {
        showMessage(messageDiv, 'Registrasi berhasil! Silakan login.', 'success');
        
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

// === PASSWORD STRENGTH CHECKER (BONUS) ===
function getPasswordStrength(password) {
    if (password.length === 0) return { score: 0, text: '', color: '' };
    if (password.length < 6) return { score: 1, text: 'Lemah', color: 'text-red-500' };
    
    let score = 1;
    if (password.length >= 8) score++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
    if (/\d/.test(password)) score++;
    if (/[^a-zA-Z0-9]/.test(password)) score++;
    
    const levels = [
        { score: 1, text: 'Lemah', color: 'text-red-500' },
        { score: 2, text: 'Sedang', color: 'text-yellow-500' },
        { score: 3, text: 'Bagus', color: 'text-green-500' },
        { score: 4, text: 'Kuat', color: 'text-green-400' },
        { score: 5, text: 'Sangat Kuat', color: 'text-green-300' }
    ];
    
    return levels.find(l => l.score === score) || levels[0];
}

function showPasswordStrength(strength) {
    // Check if strength indicator exists
    let indicator = document.getElementById('password-strength');
    
    if (!indicator && strength.score > 0) {
        // Create indicator if doesn't exist
        const passwordInput = document.querySelector('input[name="password"]');
        indicator = document.createElement('div');
        indicator.id = 'password-strength';
        indicator.className = 'text-xs mt-1';
        passwordInput.parentElement.appendChild(indicator);
    }
    
    if (indicator) {
        if (strength.score === 0) {
            indicator.textContent = '';
        } else {
            indicator.className = `text-xs mt-1 font-medium ${strength.color}`;
            indicator.textContent = `Kekuatan password: ${strength.text}`;
        }
    }
}

// === AUTO-CLEAR LOCALSTORAGE (Prevent bugs) ===
// Kalo di login/register page, clear token lama
if (isLoginPage || isRegisterPage) {
    // Jangan clear langsung, cek dulu apakah ada token valid
    const existingToken = localStorage.getItem('jwt_token');
    if (existingToken) {
        // User udah login, redirect ke app
        if (isLoginPage) {
            showMessage(
                document.getElementById('message'), 
                'Anda sudah login. Mengalihkan...', 
                'info'
            );
            setTimeout(() => {
                window.location.href = 'app.html';
            }, 1000);
        }
    }
}