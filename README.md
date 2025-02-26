# Laravel 11 Project Setup Guide

## Introduction
This guide will walk you through setting up the **Laravel 11 API-only** project using MySQL, installing dependencies, configuring the database, running migrations, and setting up API authentication with Laravel Passport.

---

## Prerequisites
Before proceeding, ensure you have the following installed:
- PHP (>= 8.1)
- Composer
- MySQL (or any preferred database)
- Laravel CLI

---

## 1. Environment Setup

### Step 1: Clone the Repository
Clone the project repository from your source control:
```sh
git clone <repository_url>
cd <project_directory>
```

### Step 2: Create the Environment File
Copy the `.env.example` file to create a new `.env` file:
```sh
cp .env.example .env
```

### Step 3: Update Database Configuration
Open the `.env` file and update the following database credentials:
```
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

---

## 2. Install Project Dependencies

Install the PHP dependencies using Composer:
```sh
composer install
```

---

## 3. Database Migration and Seeding

Run the following command to migrate the database and seed it with test data:
⚠️ **Warning:** This will reset the database and insert seed data.
```sh
php artisan migrate:fresh --seed
```

---

## 4. Passport Installation and API Setup
Laravel Passport is required for API authentication. Follow these steps:

### Step 1: Generate Passport Keys
```sh
php artisan passport:keys --force
```

### Step 2: Install API with Passport Integration
```sh
php artisan install:api --passport
```

---

## 5. Running the Application

Start the Laravel development server using:
```sh
php artisan serve
```

Your API should now be running at: 
```
http://127.0.0.1:8000
```

---

## 6. API Documentation
Access the API documentation via the following link:
🔗 [Postman Documentation](https://documenter.getpostman.com/view/23296053/2sAYdeMC7S)

The Postman collection and database backup are located in the project directory:
```
DB & Postman Collection
```

---

## 7. Additional Commands

### Cache Clearing (If Needed)
```sh
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Storage Link (For File Uploads)
```sh
php artisan storage:link
```

---

## 8. Git Repository
The project repository is available at:
🔗 [GitHub Repository](https://github.com/hentryfryzen/ASTUDIO-Practical-Assessment)

---
login credentials : 
Username:hentryfryzen@gmail.com
Password: password


## Conclusion
Your **Laravel 11 API** project with MySQL should now be up and running. If you encounter issues, double-check your `.env` configurations and ensure all required dependencies are installed. Happy developing! 🚀

