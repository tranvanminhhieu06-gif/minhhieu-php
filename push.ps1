param (
    [string]$msg = "update"
)

Write-Host ""
Write-Host "=========================================================" -ForegroundColor Cyan
Write-Host "  HIEU CEO - AUTO GIT COMMIT & PUSH PIPELINE" -ForegroundColor Yellow
Write-Host "=========================================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/3] Adding changes (git add .)..." -ForegroundColor Blue
git add .
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error during git add!" -ForegroundColor Red
    exit 1
}
Write-Host "  -> Done adding files." -ForegroundColor Green

$status = git status --porcelain
if (-not $status) {
    Write-Host "[INFO] No local changes to commit. Checking push..." -ForegroundColor Yellow
} else {
    Write-Host "[2/3] Committing with message: '$msg'..." -ForegroundColor Blue
    git commit -m "$msg"
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Error during git commit!" -ForegroundColor Red
        exit 1
    }
    Write-Host "  -> Done commit." -ForegroundColor Green
}

Write-Host "[3/3] Pushing to GitHub (git push origin main)..." -ForegroundColor Blue
git push -u origin main
if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "=========================================================" -ForegroundColor Green
    Write-Host "  SUCCESS: CODE PUSHED TO GITHUB (100%)" -ForegroundColor Green
    Write-Host "  Repo: https://github.com/tranvanminhhieu06-gif/minhhieu-php" -ForegroundColor Yellow
    Write-Host "=========================================================" -ForegroundColor Green
    Write-Host ""
} else {
    Write-Host "Error during git push!" -ForegroundColor Red
    exit 1
}
