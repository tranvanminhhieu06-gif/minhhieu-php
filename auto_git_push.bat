@echo off
chcp 65001 >nul
title HIEU CEO - Realtime Auto Git Push
cls
echo ============================================================
echo   🚀 HIEU CEO - DICH VU TU DONG DONG BO GIT (AUTO PUSH)
echo ============================================================
echo   Moi khi ban luu file code, he thong se tu dong gom va day
echo   len GitHub sau 5-7 giay.
echo.
echo   [!] De tat, chi can dong cua so nay hoac an Ctrl + C.
echo ============================================================
echo.

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0auto_git_push.ps1"

pause
