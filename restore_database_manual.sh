#!/bin/bash

# Manual restore script dengan path MAMP
# Usage: ./restore_database_manual.sh

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${YELLOW}=== Database Restore (Manual) ===${NC}\n"

# Try to find MySQL
MYSQL_PATH=""
if [ -f "/Applications/MAMP/Library/bin/mysql" ]; then
    MYSQL_PATH="/Applications/MAMP/Library/bin/mysql"
elif [ -f "/usr/local/bin/mysql" ]; then
    MYSQL_PATH="/usr/local/bin/mysql"
elif command -v mysql &> /dev/null; then
    MYSQL_PATH="mysql"
else
    echo -e "${RED}Error: MySQL not found!${NC}"
    echo "Please install MySQL or provide path manually."
    exit 1
fi

echo -e "${GREEN}Using MySQL: $MYSQL_PATH${NC}\n"

# Database config
DB_HOST="127.0.0.1"
DB_PORT="8889"
DB_DATABASE="db_samsae_new"
DB_USERNAME="root"
DB_PASSWORD="root"
BACKUP_FILE="db_samsae_new_2025-12-18.sql"

echo -e "${YELLOW}Configuration:${NC}"
echo "  Host: $DB_HOST"
echo "  Port: $DB_PORT"
echo "  Database: $DB_DATABASE"
echo "  Backup: $BACKUP_FILE"
echo ""

# Create database
echo -e "${YELLOW}Creating database...${NC}"
$MYSQL_PATH -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database created/verified${NC}\n"
else
    echo -e "${RED}✗ Failed to create database${NC}"
    exit 1
fi

# Restore database
echo -e "${YELLOW}Restoring database from backup...${NC}"
echo "This may take a few minutes..."
$MYSQL_PATH -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$BACKUP_FILE" 2>&1

if [ $? -eq 0 ]; then
    echo -e "\n${GREEN}✓ Database restored successfully!${NC}\n"
    
    # Verify
    echo -e "${YELLOW}Verifying restore...${NC}"
    TABLE_COUNT=$($MYSQL_PATH -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLES;" 2>/dev/null | wc -l | tr -d ' ')
    echo -e "${GREEN}✓ Found $TABLE_COUNT tables${NC}\n"
    
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Run: php artisan migrate"
    echo "2. Run: php artisan config:clear && php artisan cache:clear"
    echo "3. Verify data: php artisan tinker"
else
    echo -e "\n${RED}✗ Restore failed!${NC}"
    exit 1
fi
