# Deployment Guide for VetDict Backend

This guide will help you deploy the VetDict backend to Railway.

## Prerequisites

1. Node.js and npm installed
2. Git installed
3. Railway CLI installed (`npm install -g @railway/cli`)
4. Railway account (https://railway.app)

## Deployment Steps

1. **Login to Railway**
   ```bash
   railway login
   ```

2. **Run the deployment script**
   ```bash
   chmod +x deploy-railway.sh
   ./deploy-railway.sh
   ```

3. **Set up your domain** (pawshine.net)
   - Go to your Railway project dashboard
   - Navigate to Settings > Domains
   - Add your domain (pawshine.net)
   - Update your DNS settings to point to the provided Railway DNS records

## Environment Variables

The following environment variables will be set automatically:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (auto-generated)
- `DB_CONNECTION=sqlite`
- `SESSION_DRIVER=database`
- `QUEUE_CONNECTION=database`

## Database

This application uses SQLite by default. For production, you might want to switch to a more robust database like PostgreSQL or MySQL. You can add a database service from the Railway dashboard and update the `DB_CONNECTION` environment variable accordingly.

## Monitoring

Monitor your application from the Railway dashboard. You can view logs, set up alerts, and monitor resource usage.

## Updating the Application

To deploy updates:

1. Push your changes to the repository
2. Railway will automatically detect and deploy the changes

## Support

If you encounter any issues, please check the Railway dashboard logs or contact support.
