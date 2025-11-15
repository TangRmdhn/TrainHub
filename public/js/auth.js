// File: public/js/auth.js

// Cek kita lagi di halaman mana
const isRegisterPage = !!document.getElementById('register-form');
const isLoginPage = !!document.getElementById('login-form');

const API_BASE_URL = "/api"; // Sesuai .htaccess

if (isRegisterPage) {
    const registerForm = document.getElementById('register-form');
    const messageDiv = document.getElementById('message');

    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Stop form biar nggak reload
        messageDiv.textContent = 'Mendaftarkan...';
        messageDiv.className = 'text-yellow-500 text-center';

        const formData = new FormData(registerForm);
        const data = Object.fromEntries(formData.entries());

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
            
            // SUKSES
            messageDiv.textContent = 'Registrasi berhasil! Mengalihkan ke halaman login...';
            messageDiv.className = 'text-green-500 text-center';
            
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);

        } catch (error) {
            messageDiv.textContent = error.message;
            messageDiv.className = 'text-red-500 text-center';
        }
    });
}

if (isLoginPage) {
    const loginForm = document.getElementById('login-form');
    const messageDiv = document.getElementById('message');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        messageDiv.textContent = 'Memproses...';
        messageDiv.className = 'text-yellow-500 text-center';

        const formData = new FormData(loginForm);
        const data = Object.fromEntries(formData.entries());

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

            // SUKSES LOGIN
            messageDiv.textContent = 'Login berhasil! Mengalihkan ke dashboard...';
            messageDiv.className = 'text-green-500 text-center';
            
            // [PENTING] Simpen Token-nya!
            localStorage.setItem('jwt_token', result.token);
            localStorage.setItem('username', result.user.username);
            
            setTimeout(() => {
                // Lempar ke halaman APLIKASI UTAMA
                window.location.href = 'app.html';
            }, 1500);

        } catch (error) {
            messageDiv.textContent = error.message;
            messageDiv.className = 'text-red-500 text-center';
        }
    });
}