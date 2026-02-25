# Generate a patch of current changes using git
# Usage: run from repository root in PowerShell

if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    Write-Host "git is not installed or not in PATH. Install Git and rerun this script." -ForegroundColor Yellow
    exit 1
}

$patchFile = Join-Path (Get-Location) 'changes.patch'
Write-Host "Generating patch to $patchFile"

git diff HEAD > $patchFile
if ($LASTEXITCODE -eq 0) {
    Write-Host "Patch written to $patchFile" -ForegroundColor Green
} else {
    Write-Host "git diff failed with exit code $LASTEXITCODE" -ForegroundColor Red
}
