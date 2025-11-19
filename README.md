# TrainHub - AI-Powered Workout Planner

TrainHub is a web application that helps users generate personalized weekly workout plans using AI (Google Gemini). It features a dashboard for plan management, a calendar view for tracking workouts, statistics tracking, and an AI-powered plan generator.

## Features

- **AI Workout Generation**: Generates personalized 7-day workout templates based on user profile (fitness goal, level, equipment, etc.)
- **Weekly Template Model**: Stores a single weekly template and applies it over a user-defined duration (e.g., 4, 8, 12 weeks)
- **Interactive Calendar**: View daily workouts, mark them as completed, and see details in a modal
- **Workout Completion Tracking**: Mark workouts as complete and track your progress
- **Statistics Dashboard**: View your current streak, total workouts, and 30-day activity chart
- **Plan Details Modal**: See a full timeline of your plan with completion status for each day
- **Mobile Responsive**: Fully responsive design with hamburger menu for mobile devices
- **Dashboard**: Manage active plans and create new ones
- **User Authentication**: Secure login and registration system

## Tech Stack

- **Frontend**: HTML, Tailwind CSS, JavaScript, Chart.js
- **Backend**: PHP (Native), Python (FastAPI for AI service)
- **Database**: MySQL
- **AI Model**: Google Gemini 2.0 Flash

## Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/TangRmdhn/Praktikum-Web.git
cd trainhub
```

### 2. Database Setup

- Import the database schema:
  ```bash
  mysql -u root -p < trainhub_db.sql
  ```
- Update `koneksi.php` with your database credentials:
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

**On Windows (PowerShell):**

```powershell
.\.venv\Scripts\activate
```

**If you encounter a PowerShell execution policy error**, run this command first:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

Then try activating again.

**On Windows (Command Prompt):**

```cmd
.venv\Scripts\activate.bat
```

**On macOS/Linux:**

```bash
source .venv/bin/activate
```

#### Install Python Dependencies

```bash
pip install -r requirements.txt
```

#### Configure API Key

Create a `.env` file in the `AI` directory and add your Google API Key:

```
GOOGLE_API_KEY=your_api_key_here
```

#### Run the FastAPI Server

```bash
uvicorn main:app --reload
```

The AI service will be available at `http://localhost:8000`

### 4. Web Server Setup

- Host the PHP files on a local server (e.g., XAMPP, Apache, Nginx)
- Ensure the web server is running on `http://localhost` or your preferred domain
- Make sure PHP and MySQL are properly configured

### 5. Access the Application

- Open your browser and navigate to `http://localhost/trainhub`
- Register a new account or login

## Usage

1.  **Register/Login** to the application
2.  **Create a Plan**: Go to the Dashboard and click "Generate New Plan"
3.  **Fill in Details**: Enter your fitness goal, level, available equipment, etc.
4.  **Generate**: Click "Generate Plan" and wait for the AI to create your workout
5.  **Save Plan**: Review and save the generated plan
6.  **Track Progress**:
    - View your schedule on the Calendar page
    - Mark workouts as complete
    - Check your statistics and streaks
    - View detailed plan timeline in the Plans page

## Project Structure

```
trainhub/
├── AI/                         # Python FastAPI service
│   ├── main.py                # FastAPI application
│   ├── requirements.txt       # Python dependencies
│   └── .env                   # API keys (not in repo)
├── views/                      # Frontend pages
│   ├── css/                   # Stylesheets
│   │   └── style.css
│   ├── app.php                # Dashboard page
│   ├── calendar.php           # Calendar view
│   ├── plans.php              # Plans management
│   ├── stats.php              # Statistics page
│   ├── login.php              # Login page
│   ├── register.php           # Registration page
│   └── screening.php          # User screening form
├── controllers/                # Backend API endpoints
│   ├── api_calendar.php       # Calendar API
│   ├── api_stats.php          # Statistics API
│   ├── mark_complete.php      # Workout completion API
│   ├── get_plan_details.php   # Plan details API
│   ├── delete_plan.php        # Delete plan API
│   ├── save_plan.php          # Save plan API
│   ├── update_plan_date.php   # Update plan dates API
│   ├── login_controller.php   # Login handler
│   ├── regist_controller.php  # Registration handler
│   ├── screening_controller.php # Screening handler
│   └── logout.php             # Logout handler
├── index.php                   # Landing page
├── koneksi.php                 # Database connection
├── koneksi.php.example         # DB config template
├── trainhub_db.sql            # Database schema
└── README.md                   # Documentation
```

## Troubleshooting

### PowerShell Execution Policy Error

If you get an error like "cannot be loaded because running scripts is disabled", run:

```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Database Connection Issues

- Verify MySQL is running
- Check credentials in `koneksi.php`
- Ensure the database `trainhub_db` exists

### AI Service Not Responding

- Verify the FastAPI server is running on port 8000
- Check that your Google API key is valid
- Ensure all Python dependencies are installed

## License

[MIT License](LICENSE)
