@echo off
echo  Setting up BookHive Chatbot...
echo.

REM Check if Python is installed
python --version >nul 2>&1
if %errorlevel% neq 0 (
    python3 --version >nul 2>&1
    if %errorlevel% neq 0 (
        echo  Python is not installed. Please install Python 3.7 or higher.
        pause
        exit /b 1
    ) else (
        set PYTHON_CMD=python3
    )
) else (
    set PYTHON_CMD=python
)

echo  Python found: %PYTHON_CMD%

echo.
echo  Installing Python dependencies...
%PYTHON_CMD% -m pip install -r requirements.txt

if %errorlevel% neq 0 (
    echo  Failed to install dependencies. Please check your internet connection.
    pause
    exit /b 1
)

echo  Dependencies installed successfully!

echo.
echo  Starting BookHive Chatbot Server...
echo  Server will be available at: http://127.0.0.1:8001
echo  Chat interface at: http://127.0.0.1:8000/chat
echo.
echo Press Ctrl+C to stop the server
echo.

REM Start the Flask server
%PYTHON_CMD% chatbot.py