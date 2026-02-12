#!/usr/bin/env python3
"""
Electronics Store - Complete Setup Script
Automates the entire setup process including scraping and data insertion
"""

import subprocess
import sys
import os

def print_header(text):
    print("\n" + "=" * 60)
    print(f"  {text}")
    print("=" * 60 + "\n")

def print_step(step, text):
    print(f"[{step}] {text}")

def print_success(text):
    print(f"✓ {text}")

def print_error(text):
    print(f"✗ ERROR: {text}")

def check_python():
    """Check Python version"""
    print_step("1/7", "Checking Python version...")
    version = sys.version_info
    if version.major >= 3 and version.minor >= 7:
        print_success(f"Python {version.major}.{version.minor}.{version.micro} found")
        return True
    else:
        print_error(f"Python 3.7+ required, found {version.major}.{version.minor}")
        return False

def install_packages():
    """Install required Python packages"""
    print_step("2/7", "Installing Python packages...")
    packages = ['requests', 'beautifulsoup4', 'mysql-connector-python']
    
    try:
        for package in packages:
            print(f"  Installing {package}...")
            subprocess.check_call(
                [sys.executable, "-m", "pip", "install", "-q", package],
                stdout=subprocess.DEVNULL,
                stderr=subprocess.DEVNULL
            )
        print_success("All Python packages installed")
        return True
    except subprocess.CalledProcessError as e:
        print_error(f"Failed to install packages: {e}")
        return False

def check_mysql():
    """Check if MySQL is available"""
    print_step("3/7", "Checking MySQL connection...")
    try:
        import mysql.connector
        print_success("MySQL connector is ready")
        return True
    except ImportError:
        print_error("MySQL connector not available")
        return False

def setup_database():
    """Provide instructions for database setup"""
    print_step("4/7", "Database Setup Instructions")
    print("\nPlease run these commands in MySQL:")
    print("-" * 60)
    print("mysql -u root -p")
    print("CREATE DATABASE electronics_store;")
    print("USE electronics_store;")
    print("SOURCE database_setup.sql;")
    print("exit;")
    print("-" * 60)
    
    response = input("\nHave you completed the database setup? (yes/no): ")
    return response.lower() in ['yes', 'y']

def configure_credentials():
    """Guide user to configure database credentials"""
    print_step("5/7", "Database Credentials Configuration")
    print("\nPlease update the following files with your MySQL credentials:")
    print("  1. includes/config.php")
    print("  2. insert_data.py")
    print("\nDefault values to update:")
    print("  - DB_USER / user: 'root' (or your MySQL username)")
    print("  - DB_PASS / password: '' (your MySQL password)")
    
    response = input("\nHave you updated the credentials? (yes/no): ")
    return response.lower() in ['yes', 'y']

def run_scraper():
    """Run the web scraper"""
    print_step("6/7", "Running web scraper...")
    try:
        result = subprocess.run(
            [sys.executable, "scraper_enhanced.py"],
            capture_output=True,
            text=True,
            timeout=120
        )
        
        if result.returncode == 0:
            print_success("Web scraping completed")
            print(result.stdout)
            return True
        else:
            print_error("Scraping failed, but will use default data")
            print(result.stderr)
            return True  # Continue anyway with default data
    except subprocess.TimeoutExpired:
        print_error("Scraping timed out, will use default data")
        return True
    except Exception as e:
        print_error(f"Scraping error: {e}")
        return True  # Continue with default data

def insert_data():
    """Insert data into database"""
    print_step("7/7", "Inserting data into database...")
    try:
        result = subprocess.run(
            [sys.executable, "insert_data.py"],
            capture_output=True,
            text=True,
            timeout=60
        )
        
        if result.returncode == 0:
            print_success("Data insertion completed")
            print(result.stdout)
            return True
        else:
            print_error("Data insertion failed")
            print(result.stderr)
            return False
    except subprocess.TimeoutExpired:
        print_error("Data insertion timed out")
        return False
    except Exception as e:
        print_error(f"Insertion error: {e}")
        return False

def show_final_instructions():
    """Show final instructions to run the server"""
    print_header("Setup Complete!")
    
    print("🎉 Your Electronics Store is ready to use!\n")
    
    print("To start the development server, run:")
    print("  php -S localhost:8000\n")
    
    print("Then open your browser to:")
    print("  http://localhost:8000\n")
    
    print("Login Credentials:")
    print("  Admin:")
    print("    Username: admin")
    print("    Password: password123\n")
    print("  Customer:")
    print("    Username: customer1")
    print("    Password: password123\n")
    
    print("Your database now contains:")
    print("  ✓ 5 Categories")
    print("  ✓ 12 Products")
    print("  ✓ 3 Store Locations")
    print("  ✓ 2 Users")
    print("  ✓ Product Availability Data\n")
    
    print("Enjoy building your e-commerce website! 🚀")

def main():
    """Main setup function"""
    print_header("Electronics Store - Automated Setup")
    
    # Check Python version
    if not check_python():
        sys.exit(1)
    
    # Install packages
    if not install_packages():
        print("\nTrying to continue anyway...")
    
    # Check MySQL
    if not check_mysql():
        print("\nPlease install: pip install mysql-connector-python")
        sys.exit(1)
    
    # Setup database
    if not setup_database():
        print("\nPlease complete the database setup first.")
        sys.exit(1)
    
    # Configure credentials
    if not configure_credentials():
        print("\nPlease update the credentials first.")
        sys.exit(1)
    
    # Run scraper
    run_scraper()
    
    # Insert data
    if not insert_data():
        print("\n⚠ Warning: Data insertion failed. Please check:")
        print("  1. Database credentials are correct")
        print("  2. Database tables are created")
        print("  3. MySQL server is running")
        sys.exit(1)
    
    # Show final instructions
    show_final_instructions()

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\nSetup cancelled by user.")
        sys.exit(1)
    except Exception as e:
        print_error(f"Unexpected error: {e}")
        sys.exit(1)
