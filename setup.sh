#!/bin/bash

# Electronics Store Setup Script
# This script automates the setup process

echo "=========================================="
echo "Electronics Store - Automated Setup"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Check Python
echo -e "${YELLOW}[1/6] Checking Python installation...${NC}"
if command -v python3 &> /dev/null; then
    echo -e "${GREEN}✓ Python3 found: $(python3 --version)${NC}"
else
    echo -e "${RED}✗ Python3 not found. Please install Python 3.7+${NC}"
    exit 1
fi

# Step 2: Create virtual environment
echo -e "\n${YELLOW}[2/6] Setting up Python virtual environment...${NC}"
if [ ! -d ".venv" ]; then
    python3 -m venv .venv
    echo -e "${GREEN}✓ Virtual environment created${NC}"
else
    echo -e "${GREEN}✓ Virtual environment already exists${NC}"
fi

# Step 3: Install Python packages
echo -e "\n${YELLOW}[3/6] Installing Python dependencies...${NC}"
source .venv/bin/activate
pip install -q --upgrade pip
pip install -q -r requirements.txt
echo -e "${GREEN}✓ Python packages installed${NC}"

# Step 4: Check MySQL
echo -e "\n${YELLOW}[4/6] Checking MySQL installation...${NC}"
if command -v mysql &> /dev/null; then
    echo -e "${GREEN}✓ MySQL found${NC}"
else
    echo -e "${RED}✗ MySQL not found. Please install MySQL/MariaDB${NC}"
    exit 1
fi

# Step 5: Database setup instructions
echo -e "\n${YELLOW}[5/6] Database Setup${NC}"
echo -e "Please run these MySQL commands manually:"
echo -e "${GREEN}"
echo "  mysql -u root -p"
echo "  CREATE DATABASE electronics_store;"
echo "  USE electronics_store;"
echo "  SOURCE database_setup.sql;"
echo "  exit;"
echo -e "${NC}"
read -p "Press Enter after you've completed the database setup..."

# Step 6: Configure credentials
echo -e "\n${YELLOW}[6/6] Configuration${NC}"
echo -e "Before running the data scripts, make sure to update:"
echo -e "  1. ${GREEN}includes/config.php${NC} - Update DB credentials"
echo -e "  2. ${GREEN}insert_data.py${NC} - Update DB credentials"
echo ""
read -p "Press Enter after updating the credentials..."

# Run scraper and data insertion
echo -e "\n${YELLOW}Scraping data from cellphones.com.vn...${NC}"
python scraper_enhanced.py

echo -e "\n${YELLOW}Inserting data into database...${NC}"
python insert_data.py

# Final instructions
echo -e "\n${GREEN}=========================================="
echo "✓ Setup Complete!"
echo "==========================================${NC}"
echo ""
echo "To start the development server, run:"
echo -e "  ${GREEN}php -S localhost:8000${NC}"
echo ""
echo "Then open your browser to:"
echo -e "  ${GREEN}http://localhost:8000${NC}"
echo ""
echo "Login credentials:"
echo -e "  Admin - username: ${GREEN}admin${NC}, password: ${GREEN}password123${NC}"
echo -e "  Customer - username: ${GREEN}customer1${NC}, password: ${GREEN}password123${NC}"
echo ""
echo "Enjoy! 🚀"
