@echo off
REM Asset Deployment Script for Windows/cPanel
REM This script syncs local assets to the CDN/asset domain

echo 🚀 Starting Asset Deployment...

REM Configuration
set LOCAL_PUBLIC_PATH=./public
set VERSION=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set VERSION=%VERSION: =0%

echo 📦 Creating version: %VERSION%

REM Check if WinSCP or similar tool is available
where winscp.com >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ❌ WinSCP not found. Please install WinSCP for file transfer.
    echo Alternative: Use FileZilla or cPanel File Manager
    pause
    exit /b 1
)

REM Create WinSCP script for deployment
echo Creating deployment script...
(
echo open sftp://your-username@your-server.com
echo put %LOCAL_PUBLIC_PATH%\assets\* /home/username/assets.massagerepublic.co/public_html/%VERSION%/assets/
echo put %LOCAL_PUBLIC_PATH%\css\* /home/username/assets.massagerepublic.co/public_html/%VERSION%/css/
echo put %LOCAL_PUBLIC_PATH%\js\* /home/username/assets.massagerepublic.co/public_html/%VERSION%/js/
echo put %LOCAL_PUBLIC_PATH%\images\* /home/username/assets.massagerepublic.co/public_html/%VERSION%/images/
echo exit
) > deploy-script.txt

REM Execute the deployment
winscp.com /script=deploy-script.txt

if %ERRORLEVEL% EQU 0 (
    echo ✅ Assets deployed successfully!
    echo 📝 Updating environment configuration...
    echo ASSET_VERSION=%VERSION% >> .env.production
    echo 🎉 Deployment completed!
    echo Asset URL: https://assets.massagerepublic.co/%VERSION%/
) else (
    echo ❌ Deployment failed
)

REM Cleanup
del deploy-script.txt

pause
