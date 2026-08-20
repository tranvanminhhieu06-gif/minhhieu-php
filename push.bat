@echo off
chcp 65001 >nul
title HIEU CEO - Auto Git Push Pipeline

echo =========================================================
echo   👑 HIEU CEO - QUY TRINH TU DONG DAY CODE LEN GIT 👑
echo =========================================================
echo.

set MSG=%~1
if "%MSG%"=="" set MSG=update

echo [1/3] Dang them tep thay doi (git add .)...
git add .
if %ERRORLEVEL% neq 0 (
    echo [LOI] Khong the them tep tin!
    pause
    exit /b %ERRORLEVEL%
)

echo [2/3] Dang commit: "%MSG%"...
git commit -m "%MSG%"

echo [3/3] Dang day code len GitHub (git push origin main)...
git push origin main

if %ERRORLEVEL% equ 0 (
    echo.
    echo =========================================================
    echo   [THANH CONG] DA DAY TOAN BO CODE LEN GITHUB CHUAN CEO!
    echo   Repo: https://github.com/tranvanminhhieu06-gif/HieuDoAn
    echo =========================================================
) else (
    echo [LOI] Khong the push code len GitHub!
)

echo.
