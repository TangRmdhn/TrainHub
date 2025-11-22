# Tailwind CSS Build Script
# Run this whenever you make changes to your HTML/PHP files

Write-Host "Building Tailwind CSS..." -ForegroundColor Cyan
.\tailwindcss.exe -i .\src\input.css -o .\views\css\tailwind.css
Write-Host "Build complete!" -ForegroundColor Green
