#!/bin/bash

# Asset Deployment Script for cPanel
# This script syncs local assets to the CDN/asset domain

# Configuration
LOCAL_PUBLIC_PATH="./public"
REMOTE_ASSET_PATH="/home/username/assets.massagerepublic.co/public_html"
REMOTE_HOST="your-server.com"
REMOTE_USER="your-username"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}🚀 Starting Asset Deployment...${NC}"

# Check if local public directory exists
if [ ! -d "$LOCAL_PUBLIC_PATH" ]; then
    echo -e "${RED}❌ Local public directory not found: $LOCAL_PUBLIC_PATH${NC}"
    exit 1
fi

# Create version directory
VERSION=$(date +"%Y%m%d_%H%M%S")
echo -e "${YELLOW}📦 Creating version: $VERSION${NC}"

# Sync assets to remote server
echo -e "${YELLOW}📤 Syncing assets to remote server...${NC}"

# Using rsync to sync files
rsync -avz --delete \
    --exclude='*.php' \
    --exclude='index.php' \
    --exclude='.htaccess' \
    --exclude='web.config' \
    "$LOCAL_PUBLIC_PATH/" \
    "$REMOTE_USER@$REMOTE_HOST:$REMOTE_ASSET_PATH/$VERSION/"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Assets synced successfully!${NC}"
    
    # Create/update symlink to latest version
    ssh "$REMOTE_USER@$REMOTE_HOST" "cd $REMOTE_ASSET_PATH && ln -sfn $VERSION latest"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Symlink updated to latest version${NC}"
    else
        echo -e "${RED}❌ Failed to update symlink${NC}"
    fi
    
    # Update environment file
    echo -e "${YELLOW}📝 Updating environment configuration...${NC}"
    echo "ASSET_VERSION=$VERSION" >> .env.production
    
    echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
    echo -e "${GREEN}Asset URL: https://assets.massagerepublic.co/$VERSION/${NC}"
    
else
    echo -e "${RED}❌ Failed to sync assets${NC}"
    exit 1
fi

# Optional: Purge CDN cache if using CloudFlare or similar
# curl -X POST "https://api.cloudflare.com/client/v4/zones/YOUR_ZONE_ID/purge_cache" \
#      -H "Authorization: Bearer YOUR_API_TOKEN" \
#      -H "Content-Type: application/json" \
#      --data '{"purge_everything":true}'
