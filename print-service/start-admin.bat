@echo off
echo ==========================================
echo    Click2Serve Thermal Printer Service
echo ==========================================
echo.
echo Starting thermal printer service as Administrator...
echo Printer: OC-PT210 (Officom)
echo Port: 4000
echo.
echo Make sure your thermal printer is connected via USB!
echo.
echo Press any key to start the service...
pause
cd /d "%~dp0"
echo.
echo Starting Node.js print service as Administrator...
powershell -Command "Start-Process node -ArgumentList 'server.js' -Verb RunAs -WindowStyle Normal"
echo.
echo Print service started! Check the new window for status.
echo Press any key to exit...
pause
