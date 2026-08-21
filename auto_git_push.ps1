# =====================================================================
# HIEU CEO - AUTO GIT WATCHER & REALTIME PUSH SYSTEM
# =====================================================================
# Tự động phát hiện mọi thay đổi code và đẩy lên GitHub ngay lập tức.
# Có cơ chế Debounce (chờ gom thay đổi) để tránh spam commit liên tục.
# =====================================================================

param (
    [int]$DebounceSeconds = 7,
    [string]$Remote = "origin",
    [string]$Branch = "main"
)

# Chuyển mã hóa UTF-8 cho console
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$OutputEncoding = [System.Text.Encoding]::UTF8

$RepoPath = $PSScriptRoot
if (-not $RepoPath) { $RepoPath = (Get-Location).Path }
Set-Location $RepoPath

Write-Host "=====================================================================" -ForegroundColor Cyan
Write-Host " 🚀 AUTO GIT SYNC - HIEU CEO DEV TOOLS" -ForegroundColor Yellow
Write-Host " 📂 Thư mục theo dõi: $RepoPath" -ForegroundColor White
Write-Host " 🌿 Nhánh đồng bộ   : $Remote / $Branch" -ForegroundColor White
Write-Host " ⏱️  Thời gian gom   : $DebounceSeconds giây sau khi dừng gõ/lưu file" -ForegroundColor White
Write-Host " 🛑 Nhấn Ctrl + C để dừng theo dõi bất kỳ lúc nào." -ForegroundColor DarkGray
Write-Host "=====================================================================" -ForegroundColor Cyan
Write-Host ""

# Hàm thực hiện commit và push
function Invoke-GitSync {
    param ([string]$Reason = "")
    
    try {
        $status = git status --porcelain
        if (-not $status) {
            return
        }

        $timestamp = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
        $changedCount = ($status -split "`n").Count
        
        Write-Host "`n[$timestamp] 📦 Phát hiện $changedCount tệp thay đổi. Đang xử lý đồng bộ..." -ForegroundColor Yellow
        
        # 1. Git add
        git add -A
        
        # 2. Tạo commit message chi tiết
        $commitMsg = "Auto sync [$timestamp] - Cập nhật $changedCount tệp"
        if ($Reason) {
            $commitMsg += " ($Reason)"
        }
        
        $commitResult = git commit -m "$commitMsg" 2>&1
        Write-Host "📝 Đã tạo commit: $commitMsg" -ForegroundColor DarkCyan
        
        # 3. Git push
        Write-Host "🚀 Đang đẩy lên GitHub ($Remote/$Branch)..." -ForegroundColor Cyan
        $pushResult = git push $Remote $Branch 2>&1
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ [$timestamp] ĐỒNG BỘ THÀNH CÔNG LÊN GITHUB!" -ForegroundColor Green
        } else {
            Write-Host "⚠️ Không thể push lên Git (Kiểm tra kết nối mạng hoặc quyền truy cập):" -ForegroundColor Red
            Write-Host $pushResult -ForegroundColor DarkRed
        }
        
        Write-Host "👀 Đang tiếp tục lắng nghe thay đổi..." -ForegroundColor DarkGray
    } catch {
        Write-Host "❌ Lỗi trong quá trình đồng bộ: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Kiểm tra thay đổi hiện tại trước khi bắt đầu lắng nghe
$initialStatus = git status --porcelain
if ($initialStatus) {
    Write-Host "🔍 Phát hiện có code chưa commit từ trước, đang đẩy lên trước..." -ForegroundColor Magenta
    Invoke-GitSync -Reason "Đồng bộ ban đầu khi khởi động"
}

# Khởi tạo FileSystemWatcher
$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $RepoPath
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true
$watcher.NotifyFilter = [System.IO.NotifyFilters]'FileName, LastWrite, DirectoryName'

# Danh sách bỏ qua
$ignoredPatterns = @(
    '\.git',
    '\.vscode',
    '\.idea',
    'node_modules',
    'vendor',
    '(\.tmp|\.temp|\.swp|\.log)$',
    '~$',
    'auto_git_push'
)

$script:lastChangeTime = [DateTime]::MinValue
$script:pendingChange = $false
$script:lastChangedFile = ""

# Đăng ký sự kiện
$action = {
    param($source, $eventArgs)
    $path = $eventArgs.FullPath
    $changeType = $eventArgs.ChangeType
    
    # Kiểm tra xem có nằm trong danh sách bỏ qua không
    foreach ($pattern in $ignoredPatterns) {
        if ($path -match $pattern) {
            return
        }
    }
    
    $relPath = $path.Replace($RepoPath, "").TrimStart('\', '/')
    $script:lastChangeTime = [DateTime]::Now
    $script:pendingChange = $true
    $script:lastChangedFile = "$relPath ($changeType)"
    
    Write-Host "🔔 [$((Get-Date).ToString('HH:mm:ss'))] Phát hiện: $relPath [$changeType]" -ForegroundColor DarkYellow
}

$handlers = @(
    Register-ObjectEvent $watcher 'Changed' -Action $action,
    Register-ObjectEvent $watcher 'Created' -Action $action,
    Register-ObjectEvent $watcher 'Deleted' -Action $action,
    Register-ObjectEvent $watcher 'Renamed' -Action $action
)

Write-Host "🟢 [ĐANG HOẠT ĐỘNG] Trình theo dõi tự động đã sẵn sàng. Hãy thoải mái code và lưu file!" -ForegroundColor Green
Write-Host ""

try {
    while ($true) {
        Start-Sleep -Milliseconds 500
        
        if ($script:pendingChange) {
            $elapsed = ([DateTime]::Now - $script:lastChangeTime).TotalSeconds
            if ($elapsed -ge $DebounceSeconds) {
                $script:pendingChange = $false
                Invoke-GitSync -Reason $script:lastChangedFile
            }
        }
    }
} finally {
    # Dọn dẹp sự kiện khi thoát
    $watcher.EnableRaisingEvents = $false
    $watcher.Dispose()
    foreach ($h in $handlers) {
        Unregister-Event -SourceIdentifier $h.Name -ErrorAction SilentlyContinue
    }
    Write-Host "`n🛑 Đã tắt trình theo dõi tự động Git." -ForegroundColor Yellow
}
