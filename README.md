<div align="center">

# TrainHub - AI-Powered Workout Planner

**Personalized Workout Plans Powered by Google Gemini AI**

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Python](https://img.shields.io/badge/Python-3776AB?style=for-the-badge&logo=python&logoColor=white)](https://www.python.org/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Google Gemini](https://img.shields.io/badge/Google%20Gemini-8E75B2?style=for-the-badge&logo=google&logoColor=white)](https://deepmind.google/technologies/gemini/)

[Live Demo](https://trainhub.web.id) • [Report Bug](https://github.com/TangRmdhn/TrainHub/issues) • [Request Feature](https://github.com/TangRmdhn/TrainHub/issues)

</div>

---

## About The Project

**TrainHub** is a web application designed to help users create personalized weekly workout plans using the power of AI (Google Gemini). It features a comprehensive dashboard for managing plans, an interactive calendar for tracking workouts, detailed statistics, and an AI-powered plan generator.

## Key Features

- **AI Workout Generator**: Create personalized 7-day workout templates based on your profile (goals, level, equipment).
- **AI Coach Chatbot**: Interactive chat with an AI personal trainer for advice on workouts, nutrition, and motivation.
- **Weekly Template Model**: Save a weekly template and apply it for a user-defined duration (e.g., 4, 8, 12 weeks).
- **Interactive Calendar**: View daily workouts, mark them as complete, and see details in a modal.
- **Workout Tracking**: Track your progress by marking workouts as done.
- **Stats Dashboard**: Monitor your current streak, total workouts, and 30-day activity graph.
- **Mobile Responsive**: Fully responsive design for seamless use on all devices.
- **Secure Authentication**: Robust login and registration system.

## Tech Stack

### Frontend

- **HTML5 & JavaScript**
- **Tailwind CSS** (Dashboard & App Pages)
- **Bootstrap 5.3.2** (Landing Page)
- **Chart.js** (Statistics Visualization)

### Backend

- **PHP** (Native)
- **Python** (FastAPI for AI Services)

### Database

- **MySQL**

### AI Model

- **Google Gemini 2.5 Flash & Google Gemini 2.0 Flash**

## Getting Started

### 1. Clone Repository

```bash
git clone https://github.com/TangRmdhn/TrainHub.git
cd trainhub
```

### 2. Database Setup

- Import the database schema:
  ```bash
  mysql -u root -p < trainhub_db.sql
  ```
- Update `koneksi.php` with your credentials:
  ```php
  $host = "localhost";
  $user = "root";
  $password = "your_password";
  $database = "trainhub_db";
  ```

### 3. Python AI Service Setup

#### Navigate to AI directory

```bash
cd AI
```

#### Create Virtual Environment (Recommended)

```bash
python -m venv .venv
```

#### Activate Virtual Environment

- **Windows (PowerShell):**
  ```powershell
  .\.venv\Scripts\activate
  ```
- **Windows (CMD):**
  ```cmd
  .venv\Scripts\activate.bat
  ```
- **macOS/Linux:**
  ```bash
  source .venv/bin/activate
  ```

#### Install Dependencies

```bash
pip install -r requirements.txt
```

#### Configure API Key

Create a `.env` file in the `AI` directory:

```env
GOOGLE_API_KEY=your_api_key_here
```

#### Run FastAPI Server

```bash
uvicorn main:app --reload
```

The AI service will run at `http://localhost:8000`.

### 4. Web Server Setup

- Host PHP files on a local server (XAMPP, Apache, Nginx).
- Ensure the server runs on `http://localhost`.

### 5. Tailwind CSS Setup

Build the CSS before running the app.

#### Option A: Standalone CLI

```powershell
# Windows
.\tailwindcss.exe -i .\src\input.css -o .\views\css\tailwind.css --minify
```

#### Option B: npm

```bash
npm install -D tailwindcss
npx tailwindcss -i ./src/input.css -o ./views/css/tailwind.css --minify
```

### 6. Access the App

Open `http://localhost/trainhub` in your browser.

## Project Structure

```
trainhub/
├── AI/                         # Python FastAPI Service
├── views/                      # Frontend Views
│   ├── css/                    # Stylesheets
│   ├── app.php                 # Dashboard
│   └── ...
├── controllers/                # Backend Logic
├── index.php                   # Landing Page
├── config.php                  # Configuration
├── koneksi.php                 # Database Connection
├── trainhub_db.sql             # Database Schema
└── README.md                   # Documentation
```

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

## License

Distributed under the GNU General Public License v3.0. See `LICENSE` for more information.
