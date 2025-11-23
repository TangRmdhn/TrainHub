# TrainHub - AI-Powered Workout Planner

TrainHub adalah aplikasi web yang membantu pengguna membuat rencana latihan mingguan yang dipersonalisasi menggunakan AI (Google Gemini). Aplikasi ini memiliki dashboard untuk mengelola rencana, tampilan kalender untuk melacak latihan, pelacakan statistik, dan generator rencana bertenaga AI.

## Live Demo

**[Kunjungi Website Live](https://trainhub.web.id)**

**[Coba AI](https://trainhub.web.id)**

## Fitur

- **Generator Latihan AI**: Membuat template latihan 7 hari yang dipersonalisasi berdasarkan profil pengguna (tujuan kebugaran, level, peralatan, dll.)
- **AI Coach Chatbot**: Chat interaktif dengan AI personal trainer yang memahami profil dan goal fitnessmu, siap menjawab pertanyaan seputar latihan, nutrisi, dan memberikan motivasi
- **Model Template Mingguan**: Menyimpan satu template mingguan dan menerapkannya selama durasi yang ditentukan pengguna (misalnya, 4, 8, 12 minggu)
- **Kalender Interaktif**: Lihat latihan harian, tandai sebagai selesai, dan lihat detailnya di modal
- **Pelacakan Penyelesaian Latihan**: Tandai latihan sebagai selesai dan lacak kemajuanmu
- **Dashboard Statistik**: Lihat streak saat ini, total latihan, dan grafik aktivitas 30 hari
- **Modal Detail Rencana**: Lihat timeline lengkap rencanamu dengan status penyelesaian untuk setiap hari
- **Responsif Mobile**: Desain yang sepenuhnya responsif dengan menu hamburger untuk perangkat mobile
- **Dashboard**: Kelola rencana aktif dan buat yang baru
- **Autentikasi Pengguna**: Sistem login dan registrasi yang aman

## Tech Stack

- **Frontend**:
  - HTML, JavaScript
  - **CSS Frameworks**:
    - Tailwind CSS (untuk dashboard & app pages)
    - Bootstrap 5.3.2 (custom build untuk landing page)
  - Chart.js (untuk visualisasi statistik)
- **Backend**: PHP (Native), Python (FastAPI untuk layanan AI)
- **Database**: MySQL
- **AI Model**: Google Gemini 2.5 Flash & Google Gemini 2.0 Flash

## Panduan Instalasi

### 1. Clone repository

```bash
git clone https://github.com/TangRmdhn/TrainHub.git
cd trainhub
```

### 2. Setup Database

- Import skema database:
  ```bash
  mysql -u root -p < trainhub_db.sql
  ```
- Update `koneksi.php` dengan kredensial databasemu:
  ```php
  $host = "localhost";
  $user = "root";
  $password = "password_kamu";
  $database = "trainhub_db";
  ```

### 3. Setup Layanan AI Python

#### Masuk ke direktori AI

```bash
cd AI
```

#### Buat Virtual Environment (Disarankan)

```bash
python -m venv .venv
```

#### Aktifkan Virtual Environment

**Di Windows (PowerShell):**

```powershell
.\.venv\Scripts\activate
```

**Jika muncul error execution policy di PowerShell**, jalankan perintah ini dulu:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

Lalu coba aktifkan lagi.

**Di Windows (Command Prompt):**

```cmd
.venv\Scripts\activate.bat
```

**Di macOS/Linux:**

```bash
source .venv/bin/activate
```

#### Install Dependencies Python

```bash
pip install -r requirements.txt
```

#### Konfigurasi API Key

Buat file `.env` di direktori `AI` dan tambahkan Google API Key kamu:

```
GOOGLE_API_KEY=api_key_kamu_disini
```

#### Jalankan Server FastAPI

```bash
uvicorn main:app --reload
```

Layanan AI akan tersedia di `http://localhost:8000`

### 4. Setup Web Server

- Host file PHP di server lokal (misalnya, XAMPP, Apache, Nginx)
- Pastikan web server berjalan di `http://localhost` atau domain pilihanmu
- Pastikan PHP dan MySQL sudah terkonfigurasi dengan benar

### 5. Setup Tailwind CSS

Proyek ini menggunakan Tailwind CSS. Kamu perlu membuild file CSS sebelum menjalankan aplikasi.

#### Opsi A: Menggunakan Standalone CLI (Tanpa Node.js)

1. Download executable Tailwind CLI:

   - **Windows**: [tailwindcss-windows-x64.exe](https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-windows-x64.exe)
   - **macOS**: [tailwindcss-macos-x64](https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-macos-x64)
   - **Linux**: [tailwindcss-linux-x64](https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-linux-x64)

2. Rename menjadi `tailwindcss.exe` (Windows) atau `tailwindcss` (macOS/Linux) dan taruh di root project

3. Build CSS:

   ```powershell
   # Windows
   .\tailwindcss.exe -i .\src\input.css -o .\views\css\tailwind.css --minify

   # macOS/Linux
   ./tailwindcss -i ./src/input.css -o ./views/css/tailwind.css --minify
   ```

   Atau gunakan script yang sudah disediakan:

   ```powershell
   # Windows
   .\build-css.ps1
   ```

#### Opsi B: Menggunakan npm (jika ada Node.js)

```bash
npm install -D tailwindcss
npx tailwindcss -i ./src/input.css -o ./views/css/tailwind.css --minify
```

#### Mode Development (Auto-rebuild saat ada perubahan)

```powershell
# Windows
.\watch-css.ps1

# Atau manual
.\tailwindcss.exe -i .\src\input.css -o .\views\css\tailwind.css --watch
```

### 6. Akses Aplikasi

- Buka browser dan pergi ke `http://localhost/trainhub`
- Daftar akun baru atau login

## Cara Penggunaan

1.  **Daftar/Login** ke aplikasi
2.  **Buat Rencana**: Pergi ke Dashboard dan klik "Generate New Plan"
3.  **Isi Detail**: Masukkan tujuan kebugaran, level, peralatan yang tersedia, dll.
4.  **Generate**: Klik "Generate Plan" dan tunggu AI membuatkan latihanmu
5.  **Simpan Rencana**: Review dan simpan rencana yang sudah dibuat
6.  **Lacak Progres**:
    - Lihat jadwalmu di halaman Calendar
    - Tandai latihan sebagai selesai
    - Cek statistik dan streak kamu
    - Lihat timeline detail rencana di halaman Plans

## Struktur Proyek

```
trainhub/
├── AI/                         # Layanan Python FastAPI
│   ├── main.py                # Aplikasi FastAPI
│   ├── requirements.txt       # Dependencies Python
│   └── .env                   # API keys (tidak ada di repo)
├── views/                      # Halaman Frontend
│   ├── css/                   # Stylesheets
│   │   ├── tailwind.css       # Tailwind CSS (untuk app pages)
│   │   └── bootstrap-landing.min.css # Bootstrap custom build (untuk landing page)
│   ├── app.php                # Halaman Dashboard
│   ├── calendar.php           # Tampilan Kalender
│   ├── plans.php              # Manajemen Rencana
│   ├── stats.php              # Halaman Statistik
│   ├── login.php              # Halaman Login
│   ├── register.php           # Halaman Registrasi
│   └── screening.php          # Form Screening User
├── controllers/                # Endpoint API Backend
│   ├── api_calendar.php       # API Kalender
│   ├── api_stats.php          # API Statistik
│   ├── mark_complete.php      # API Penyelesaian Latihan
│   ├── get_plan_details.php   # API Detail Rencana
│   ├── delete_plan.php        # API Hapus Rencana
│   ├── save_plan.php          # API Simpan Rencana
│   ├── update_plan_date.php   # API Update Tanggal Rencana
│   ├── login_controller.php   # Handler Login
│   ├── regist_controller.php  # Handler Registrasi
│   ├── screening_controller.php # Handler Screening
│   └── logout.php             # Handler Logout
├── index.php                   # Halaman Landing (Bootstrap CSS)
├── koneksi.php                 # Koneksi Database
├── koneksi.php.example         # Template config DB
├── trainhub_db.sql            # Skema Database
└── README.md                   # Dokumentasi
```

## Troubleshooting

### Error PowerShell Execution Policy

Jika muncul error seperti "cannot be loaded because running scripts is disabled", jalankan:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Masalah Koneksi Database

- Pastikan MySQL berjalan
- Cek kredensial di `koneksi.php`
- Pastikan database `trainhub_db` ada

### Layanan AI Tidak Merespon

- Pastikan server FastAPI berjalan di port 8000
- Cek apakah Google API key valid
- Pastikan semua dependencies Python sudah terinstall

## Lisensi

[MIT License](LICENSE)
