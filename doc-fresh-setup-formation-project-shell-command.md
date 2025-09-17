# 🚀 Rocketsview Formation - Project Setup Guide and Documentation (fresh-setup-formation-project.sh)

> **Formation Laravel Livewire Development Environment Setup**  
> *Automated Project Initialization for Rocketsview Formation Projects*  
> **Author:** Faiz Nasir | **Version:** 1.0.0 | **Last Updated:** 2025-09-17

## 🌟 Introduction

This comprehensive documentation will walk new developer through setting up a complete Laravel development environment with all necessary dependencies for Rocketsview Formation projects.

The included `setup-formation-project.sh` script automates the entire setup process, including:

- ✅ Laravel 10.x installation
- ✅ Jetstream + Livewire integration
- ✅ Database configuration
- ✅ Formation package setup
- ✅ Environment configuration
- ✅ User authentication
- ✅ Frontend asset compilation

## 🖥️ System Requirements

### Minimum Requirements
- **Operating System**: macOS 12.0 (Monterey) or later
- **PHP**: 8.1 or higher with required extensions
- **Composer**: Latest version
- **Node.js**: LTS version (18.x or higher)
- **Database**: MySQL 8.0+ or MariaDB 10.5+
- **Web Server**: Nginx/Apache (recommended: Laravel Herd)

## 📦 Prerequisites

### 1. Install Required Software

#### Option A: Using Laravel Herd (Recommended)
```bash
# Download and install Laravel Herd
curl -s https://herd.laravel.com/install.sh | sh
```

#### Option B: Manual Installation
```bash
# Install PHP 8.1+ with required extensions
brew install php@8.1

# Install Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Install Node.js & NPM
brew install node@18

# Install MySQL
brew install mysql
brew services start mysql
```

### 2. Prepare Formation Package

1. Download the latest Formation package
2. Place it in the `packages/formation` directory

```bash
# Create packages directory (if it doesn't exist)
mkdir -p packages

# Expected structure:
# your-project/
# ├── packages/
# │   └── formation/     # <-- Place Formation package here
# │       ├── composer.json
# │       ├── src/
# │       └── ...
# └── setup-formation-project.sh

# By default this script will automatically by default create the packages folder.

```

## 🚀 Setup Instructions

### Method 1: Interactive Mode (Recommended)

1. Make the script executable:
   ```bash
   chmod +x setup-formation-project.sh
   ```

2. Run the script:
   ```bash
   ./setup-formation-project.sh
   ```

3. Follow the interactive prompts to configure:
   - Project name
   - Database credentials
   - Environment settings

### Method 2: Direct Mode (For Advanced Users)

```bash
# Basic usage
./setup-formation-project.sh my-project-name

# With database configuration. This one by default no need because it will automatically create the database by using default credentials for you using the root and localhost. Later when finish need to run "php artisan db:seed" to create the admin user.

DB_DATABASE=my_database \
DB_USERNAME=my_user \
DB_PASSWORD=my_password \
./setup-formation-project.sh my-project-name
```

## 🔄 Script Workflow

The setup script follows this workflow:

1. **Environment Validation**
   - Checks PHP, Composer, and Node.js versions
   - Verifies required PHP extensions
   - Validates Formation package presence

2. **Project Initialization**
   - Creates new Laravel project
   - Configures `.env` with optimal settings
   - Sets up application key

3. **Core Dependencies**
   - Installs Laravel Jetstream with Livewire
   - Sets up authentication scaffolding
   - Configures frontend dependencies

4. **Database Setup**
   - Creates database if it doesn't exist
   - Runs database migrations
   - Seeds initial admin user

5. **Formation Integration**
   - Configures Formation package
   - Publishes assets and configurations
   - Sets up permissions and roles

6. **Finalization**
   - Optimizes application
   - Clears caches
   - Builds frontend assets

## 🏆 Best Practices

### Development Workflow
1. Always work in feature branches
2. Run tests before committing
3. Keep `.env` in `.gitignore`
4. Use environment-specific configurations

### Performance Tips
- Enable OPcache for PHP
- Use Redis for caching and sessions
- Optimize Composer autoloader:
  ```bash
  composer dump-autoload -o
  ```

### Security
- Change default admin credentials after first login
- Use strong database passwords
- Keep dependencies updated
- Regularly backup your database

## ❓ Frequently Asked Questions

### Q: Can I use this on Windows?
A: This script is optimized for macOS with Laravel Herd only by Faiz Nasir. Please inform if want for Windows in the future.

### Q: How do I update the Formation package?
A: Replace the contents of `packages/formation` with the new version and run:
```bash
composer update leekhengteck/formation
```

### Q: How do I reset the entire setup?
A: Run these commands:
```bash
rm -rf vendor/
rm -rf node_modules/
rm -rf bootstrap/cache/*
rm -rf storage/framework/views/*
composer install
npm install
php artisan migrate:fresh --seed
```

## 📞 Support

For technical support, please contact:
- **Email**: faiz.nasir@rocketsview.com

---
*© 2025 Rocketsview Management Sdn Bhd. All rights reserved.*
  - Language files
  - Livewire components
  - Controllers
  - Actions
  - Views (with force)
  - Migrations
  - Models
  - Formation resources
  - Images
- Builds frontend assets with `npm run build`

## 🔧 Script Features

### 🛡️ Error Handling
- Validates all prerequisites
- Checks Formation package structure
- Handles composer conflicts automatically
- Tests database connections
- Provides clear error messages

### 🎨 User Experience
- Colored output for better readability
- Progress indicators for each step
- Interactive prompts for configuration
- Backup of existing files
- Clear success/error messages

### 🔍 Validation
- Project name validation (alphanumeric, hyphens, underscores)
- PHP version checking (8.1+)
- **Laravel Herd detection** and integration
- Formation package structure validation
- Database connection testing

### 📊 Logging
- Detailed step-by-step progress
- Clear indication of what's happening
- Error reporting with context

### Manual Recovery

If the script fails at any step, you can:

1. **Check the error message** - it will indicate which step failed
2. **Fix the issue** manually
3. **Re-run the script** - it will detect existing files and ask for confirmation
4. **Continue from where it left off**

## 📁 Project Structure After Setup

```
your-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Livewire/
│   └── Models/
├── packages/
│   └── formation/
├── resources/
│   ├── views/
│   ├── js/
│   └── css/
├── routes/
│   ├── web.php      # Formation web routes
│   └── api.php      # Formation API routes
├── database/
│   ├── migrations/
│   └── seeders/
├── .env             # Environment configuration
└── composer.json    # Updated with Formation requirements
```

## 🎯 After Setup

Once the script completes successfully:

### 1. Access Your Application

#### With Laravel Herd (Recommended):
- **URL**: `http://your-project-name.test`
- **Login**: admin@example.com
- **Password**: password
- **No server needed** - Herd handles everything automatically!

#### Without Herd (Fallback):
```bash
cd your-project-name
php artisan serve
```
- **URL**: http://localhost:8000
- **Login**: admin@example.com
- **Password**: password

### 3. Verify Formation Installation
After login, you should see the Formation sidebar with all modules.

### 4. Optional: Configure Additional Settings
- Update `.env` for production settings
- Configure AWS S3 for file uploads
- Set up additional database connections
- Configure mail settings

## 🔐 Security Notes

### Default Credentials
The script creates a default admin user:
- **Email**: admin@example.com
- **Password**: password

**⚠️ IMPORTANT**: Change these credentials immediately in production!

### Environment Security
- The `.env` file contains sensitive information
- Never commit `.env` to version control
- Use environment-specific configurations

## 🚀 Next Steps

After successful setup, new user can:

1. **Create your first Formation module**
2. **Set up additional users and permissions**
3. **Configure AWS S3 for file uploads**
4. **Customize the UI/UX**
5. **Add your business logic**

## 📞 Support

If you encounter issues:

1. Check this documentation for common solutions and guidelines.
2. Verify all prerequisites are met
3. Ensure Formation package is properly downloaded
4. Check the error messages for specific guidance

## 🎉 Success Indicators

You'll know the setup was successful when:

✅ Script completes without errors  
✅ You can access http://localhost:8000  
✅ Login works with default credentials  
✅ Formation sidebar appears after login  
✅ No console errors in browser  

### By Faiz Nasir (Rocketsview Senior Software Engineer)
