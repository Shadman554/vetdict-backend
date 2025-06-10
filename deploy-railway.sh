#!/bin/bash

# Exit on error
set -e

# Install Railway CLI if not installed
if ! command -v railway &> /dev/null; then
    echo "Installing Railway CLI..."
    npm install -g @railway/cli
fi

# Login to Railway if not already logged in
if ! railway status &> /dev/null; then
    echo "Please log in to Railway..."
    railway login
fi

# Create a new Railway project or use existing one
echo "Creating/Updating Railway project..."
railway link --name vetdict-backend || true

# Set environment variables
echo "Setting environment variables..."
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_KEY=$(php artisan key:generate --show)
railway variables set DB_CONNECTION=sqlite
railway variables set SESSION_DRIVER=database
railway variables set QUEUE_CONNECTION=database

# Deploy to Railway
echo "Deploying to Railway..."
railway up --detach

echo "Deployment initiated! Check the Railway dashboard for progress."
echo "Once deployed, your app will be available at: https://vetdict-backend.up.railway.app"
echo "You can connect your domain pawshine.net from the Railway dashboard."
