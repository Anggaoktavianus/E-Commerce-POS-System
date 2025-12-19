#!/bin/bash

# Script untuk restore database dari backup SQL
# Usage: ./restore_database.sh [backup_file.sql]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Database Restore Script ===${NC}\n"

# Get database config from .env
if [ ! -f .env ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    exit 1
fi

# Load .env variables
export $(grep -v '^#' .env | grep -E '^DB_' | xargs)

DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-samsae}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

# Check if backup file is provided
BACKUP_FILE=${1:-db_samsae_new_2025-12-12.sql}

if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}Error: Backup file '$BACKUP_FILE' not found!${NC}"
    echo "Available SQL files:"
    ls -lh *.sql 2>/dev/null || echo "No SQL files found"
    exit 1
fi

echo -e "${YELLOW}Database Configuration:${NC}"
echo "  Host: $DB_HOST"
echo "  Port: $DB_PORT"
echo "  Database: $DB_DATABASE"
echo "  Username: $DB_USERNAME"
echo "  Backup File: $BACKUP_FILE"
echo ""

# Confirm before proceeding
read -p "Are you sure you want to restore database '$DB_DATABASE'? This will REPLACE all existing data! (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo -e "${YELLOW}Restore cancelled.${NC}"
    exit 0
fi

echo -e "\n${YELLOW}Starting restore process...${NC}"

# Create database if not exists
echo -e "${YELLOW}Creating database if not exists...${NC}"
if [ -z "$DB_PASSWORD" ]; then
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1
else
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1
fi

# Restore database
echo -e "${YELLOW}Restoring database from backup...${NC}"
if [ -z "$DB_PASSWORD" ]; then
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE" < "$BACKUP_FILE" 2>&1
else
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$BACKUP_FILE" 2>&1
fi

if [ $? -eq 0 ]; then
    echo -e "\n${GREEN}✓ Database restored successfully!${NC}"
    echo -e "${GREEN}Database: $DB_DATABASE${NC}"
    echo -e "${GREEN}Backup file: $BACKUP_FILE${NC}"
    echo ""
    echo -e "${YELLOW}Next steps:${NC}"
    echo "1. Run: php artisan migrate (to apply any new migrations)"
    echo "2. Run: php artisan db:seed (if needed)"
    echo "3. Clear cache: php artisan config:clear && php artisan cache:clear"
else
    echo -e "\n${RED}✗ Restore failed!${NC}"
    echo "Please check the error messages above."
    exit 1
fi
