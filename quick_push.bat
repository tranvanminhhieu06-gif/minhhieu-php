@echo off
chcp 65001 >nul
title HIEU CEO - Quick Git Push
cls
echo ============================================================
echo   🚀 HIEU CEO - DAY CODE NHANH LEN GITHUB (QUICK PUSH)
echo ============================================================
echo.

set /p msg="Nhap ghi chu commit (De trong se tu dong lay thoi gian hien tai): "
if "%msg%"=="" (
    for /f "tokens=1-4 delims=/ " %%a in ('date /t') do (set mydate=%%c-%%a-%%b)
    for /f "tokens=1-2 delims=: " %%a in ('time /t') do (set mytime=%%a:%%b)
    set msg=Manual push [%%DATE%% %%TIME%%]
)

echo.
echo [1/3] Dang them tat ca cac tep thay doi...
git add -A

echo [2/3] Dang tao commit: "%msg%"...
git commit -m "%msg%"

echo [3/3] Dang day len GitHub (origin/main)...
git push origin main

echo.
if %ERRORLEVEL% equ 0 (
    echo ============================================================
    echo   ✅ DA DAY CODE LEN GITHUB THANH CONG!
    echo ============================================================
) else (
    echo ============================================================
    echo   ❌ CO LOI XAY RA TRONG QUA TRINH PUSH.
    echo ============================================================
)

echo.
pause
