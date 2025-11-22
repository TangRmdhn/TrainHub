# Tailwind CSS Watch Mode
# This will automatically rebuild CSS when you save changes

Write-Host "Watching for changes..." -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop" -ForegroundColor Yellow
.\tailwindcss.exe -i .\src\input.css -o .\views\css\tailwind.css --watch
