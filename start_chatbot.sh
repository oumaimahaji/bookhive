#!/bin/bash

echo " Setting up BookHive Chatbot..."
echo ""

# Check if Python is installed
if ! command -v python3 &> /dev/null && ! command -v python &> /dev/null; then
    echo " Python is not installed. Please install Python 3.7 or higher."
    exit 1
fi

# Use python3 if available, otherwise python
PYTHON_CMD="python"
if command -v python3 &> /dev/null; then
    PYTHON_CMD="python3"
fi

echo " Python found: $PYTHON_CMD"

# Install dependencies
echo ""
echo " Installing Python dependencies..."
$PYTHON_CMD -m pip install -r requirements.txt

if [ $? -eq 0 ]; then
    echo " Dependencies installed successfully!"
else
    echo " Failed to install dependencies. Please check your internet connection."
    exit 1
fi

echo ""
echo " Starting BookHive Chatbot Server..."
echo " Server will be available at: http://127.0.0.1:8001"
echo " Chat interface at: http://127.0.0.1:8000/chat"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start the Flask server
$PYTHON_CMD chatbot.py