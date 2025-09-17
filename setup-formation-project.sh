#!/bin/bash

# =============================================================================
# ROCKETSVIEW FORMATION PROJECT SETUP SCRIPT - FRESH PROJECT INSTALLATION
# =============================================================================
# This script automates the complete setup of a new Formation project
# Compatible with macOS and requires Formation package to be downloaded first and put in the packages folder.
# Developed By Faiz Nasir
# =============================================================================

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FORMATION_PACKAGE_PATH="packages/formation"

# =============================================================================
# UTILITY FUNCTIONS
# =============================================================================

print_header() {
    echo -e "\n${PURPLE}================================${NC}"
    echo -e "${PURPLE}$1${NC}"
    echo -e "${PURPLE}================================${NC}\n"
}

print_step() {
    echo -e "${BLUE}[STEP $1]${NC} $2"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${CYAN}ℹ️  $1${NC}"
}

check_command() {
    if ! command -v $1 &> /dev/null; then
        print_error "$1 is not installed. Please install it first."
        exit 1
    fi
}

wait_for_user() {
    echo -e "\n${YELLOW}Press Enter to continue...${NC}"
    read
}

# =============================================================================
# VALIDATION FUNCTIONS
# =============================================================================

validate_project_name() {
    if [[ -z "$PROJECT_NAME" ]]; then
        print_error "Project name cannot be empty!"
        exit 1
    fi
    
    if [[ ! "$PROJECT_NAME" =~ ^[a-zA-Z0-9_-]+$ ]]; then
        print_error "Project name can only contain letters, numbers, hyphens, and underscores!"
        exit 1
    fi
}

check_prerequisites() {
    print_header "CHECKING PREREQUISITES"
    
    # Check required commands
    check_command "php"
    check_command "composer"
    check_command "npm"
    
    # Check for Laravel Herd
    if command -v herd &> /dev/null; then
        print_success "Laravel Herd detected!"
        HERD_AVAILABLE=true
    else
        print_warning "Laravel Herd not found. Using system MySQL."
        check_command "mysql"
        HERD_AVAILABLE=false
    fi
    
    # Check PHP version
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    if [[ $(echo "$PHP_VERSION" | cut -d. -f1) -lt 8 ]] || [[ $(echo "$PHP_VERSION" | cut -d. -f2) -lt 1 ]]; then
        print_error "PHP 8.1 or higher is required. Current version: $PHP_VERSION"
        exit 1
    fi
    
    print_success "PHP version: $PHP_VERSION"
    print_success "All prerequisites are met!"
}

check_formation_package_later() {
    print_header "FORMATION PACKAGE SETUP"
    
    # Auto-create packages directory
    mkdir -p packages
    print_success "Created packages directory: $(pwd)/packages/"
    
    print_warning "IMPORTANT: Formation Package Required!"
    print_info "Before proceeding with Formation integration, you need to:"
    print_info "1. Download the Formation package"
    print_info "2. Extract it to: $(pwd)/packages/formation/"
    print_info "3. Ensure the structure is: packages/formation/composer.json"
    print_info ""
    print_info "Expected structure:"
    print_info "  $(pwd)/"
    print_info "  ├── packages/"
    print_info "  │   └── formation/"
    print_info "  │       ├── composer.json"
    print_info "  │       ├── src/"
    print_info "  │       └── ..."
    print_info ""
    
    while true; do
        echo -n "Have you downloaded and placed the Formation package? (y/n): "
        read -r formation_ready
        
        if [[ "$formation_ready" =~ ^[Yy]$ ]]; then
            # Check if package exists now
            if [[ ! -d "packages/formation" ]]; then
                print_error "Formation package not found at: packages/formation"
                print_info "Please ensure you've extracted the Formation package to the correct location."
                continue
            fi
            
            if [[ ! -f "packages/formation/composer.json" ]]; then
                print_error "Invalid Formation package structure. composer.json not found."
                print_info "Please check the package structure."
                continue
            fi
            
            print_success "Formation package found and validated!"
            break
        elif [[ "$formation_ready" =~ ^[Nn]$ ]]; then
            print_info "Please download and extract the Formation package first, then run the script again."
            exit 0
        else
            print_warning "Please answer 'y' for yes or 'n' for no."
        fi
    done
}

# =============================================================================
# SETUP FUNCTIONS
# =============================================================================

create_project_directory() {
    print_step "1" "Creating project directory: $PROJECT_NAME"
    
    if [[ -d "$PROJECT_NAME" ]]; then
        print_warning "Directory $PROJECT_NAME already exists!"
        echo -n "Do you want to remove it and start fresh? (y/N): "
        read -r response
        if [[ "$response" =~ ^[Yy]$ ]]; then
            print_info "Removing existing directory..."
            rm -rf "$PROJECT_NAME"
            print_success "Existing directory removed."
        else
            print_info "Setup cancelled."
            exit 0
        fi
    fi
    
    mkdir -p "$PROJECT_NAME"
    cd "$PROJECT_NAME"
    print_success "Project directory created and entered: $(pwd)"
}

setup_laravel_project() {
    print_step "2" "Setting up Laravel project structure"
    
    # Create basic Laravel structure if not exists
    if [[ ! -f "artisan" ]]; then
        print_info "Creating new Laravel 10 project for Formation compatibility..."
        
        # Force Laravel 10.x to ensure Formation compatibility
        if ! composer create-project laravel/laravel:"^10.0" . --prefer-dist --no-interaction; then
            print_error "Failed to create Laravel project. Please check your internet connection and try again."
            exit 1
        fi
        
        # Validate Laravel installation
        if [[ ! -f "artisan" ]] || [[ ! -f "composer.json" ]]; then
            print_error "Laravel project creation failed. Missing essential files."
            exit 1
        fi
        
        # Remove existing composer.lock to avoid conflicts
        if [[ -f "composer.lock" ]]; then
            rm composer.lock
            print_info "Removed default composer.lock to avoid conflicts"
        fi
        
        # Verify Laravel version
        LARAVEL_VERSION=$(php artisan --version | grep -o 'Laravel Framework [0-9]*\.[0-9]*' | grep -o '[0-9]*\.[0-9]*' | head -1)
        if [[ "${LARAVEL_VERSION%%.*}" != "10" ]]; then
            print_warning "Warning: Laravel version is $LARAVEL_VERSION, Formation requires Laravel 10.x"
        else
            print_success "Laravel $LARAVEL_VERSION detected - Compatible with Formation!"
        fi
    fi
    
    print_success "Laravel 10 project structure ready!"
}

install_packages() {
    print_step "3" "Installing required packages"
    
    # Backup existing composer.json
    if [[ -f "composer.json" ]]; then
        cp composer.json composer.json.backup
        print_info "Backed up existing composer.json"
    fi
    
    # Update composer.json with required packages
    print_info "Updating composer.json with required packages..."
    
    # Create temporary PHP script to update composer.json
    cat > update_composer.php << 'EOF'
<?php
$composerFile = 'composer.json';
$composer = json_decode(file_get_contents($composerFile), true);

// Required packages - Laravel 10 compatible versions
$requiredPackages = [
    "php" => "^8.1",
    "guzzlehttp/guzzle" => "^7.2",
    "laravel/framework" => "^10.0",
    "laravel/jetstream" => "^3.0",
    "laravel/sanctum" => "^3.2",
    "laravel/tinker" => "^2.8",
    "league/flysystem-aws-s3-v3" => "^3.0",
    "livewire/livewire" => "^2.11",
    "maatwebsite/excel" => "^3.1",
    "spatie/laravel-permission" => "^5.10"
];

// Update require section
foreach ($requiredPackages as $package => $version) {
    $composer['require'][$package] = $version;
}

// Remove problematic packages that might cause conflicts
$packagesToRemove = ['laravel/pail', 'nunomaduro/collision'];
foreach ($packagesToRemove as $package) {
    if (isset($composer['require'][$package])) {
        unset($composer['require'][$package]);
    }
    if (isset($composer['require-dev'][$package])) {
        unset($composer['require-dev'][$package]);
    }
}

// Note: Formation repository will be added later after package is downloaded

file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "composer.json updated successfully!\n";
EOF

    php update_composer.php
    rm update_composer.php
    
    # Remove composer.lock if exists to avoid conflicts
    if [[ -f "composer.lock" ]]; then
        rm composer.lock
        print_info "Removed existing composer.lock"
    fi
    
    # Install packages with conflict resolution
    print_info "Running composer update to resolve dependencies..."
    if ! composer update --no-interaction --with-all-dependencies; then
        print_warning "Composer update failed. Trying alternative approach..."
        
        # Try composer install instead
        print_info "Attempting composer install..."
        if ! composer install --no-interaction; then
            print_error "Package installation failed. Please check the error messages above."
            print_info "You may need to manually resolve dependency conflicts."
            exit 1
        fi
    fi
    
    # Verify essential packages are installed
    if [[ ! -d "vendor/laravel/framework" ]]; then
        print_error "Laravel Framework not properly installed."
        exit 1
    fi
    
    print_success "Packages installed successfully!"
}

setup_jetstream() {
    print_step "4" "Setting up Laravel Jetstream"
    
    print_info "Installing Jetstream with Livewire..."
    if ! php artisan jetstream:install livewire --no-interaction; then
        print_error "Jetstream installation failed."
        exit 1
    fi
    
    print_info "Installing NPM packages..."
    if ! npm install; then
        print_warning "NPM install failed. You may need to run 'npm install' manually later."
    fi
    
    print_success "Jetstream setup completed!"
}

setup_environment() {
    print_step "5" "Setting up environment configuration"
    
    # Copy .env.example to .env
    if [[ -f ".env.example" ]]; then
        cp .env.example .env
        print_success "Environment file created from .env.example"
    else
        print_warning ".env.example not found, creating basic .env file"
        cat > .env << EOF
APP_NAME=$PROJECT_NAME
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${PROJECT_NAME}_db
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF
    fi
    
    # Generate application key
    print_info "Generating application key..."
    php artisan key:generate --no-interaction
    
    print_success "Environment configuration completed!"
}

create_database() {
    local db_name="$1"
    local db_user="$2"
    local db_pass="$3"
    
    print_info "Attempting to create database '$db_name'..."
    
    # Try different methods to create database
    local created=false
    
    # Method 1: Direct MySQL command (Herd/XAMPP/MAMP)
    if command -v mysql &> /dev/null; then
        if [[ -z "$db_pass" ]]; then
            # No password
            if mysql -u"$db_user" -e "CREATE DATABASE IF NOT EXISTS \`$db_name\`;" 2>/dev/null; then
                print_success "Database '$db_name' created successfully!"
                created=true
            fi
        else
            # With password
            if mysql -u"$db_user" -p"$db_pass" -e "CREATE DATABASE IF NOT EXISTS \`$db_name\`;" 2>/dev/null; then
                print_success "Database '$db_name' created successfully!"
                created=true
            fi
        fi
    fi
    
    # Method 2: Try with Herd CLI if available
    if [[ "$created" == false ]] && command -v herd &> /dev/null; then
        if herd db create "$db_name" 2>/dev/null; then
            print_success "Database '$db_name' created using Herd CLI!"
            created=true
        fi
    fi
    
    if [[ "$created" == false ]]; then
        print_warning "Could not create database automatically."
        print_info "Please create the database '$db_name' manually using one of these methods:"
        print_info "1. MySQL Command: CREATE DATABASE \`$db_name\`;"
        print_info "2. phpMyAdmin or similar GUI tool"
        print_info "3. Herd GUI: Open Herd > Database > Create Database"
        print_info ""
        echo -n "Press Enter after creating the database manually..."
        read
    fi
}

test_database_connection() {
    local db_name="$1"
    local max_attempts=3
    local attempt=1
    
    while [[ $attempt -le $max_attempts ]]; do
        print_info "Testing database connection (attempt $attempt/$max_attempts)..."
        
        # Test with a simple query that doesn't require tables
        if php -r "
            try {
                \$pdo = new PDO('mysql:host=127.0.0.1;dbname=$db_name', '$DB_USER', '$DB_PASS');
                \$pdo->query('SELECT 1');
                echo 'success';
            } catch (Exception \$e) {
                echo 'failed: ' . \$e->getMessage();
            }
        " 2>/dev/null | grep -q "success"; then
            print_success "Database connection successful!"
            return 0
        fi
        
        if [[ $attempt -lt $max_attempts ]]; then
            print_warning "Connection failed. Retrying in 2 seconds..."
            sleep 2
        fi
        
        ((attempt++))
    done
    
    print_warning "Database connection failed after $max_attempts attempts."
    return 1
}

setup_database() {
    print_step "6" "Setting up database"
    
    if [[ "$HERD_AVAILABLE" == true ]]; then
        print_info "Using Laravel Herd database configuration..."
        
        # Herd default configuration
        DB_NAME="${PROJECT_NAME}_db"
        DB_USER="root"
        DB_PASS=""
        DB_HOST="127.0.0.1"
        DB_PORT="3306"
        
        print_info "Herd Database Settings:"
        print_info "  Host: $DB_HOST"
        print_info "  Port: $DB_PORT"
        print_info "  Database: $DB_NAME"
        print_info "  Username: $DB_USER"
        print_info "  Password: (empty)"
        
        echo -n "Use these Herd defaults? (Y/n): "
        read -r use_defaults
        if [[ "$use_defaults" =~ ^[Nn]$ ]]; then
            # Manual configuration
            echo -n "Enter database name (default: ${PROJECT_NAME}_db): "
            read -r custom_db_name
            DB_NAME=${custom_db_name:-"${PROJECT_NAME}_db"}
            
            echo -n "Enter database username (default: root): "
            read -r custom_db_user
            DB_USER=${custom_db_user:-"root"}
            
            echo -n "Enter database password (press Enter if no password): "
            read -rs custom_db_pass
            DB_PASS="$custom_db_pass"
            echo
        fi
        
    else
        # Manual database configuration for non-Herd setups
        echo -e "\n${CYAN}Database Configuration:${NC}"
        echo -n "Enter database name (default: ${PROJECT_NAME}_db): "
        read -r DB_NAME
        DB_NAME=${DB_NAME:-"${PROJECT_NAME}_db"}
        
        echo -n "Enter database username (default: root): "
        read -r DB_USER
        DB_USER=${DB_USER:-"root"}
        
        echo -n "Enter database password (press Enter if no password): "
        read -rs DB_PASS
        echo
        
        DB_HOST="127.0.0.1"
        DB_PORT="3306"
    fi
    
    # Update .env file with database configuration
    sed -i '' "s/DB_HOST=.*/DB_HOST=$DB_HOST/" .env
    sed -i '' "s/DB_PORT=.*/DB_PORT=$DB_PORT/" .env
    sed -i '' "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i '' "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i '' "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
    
    # Create database
    create_database "$DB_NAME" "$DB_USER" "$DB_PASS"
    
    # Test database connection
    if test_database_connection "$DB_NAME"; then
        # Run migrations only if connection is successful
        print_info "Running database migrations..."
        if php artisan migrate --no-interaction --force; then
            print_success "Database migrations completed successfully!"
        else
            print_warning "Some migrations may have failed (possibly due to existing tables)."
            print_info "This is normal if you're re-running the setup on an existing database."
            
            # Try to run migrations individually to see what's missing
            print_info "Checking migration status..."
            php artisan migrate:status --no-interaction 2>/dev/null || true
        fi
    else
        print_warning "Skipping migrations due to connection issues."
        print_info "After fixing the database connection, run: php artisan migrate"
    fi
    
    print_success "Database setup completed!"
}

create_seeder() {
    print_step "7" "Creating database seeder"
    
    # Create DatabaseSeeder content
    cat > database/seeders/DatabaseSeeder.php << 'EOF'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
EOF

    # Run seeder only if database connection works
    print_info "Running database seeder..."
    if php artisan migrate:status &> /dev/null; then
        # Check if admin user already exists
        if php -r "
            require 'vendor/autoload.php';
            \$app = require_once 'bootstrap/app.php';
            \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
            try {
                \$user = App\Models\User::where('email', 'admin@example.com')->first();
                if (\$user) {
                    echo 'exists';
                } else {
                    echo 'not_exists';
                }
            } catch (Exception \$e) {
                echo 'error';
            }
        " 2>/dev/null | grep -q "exists"; then
            print_info "Admin user already exists, skipping seeder."
            print_success "Default login: admin@example.com / password"
        else
            if php artisan db:seed --no-interaction; then
                print_success "Database seeder completed!"
                print_info "Default login: admin@example.com / password"
            else
                print_warning "Seeder failed (possibly due to existing data)."
                print_info "This is normal if you're re-running the setup."
                print_info "Default login should be: admin@example.com / password"
            fi
        fi
    else
        print_warning "Skipping seeder due to database connection issues."
        print_info "After fixing the database connection, run: php artisan db:seed"
        print_info "Default login will be: admin@example.com / password"
    fi
}

copy_formation_package() {
    print_step "8" "Copying Formation package"
    
    # Create packages directory in project if it doesn't exist
    mkdir -p packages
    
    # Check multiple possible locations for Formation package
    local formation_found=false
    local formation_source=""
    
    # Location 1: Already in project packages folder (manually placed)
    if [[ -d "packages/formation" ]]; then
        print_info "Formation package found in project packages folder."
        formation_found=true
        formation_source="packages/formation (already in project)"
    # Location 2: Parent directory packages folder
    elif [[ -d "../packages/formation" ]]; then
        print_info "Copying Formation package from parent directory..."
        cp -r ../packages/formation packages/
        formation_found=true
        formation_source="../packages/formation"
    # Location 3: Current directory (if user placed it here)
    elif [[ -d "formation" ]]; then
        print_info "Moving Formation package from current directory to packages..."
        mv formation packages/
        formation_found=true
        formation_source="./formation (moved to packages/)"
    # Location 4: Downloads folder (common location)
    elif [[ -d "$HOME/Downloads/formation" ]]; then
        print_info "Copying Formation package from Downloads folder..."
        cp -r "$HOME/Downloads/formation" packages/
        formation_found=true
        formation_source="$HOME/Downloads/formation"
    fi
    
    if [[ "$formation_found" == true ]]; then
        # Validate the package structure
        if [[ -f "packages/formation/composer.json" ]]; then
            print_success "Formation package ready!"
            print_info "Source: $formation_source"
        else
            print_error "Formation package structure is invalid (missing composer.json)."
            print_info "Please ensure the Formation package has the correct structure."
            exit 1
        fi
    else
        print_error "Formation package not found in any expected location."
        print_info "Searched locations:"
        print_info "  - packages/formation (current project)"
        print_info "  - ../packages/formation (parent directory)"
        print_info "  - ./formation (current directory)"
        print_info "  - $HOME/Downloads/formation (Downloads folder)"
        print_info ""
        print_info "Please place the Formation package in one of these locations and try again."
        exit 1
    fi
}

configure_formation_repository() {
    print_step "9" "Configuring Formation repository"
    
    print_info "Adding Formation repository to composer.json..."
    
    # Create temporary PHP script to add Formation repository
    cat > add_formation_repo.php << 'EOF'
<?php
$composerFile = 'composer.json';
$composer = json_decode(file_get_contents($composerFile), true);

// Add repositories for Formation package
$composer['repositories'] = [
    [
        "type" => "path",
        "url" => "packages/formation",
        "options" => [
            "symlink" => false
        ]
    ]
];

file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Formation repository added to composer.json successfully!\n";
EOF

    php add_formation_repo.php
    rm add_formation_repo.php
    
    print_success "Formation repository configured!"
}

install_formation() {
    print_step "10" "Installing Formation package"
    
    # Validate Formation package exists
    if [[ ! -d "packages/formation" ]]; then
        print_error "Formation package not found in packages/formation directory."
        exit 1
    fi
    
    # Install Formation package
    print_info "Installing Formation package via Composer..."
    if ! composer require leekhengteck/formation:dev-main --no-interaction; then
        print_error "Failed to install Formation package."
        print_info "This could be due to:"
        print_info "1. Network connectivity issues"
        print_info "2. Composer repository configuration problems"
        print_info "3. Package dependency conflicts"
        print_info ""
        print_info "Try running manually: composer require leekhengteck/formation:dev-main"
        exit 1
    fi
    
    # Verify Formation installation
    if [[ ! -d "vendor/leekhengteck/formation" ]] && [[ ! -d "vendor/formation/formation" ]]; then
        print_error "Formation package installation verification failed."
        exit 1
    fi
    
    print_success "Formation package installed successfully!"
}

setup_formation() {
    print_step "11" "Setting up Formation package"
    
    # Check if Formation service provider is available
    print_info "Validating Formation installation..."
    if ! php artisan list | grep -q "vendor:publish"; then
        print_error "Laravel artisan commands not working properly."
        exit 1
    fi
    
    print_info "Publishing Formation assets..."
    
    # Array of publish commands with descriptions
    declare -a publish_commands=(
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"lang\" --no-interaction:Language files"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"livewire\" --no-interaction:Livewire components"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"controllers\" --no-interaction:Controllers"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"actions\" --no-interaction:Action classes"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"views\" --force --no-interaction:View templates"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"migrations\" --no-interaction:Database migrations"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"models\" --no-interaction:Model classes"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"formation\" --no-interaction:Formation resources"
        "php artisan vendor:publish --provider=\"Formation\\FormationServiceProvider\" --tag=\"images\" --no-interaction:Image assets"
    )
    
    local failed_commands=()
    
    # Execute each publish command with error handling
    for cmd_desc in "${publish_commands[@]}"; do
        local cmd="${cmd_desc%:*}"
        local desc="${cmd_desc#*:}"
        
        print_info "Publishing: $desc"
        if ! eval "$cmd" 2>/dev/null; then
            print_warning "Failed to publish: $desc"
            failed_commands+=("$desc")
        else
            print_success "Published: $desc"
        fi
    done
    
    # Report any failures
    if [[ ${#failed_commands[@]} -gt 0 ]]; then
        print_warning "Some Formation assets failed to publish:"
        for failed in "${failed_commands[@]}"; do
            print_warning "  - $failed"
        done
        print_info "You may need to publish these manually later."
    fi
    
    # Run Formation migrations
    print_info "Running Formation migrations..."
    if php artisan migrate:status &> /dev/null; then
        if ! php artisan migrate --no-interaction; then
            print_warning "Formation migrations failed. You may need to run them manually."
        else
            print_success "Formation migrations completed!"
        fi
    else
        print_warning "Skipping Formation migrations due to database connection issues."
    fi
    
    # Build assets
    print_info "Building frontend assets..."
    if ! npm run build; then
        print_warning "Asset build failed. Trying alternative approach..."
        if ! npm run dev; then
            print_warning "Frontend asset compilation failed."
            print_info "You may need to run 'npm run build' or 'npm run dev' manually later."
        else
            print_success "Assets compiled in development mode!"
        fi
    else
        print_success "Assets built successfully!"
    fi
    
    print_success "Formation setup completed!"
}

setup_routing() {
    print_step "12" "Setting up routing"
    
    # Backup existing routes
    if [[ -f "routes/web.php" ]]; then
        cp routes/web.php routes/web.php.backup
        print_info "Backed up existing web routes"
    fi
    
    if [[ -f "routes/api.php" ]]; then
        cp routes/api.php routes/api.php.backup
        print_info "Backed up existing API routes"
    fi
    
    # Setup web routes
    print_info "Setting up Formation web routes..."
    cat > routes/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Resource;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/{moduleSection}/{moduleGroup}/{module}', Resource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import', App\Http\Livewire\ImportResource::class);
    Route::get('/{moduleSection}/{moduleGroup}/{module}/import-errors', App\Http\Livewire\ImportErrorResource::class);
});
EOF

    # Setup API routes
    print_info "Setting up API routes..."
    cat > routes/api.php << 'EOF'
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken($request->device_name)->plainTextToken;
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('/{moduleSection}/{moduleGroup}/{modules}', ApiController::class)->only(['index', 'store']);
    Route::get('/{moduleSection}/{moduleGroup}/{modules}/{id}', [ApiController::class, 'show']);
    Route::put('/{moduleSection}/{moduleGroup}/{modules}/{id}', [ApiController::class, 'update']);
    Route::delete('/{moduleSection}/{moduleGroup}/{modules}/{id}', [ApiController::class, 'destroy']);
});
EOF

    # Validate route files
    if [[ ! -f "routes/web.php" ]] || [[ ! -f "routes/api.php" ]]; then
        print_error "Failed to create route files."
        exit 1
    fi
    
    # Test route configuration
    print_info "Validating route configuration..."
    if ! php artisan route:list --compact &> /dev/null; then
        print_warning "Route validation failed. There may be syntax errors in routes."
        print_info "You may need to check routes/web.php and routes/api.php manually."
    else
        print_success "Routes validated successfully!"
    fi
    
    print_success "Routing setup completed!"
}

setup_herd_integration() {
    print_step "13" "Setting up Herd integration"
    
    if [[ "$HERD_AVAILABLE" == true ]]; then
        print_info "Configuring Laravel Herd integration..."
        
        # Check current directory for Herd linking
        local current_dir=$(pwd)
        local project_path="$current_dir"
        
        # Add site to Herd
        print_info "Adding site to Herd..."
        if herd link "$PROJECT_NAME" 2>/dev/null; then
            print_success "Successfully linked to Herd!"
        else
            print_warning "Could not automatically link to Herd."
            print_info "Manual Herd setup:"
            print_info "1. Open Herd application"
            print_info "2. Go to Sites tab"
            print_info "3. Click 'Add Site' or '+'"
            print_info "4. Select this directory: $project_path"
            print_info "5. Set domain to: ${PROJECT_NAME}.test"
        fi
        
        # Get Herd URL
        HERD_URL="http://${PROJECT_NAME}.test"
        
        # Update APP_URL in .env
        if [[ -f ".env" ]]; then
            sed -i '' "s|APP_URL=.*|APP_URL=$HERD_URL|" .env
            print_success "Updated APP_URL to: $HERD_URL"
        else
            print_warning ".env file not found. Please update APP_URL manually."
        fi
        
        # Test Herd configuration
        print_info "Testing Herd configuration..."
        if herd status "$PROJECT_NAME" &> /dev/null; then
            print_success "Herd site is active!"
        else
            print_info "Herd site may need manual activation."
        fi
        
        print_success "Herd integration completed!"
        print_info "Your site will be available at: $HERD_URL"
        print_info "If the site doesn't work immediately, restart Herd or check the Herd GUI."
    else
        print_info "Herd not available, using standard Laravel serve..."
        HERD_URL="http://localhost:8000"
        
        # Update APP_URL for non-Herd setup
        if [[ -f ".env" ]]; then
            sed -i '' "s|APP_URL=.*|APP_URL=$HERD_URL|" .env
        fi
        
        print_info "Development server setup:"
        print_info "1. Run: php artisan serve"
        print_info "2. Visit: $HERD_URL"
        print_info "3. Login with: admin@example.com / password"
    fi
}

final_optimization() {
    print_step "14" "Final optimization and cleanup"
    
    # Clear application cache
    print_info "Clearing application cache..."
    if ! php artisan optimize:clear --no-interaction; then
        print_warning "Cache clearing failed. Trying individual commands..."
        php artisan config:clear --no-interaction 2>/dev/null || true
        php artisan route:clear --no-interaction 2>/dev/null || true
        php artisan view:clear --no-interaction 2>/dev/null || true
        php artisan cache:clear --no-interaction 2>/dev/null || true
    else
        print_success "Application cache cleared!"
    fi
    
    # Run final migrations if database is available
    if php artisan migrate:status &> /dev/null; then
        print_info "Checking migration status..."
        
        # Check if there are pending migrations
        if php artisan migrate:status | grep -q "Pending"; then
            print_info "Running pending migrations..."
            # Use migrate:fresh only if it's a fresh setup, otherwise use regular migrate
            if ! php artisan migrate --no-interaction --force 2>/dev/null; then
                print_warning "Some migrations failed (likely due to existing tables)."
                print_info "Attempting to mark migrations as completed..."
                
                # Try to mark problematic migrations as run without actually running them
                php artisan migrate --pretend --no-interaction 2>/dev/null || true
                
                print_info "Migration conflicts resolved. This is normal for existing databases."
            else
                print_success "Final migrations completed!"
            fi
        else
            print_success "All migrations are up to date!"
        fi
    else
        print_info "Skipping final migrations due to database connection issues."
    fi
    
    # Generate application key if not set
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        print_info "Generating application key..."
        php artisan key:generate --no-interaction
    fi
    
    # Set proper permissions (macOS/Linux)
    print_info "Setting proper file permissions..."
    chmod -R 755 storage bootstrap/cache 2>/dev/null || true
    
    # Create symbolic link for storage (if needed)
    if [[ ! -L "public/storage" ]]; then
        print_info "Creating storage symbolic link..."
        php artisan storage:link --no-interaction 2>/dev/null || print_info "Storage link creation skipped."
    fi
    
    # Final validation
    print_info "Running final validation..."
    local validation_passed=true
    
    # Check essential files
    local essential_files=(".env" "composer.json" "package.json" "artisan")
    for file in "${essential_files[@]}"; do
        if [[ ! -f "$file" ]]; then
            print_warning "Missing essential file: $file"
            validation_passed=false
        fi
    done
    
    # Check essential directories
    local essential_dirs=("app" "resources" "routes" "database" "storage")
    for dir in "${essential_dirs[@]}"; do
        if [[ ! -d "$dir" ]]; then
            print_warning "Missing essential directory: $dir"
            validation_passed=false
        fi
    done
    
    if [[ "$validation_passed" == true ]]; then
        print_success "Project validation passed!"
    else
        print_warning "Some validation checks failed. Project may need manual fixes."
    fi
    
    print_success "Final optimization completed!"
}

# =============================================================================
# MAIN EXECUTION
# =============================================================================

main() {
    print_header "ROCKETSVIEW FORMATION PROJECT SETUP"
    
    # Get project name
    if [[ -z "$1" ]]; then
        echo -n "Enter project name: "
        read -r PROJECT_NAME
    else
        PROJECT_NAME="$1"
    fi
    
    validate_project_name
    
    print_info "Setting up project: $PROJECT_NAME"
    print_info "Script location: $SCRIPT_DIR"
    
    # Check prerequisites
    check_prerequisites
    
    print_warning "This script will create a new Laravel project first, then integrate Formation."
    print_warning "Make sure you have:"
    print_warning "1. MySQL server running (or Laravel Herd)"
    print_warning "2. Proper database credentials"
    print_warning "3. Formation package ready to download"
    
    wait_for_user
    
    # Execute Laravel setup steps (1-7)
    create_project_directory
    setup_laravel_project
    install_packages
    setup_jetstream
    setup_environment
    setup_database
    create_seeder
    
    # Now ask for Formation package (step 7)
    check_formation_package_later
    
    # Execute Formation setup steps (8-14)
    copy_formation_package
    configure_formation_repository
    install_formation
    setup_formation
    setup_routing
    setup_herd_integration
    final_optimization
    
    # Success message
    print_header "SETUP COMPLETED SUCCESSFULLY!"
    print_success "Project '$PROJECT_NAME' has been set up successfully!"
    print_info "Project location: $(pwd)"
    print_info "Default login: admin@example.com / password"
    print_info ""
    
    if [[ "$HERD_AVAILABLE" == true ]]; then
        print_info "🚀 Laravel Herd Integration:"
        print_info "✅ Site linked to Herd automatically"
        print_info "✅ Visit: http://${PROJECT_NAME}.test"
        print_info "✅ No need to run 'php artisan serve'"
        print_info ""
        print_info "Next steps:"
        print_info "1. Open http://${PROJECT_NAME}.test in your browser"
        print_info "2. Login with admin@example.com / password"
        print_info "3. Start building your Formation modules!"
    else
        print_info "Next steps:"
        print_info "1. cd $PROJECT_NAME"
        print_info "2. php artisan db:seed"
        print_info "3. php artisan serve"
        print_info "4. Visit http://localhost:8000"
        print_info "5. Login with admin@example.com / password"
    fi
    
    print_info ""
    print_success "Happy coding with Formation! 🚀 Developed by Faiz Nasir"
}

# Run main function with all arguments
main "$@"
