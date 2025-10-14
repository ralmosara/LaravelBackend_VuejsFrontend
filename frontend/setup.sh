#!/bin/bash

echo "Setting up Vue.js Frontend..."

# Install dependencies
echo "Installing npm dependencies..."
npm install

# Copy env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

echo "✓ Frontend setup complete!"
echo "Run 'npm run dev' to start the development server"
