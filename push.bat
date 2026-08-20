@echo off
setlocal enabledelayedexpansion

echo =========================================================
echo   HIEU CEO - AUTO GIT PUSH PIPELINE
echo =========================================================
echo.

set MSG=%~1
if "%MSG%"=="" set MSG=update

echo [1/3] Adding changes (git add .)...
git add .
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Git add failed!
    exit /b %ERRORLEVEL%
)

echo [2/3] Committing: "%MSG%"...
git commit -m "%MSG%"

echo [3/3] Pushing to GitHub (git push origin main)...
git push origin main

if %ERRORLEVEL% equ 0 (
    echo.
    echo =========================================================
    echo   [SUCCESS] CODE PUSHED TO GITHUB SUCCESSFULLY!
    echo   Repo: https://github.com/tranvanminhhieu06-gif/HieuDoAn
    echo =========================================================
) else (
    echo [ERROR] Git push failed!
)
echo.
