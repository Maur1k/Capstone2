@echo off
echo ==========================================
echo    Click2Serve Thermal Printer Service
echo ==========================================
echo.
echo Starting thermal printer service...
echo Printer: OC-PT210 (Officom)
echo Port: 4000
echo.
echo Make sure your thermal printer is connected via USB!
echo.
cd /d "%~dp0"
node server.js
pause

