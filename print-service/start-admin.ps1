# PowerShell script to start print service as Administrator
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "    Click2Serve Thermal Printer Service" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Starting thermal printer service as Administrator..." -ForegroundColor Yellow
Write-Host "Printer: OC-PT210 (Officom)" -ForegroundColor Green
Write-Host "Port: 4000" -ForegroundColor Green
Write-Host ""
Write-Host "Make sure your thermal printer is connected via USB!" -ForegroundColor Red
Write-Host ""

# Check if running as Administrator
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "This script requires Administrator privileges!" -ForegroundColor Red
    Write-Host "Restarting as Administrator..." -ForegroundColor Yellow
    
    # Restart as Administrator
    Start-Process powershell -ArgumentList "-ExecutionPolicy Bypass -File `"$PSCommandPath`"" -Verb RunAs
    exit
}

Write-Host "Running as Administrator ✓" -ForegroundColor Green
Write-Host ""

# Navigate to the script directory
$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Definition
Set-Location $scriptPath

Write-Host "Current directory: $PWD" -ForegroundColor Blue
Write-Host ""

# Start the print service
Write-Host "Starting Node.js print service..." -ForegroundColor Yellow
node server.js
