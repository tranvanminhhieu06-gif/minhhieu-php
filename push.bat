@echo off
chcp 65001 > nul
setlocal enabledelayedexpansion

echo ========================================================
echo   👑 HIEU CEO - AUTO GIT SYNC & PUSH WORKFLOW 👑
echo ========================================================
echo.

:: Get commit message from argument or default to "update" with timestamp
set "MSG=%~1"
if "%MSG%"=="" (
    for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
    set "DATE_STR=!datetime:~0,4!-!datetime:~4,2!-!datetime:~6,2! !datetime:~8,2!:!datetime:~10,2!"
    set "MSG=update: !DATE_STR!"
)

echo [1/3] Đang thêm các tệp thay đổi (git add .)...
git add .
if errorlevel 1 (
    echo [ERROR] Lỗi khi thêm tệp vào git staging!
    pause
    exit /b 1
)
echo [OK] Đã add tất cả tệp thành công!
echo.

echo [2/3] Đang tạo commit với thông điệp: "%MSG%"...
git commit -m "%MSG%"
if errorlevel 1 (
    echo [INFO] Không có thay đổi mới nào cần commit hoặc đã commit trước đó.
) else (
    echo [OK] Đã commit thành công!
)
echo.

echo [3/3] Đang đẩy lên GitHub (git push origin main)...
git push origin main
if errorlevel 1 (
    echo.
    echo [ERROR] Lỗi khi đẩy lên GitHub! Hãy kiểm tra kết nối mạng hoặc phân quyền.
    pause
    exit /b 1
)

echo.
echo ========================================================
echo   🎉 HOÀN TẤT TỰ ĐỘNG ĐẨY CODE LÊN GITHUB THÀNH CÔNG!
echo ========================================================
echo.
timeout /t 3 > nul
