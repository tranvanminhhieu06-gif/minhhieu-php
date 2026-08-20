# HIEU CEO - PowerShell Automated Git Sync & Push Workflow
param (
    [string]$Message = ""
)

[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "  HIEU CEO - AUTO GIT SYNC & PUSH WORKFLOW" -ForegroundColor Yellow
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host ""

if ([string]::IsNullOrWhiteSpace($Message)) {
    $now = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $Message = "update: $now"
}

Write-Host "[1/3] Dang them cac tep thay doi (git add .)..." -ForegroundColor Cyan
git add .
Write-Host "[OK] Da add tat ca tep thanh cong!" -ForegroundColor Green
Write-Host ""

Write-Host "[2/3] Dang tao commit: $Message..." -ForegroundColor Cyan
git commit -m "$Message"
Write-Host ""

Write-Host "[3/3] Dang day len GitHub (git push origin main)..." -ForegroundColor Cyan
git push origin main

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================================" -ForegroundColor Green
    Write-Host "  HOAN TAT TU DONG DAY CODE LEN GITHUB & RENDER!" -ForegroundColor Green
    Write-Host "========================================================" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "[ERROR] Day code that bai! Kiem tra lai ket noi hoac quyen truy cap." -ForegroundColor Red
}
