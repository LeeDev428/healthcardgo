# HealthCardGo
## Healthcare Appointment Management and Disease Surveillance System

**Version:** 1.0  
**Project Owner:** City Health Office of Panabo City, Davao del Norte

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [User Roles & Permissions](#user-roles--permissions)
- [Key Features Documentation](#key-features-documentation)
- [Project Structure](#project-structure)
- [Development](#development)
- [Testing](#testing)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## 🎯 Overview

The **HealthCardGo** system is a comprehensive digital platform designed to modernize healthcare service delivery for the City Health Office of Panabo City, Davao del Norte. The system streamlines appointment scheduling, patient record management, and real-time disease surveillance across 50+ barangays while providing predictive analytics capabilities for informed public health decision-making.

### Problem Statement
The City Health Office currently faces challenges in:
- Manual appointment scheduling leading to inefficiencies and patient wait times
- Fragmented patient record management across different healthcare services
- Limited real-time visibility into disease patterns and outbreaks
- Lack of data-driven insights for public health planning
- Difficulty in coordinating care across multiple healthcare categories

### Goals and Objectives
- **Operational Efficiency:** Reduce appointment scheduling overhead by 70%
- **Patient Experience:** Provide 24/7 digital access to healthcare services
- **Disease Surveillance:** Enable real-time monitoring of disease patterns across all barangays
- **Predictive Analytics:** Support proactive public health interventions through SARIMA-based forecasting
- **Data Security:** Ensure HIPAA-compliant handling of sensitive medical information

---

## ✨ Features

### Core Features
- ✅ **Multi-role User Management** - Super Admin, Healthcare Admins (4 categories), Doctors, Patients
- ✅ **Patient Registration & Approval** - Workflow for patient registration with admin approval
- ✅ **Walk-in Patient Registration** - Support for patients without user accounts
- ✅ **Appointment Booking & Management** - 7-day lead time appointment scheduling
- ✅ **Digital Health Card Generation** - QR code-enabled health cards (food/non-food purposes)
- ✅ **Medical Records Management** - Encrypted, template-based medical records
- ✅ **Disease Surveillance Dashboard** - Real-time monitoring with interactive heatmaps
- ✅ **Predictive Analytics** - SARIMA-based forecasting for disease and health card trends
- ✅ **In-app Notification System** - Real-time notifications for appointments and approvals
- ✅ **Patient Feedback System** - Post-appointment feedback collection
- ✅ **Reports & Analytics** - Comprehensive reporting with filters (barangay, date range, service category)
- ✅ **Historical Data Management** - Historical disease and health card data tracking
- ✅ **Barangay-based Reporting** - Geographic filtering for targeted public health interventions

### Healthcare Admin Categories
1. **Health Card Admin** - Manages health card generation (food/non-food purposes)
2. **HIV Admin** - Manages HIV testing and treatment records (encrypted)
3. **Pregnancy Admin** - Manages maternal health and pregnancy care
4. **Medical Records Admin** - Cross-category medical records management

---

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 12.0
- **PHP Version:** 8.2+
- **Authentication:** Laravel Fortify 1.30
- **Database:** MySQL
- **PDF Generation:** barryvdh/laravel-dompdf 3.1
- **QR Code:** endroid/qr-code 6.0
- **Dev Tools:** Laravel Boost 1.5

### Frontend
- **UI Framework:** Livewire Flux 2.1.1 (Reactive Components)
- **Styling:** TailwindCSS 4.0.7
- **JavaScript:** Alpine.js 3.x, Axios 1.7.4
- **Charts:** Chart.js 4.5.1
- **Maps:** Leaflet 1.9.4
- **Build Tool:** Vite 7.0.4

### Testing
- **Framework:** PestPHP 3.8
- **Plugins:** Laravel Pest Plugin

### Additional Libraries
- Laravel Volt 1.7.0 (Single-file Livewire components)
- Concurrently (Parallel dev server processes)

---

## 💻 System Requirements

### Required
- **PHP:** >= 8.2
- **Composer:** >= 2.x
- **Node.js:** >= 18.x
- **npm:** >= 9.x
- **MySQL:** >= 8.0

### Recommended
- **OS:** Windows 10/11, Ubuntu 20.04+, or macOS 12+
- **RAM:** 4GB minimum, 8GB recommended
- **Storage:** 500MB minimum for application files
- **Web Server:** Apache 2.4+ or Nginx 1.18+

---

## 📦 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd healthcardgo
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Environment Variables
Edit `.env` file with your settings:

```env
APP_NAME=HealthCardGo
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Manila
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthcardgo
DB_USERNAME=root
DB_PASSWORD=

# Session Configuration
SESSION_DRIVER=database

# Mail Configuration (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@healthcardgo.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Storage Link
```bash
php artisan storage:link
```

---

## 🗄️ Database Setup

### 1. Create Database
```sql
CREATE DATABASE healthcardgo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Seed Database
```bash
php artisan db:seed
```

This will create:
- **Roles:** super_admin, healthcare_admin, doctor, patient
- **Barangays:** 50+ barangays in Panabo City
- **Services:** 9 healthcare services across 7 categories
- **Test Users:**
  - Super Admin: `admin@test.com` / `qwerty123`
  - Doctor: `doctor@test.com` / `qwerty123`
  - Patient: `patient@test.com` / `qwerty123`
  - Health Card Admin: `healthcardadmin@test.com` / `qwerty123`
  - HIV Admin: `hivadmin@test.com` / `qwerty123`
  - Pregnancy Admin: `pregnancyadmin@test.com` / `qwerty123`
  - Medical Records Admin: `medicalrecordsadmin@test.com` / `qwerty123`

### Database Schema Overview

**Core Tables:**
- `users` - User accounts with role-based access
- `roles` - System roles and permissions
- `patients` - Patient profiles (linked to users or walk-in)
- `barangays` - Geographic locations (50+ barangays)
- `services` - Healthcare services (health_card, hiv_testing, pregnancy_care, etc.)
- `appointments` - Appointment bookings with QR codes
- `health_cards` - Digital health cards with QR codes
- `diseases` - Disease surveillance records
- `medical_records` - Encrypted medical records
- `notifications` - In-app notification system
- `feedback` - Patient feedback on services
- `audit_logs` - System audit trail
- `historical_disease_data` - Historical disease tracking
- `historical_health_card_data` - Historical health card metrics
- `announcements` - System-wide announcements

---

## 👥 User Roles & Permissions

### 1. Super Admin (Role ID: 1)
**Responsibilities:**
- Full system access and configuration
- User management (create, edit, deactivate all user types)
- Patient registration approval
- Access to all reports and analytics
- Audit log access

**Permissions:**
- Create/Edit/Delete: Healthcare Admins, Doctors
- Approve/Reject: Patient registrations
- View/Edit: All patient records (including sensitive data)
- Configure: System settings, appointment slots, services, barangays
- Access: All modules and dashboards

**Default Credentials:** `admin@test.com` / `qwerty123`

---

### 2. Healthcare Admins (Role ID: 2)
All healthcare admins can approve patient registrations.

#### 2.1 Health Card Admin (`admin_category: 'healthcard'`)
**Responsibilities:**
- Manage health card records
- Process health card-related appointments
- Update patient health card information
- Generate health card reports
- Filter by health card purpose (food/non-food)

**Permissions:**
- View/Edit: Health card patient records only
- Add: New patients to health card category
- View: Health card appointments and queue
- Generate: Health cards with QR codes

**Default Credentials:** `healthcardadmin@test.com` / `qwerty123`

#### 2.2 HIV Admin (`admin_category: 'hiv'`)
**Responsibilities:**
- Manage HIV testing and treatment records (encrypted)
- Process HIV-related appointments
- Update HIV test results and treatment plans
- Generate HIV surveillance reports

**Permissions:**
- View/Edit: HIV patient records only (encrypted access)
- Add: New patients to HIV category
- View: HIV appointments and queue
- Access: HIV disease surveillance data

**Default Credentials:** `hivadmin@test.com` / `qwerty123`

#### 2.3 Pregnancy Admin (`admin_category: 'pregnancy'`)
**Responsibilities:**
- Manage maternal health records
- Process pregnancy-related appointments
- Track prenatal and postnatal care
- Generate pregnancy and maternal health reports

**Permissions:**
- View/Edit: Pregnancy records only (encrypted access)
- Add: New patients to pregnancy category
- View: Pregnancy-related appointments and queue
- Access: Pregnancy surveillance data

**Default Credentials:** `pregnancyadmin@test.com` / `qwerty123`

#### 2.4 Medical Records Admin (`admin_category: 'medical_records'`)
**Responsibilities:**
- Manage general medical records across all categories
- Consolidate patient medical histories
- Generate comprehensive medical reports
- Archive and maintain record integrity
- Register walk-in patients

**Permissions:**
- View/Edit: All medical records (excluding category-specific sensitive data)
- View: All appointments across categories
- Generate: Cross-category reports and analytics

**Default Credentials:** `medicalrecordsadmin@test.com` / `qwerty123`

---

### 3. Doctors (Role ID: 3)
**Responsibilities:**
- Access patient records during appointments
- Update medical findings and diagnoses
- Record treatment plans and prescriptions
- Complete appointment outcomes
- Provide medical documentation

**Permissions:**
- View: All patient records (including sensitive categories when treating patients)
- Edit: Medical findings, diagnoses, treatment plans
- View: Assigned appointments and queue
- Cannot: Approve registrations, manage users, or modify system settings

**Default Credentials:** `doctor@test.com` / `qwerty123`

---

### 4. Patients (Role ID: 4)
**Responsibilities:**
- Register and maintain personal information
- Book and manage appointments
- View personal medical records
- Access digital health card
- Provide feedback after appointments

**Permissions:**
- View: Own medical records and appointment history
- Book: Appointments (after approval, with 7-day lead time)
- Cancel: Appointments (up to 24 hours before)
- Download: Digital health card with QR code
- Submit: Feedback and ratings

**Default Credentials:** `patient@test.com` / `qwerty123`

---

## 📖 Key Features Documentation

### 1. Appointment Management

#### For Patients:
- **Booking:** 7-day minimum lead time from current date
- **Services Available:** 
  - Health Card
  - HIV Testing
  - Pregnancy Care
  - Vaccination
  - Laboratory
  - Health Education
  - Emergency
- **Status Flow:** Pending → Confirmed → Completed
- **Cancellation:** Up to 24 hours before appointment
- **Digital Copy:** Appointment QR code for check-in

#### For Healthcare Admins:
- **Category Filtering:** Only see appointments for their admin category
- **Status Management:** Can confirm, cancel, or mark appointments as no-show
- **Simplified Statuses:** confirmed, cancelled, no_show only
- **Walk-in Registration:** Can register patients without user accounts
- **Queue Management:** Real-time appointment queue tracking

#### For Super Admin:
- **Full Visibility:** See all appointments across all categories
- **Barangay Filtering:** Filter appointments by patient's barangay
- **Date Range Reports:** Generate appointment reports with custom filters
- **Status Options:** All statuses (pending, confirmed, cancelled, completed, no_show)

---

### 2. Health Card Generation

#### Features:
- **Digital Health Cards:** QR code-enabled for easy verification
- **Purpose Types:**
  - **Food:** For food handlers and vendors
  - **Non-Food:** For general health purposes
- **PDF Download:** Printable health cards with QR codes
- **PNG Download:** Image format for digital use
- **Validity Tracking:** Expiration dates and renewal alerts
- **Historical Data:** Track health card issuance trends

#### Process:
1. Patient completes health card appointment
2. Healthcare admin (Health Card Admin) processes appointment
3. System generates health card with unique QR code
4. Patient can download PDF or PNG from dashboard
5. QR code can be scanned for verification

---

### 3. Disease Surveillance

#### Features:
- **Real-time Dashboard:** Monitor disease cases across barangays
- **Interactive Heatmap:** Visualize disease distribution using Leaflet
- **Trend Predictions:** SARIMA-based forecasting for next 2 months
- **Historical Data:** Track disease patterns over time
- **Barangay-level Reporting:** Targeted interventions based on location

#### Disease Categories:
- Respiratory diseases
- Gastrointestinal diseases
- Vector-borne diseases
- Communicable diseases
- Non-communicable diseases

#### Access:
- Super Admin: Full access to all disease data
- Healthcare Admins: Category-specific disease surveillance
- Doctors: Can record disease diagnoses during appointments

---

### 4. Medical Records Management

#### Features:
- **Encrypted Storage:** Sensitive medical data encrypted at rest
- **Template-based:** Predefined templates for different record types
- **Category-specific Access:** Role-based access to sensitive records
- **Audit Trail:** Track all access and modifications
- **Diagnosis Codes:** Standard medical coding system

#### Record Types:
- General medical records
- HIV testing results (encrypted, HIV Admin only)
- Pregnancy care records (encrypted, Pregnancy Admin only)
- Vaccination records
- Laboratory results

#### Access Control:
- Patients: View own records only
- Doctors: Full access during appointments
- Healthcare Admins: Category-specific access
- Super Admin: Full access to all records

---

### 5. Reports & Analytics

#### Report Types:
1. **Appointment Reports**
   - Filter by: date range, service category, barangay, status
   - Shows: patient details, appointment date, service, barangay, status
   - Export: PDF, Excel (future)

2. **Disease Reports**
   - Filter by: date range, disease type, barangay, severity
   - Shows: disease cases, patient demographics, barangay distribution
   - Export: PDF, Excel (future)

3. **Feedback Reports**
   - Filter by: date range, service category, rating
   - Shows: patient feedback, ratings, service quality metrics
   - Export: PDF, Excel (future)

4. **Health Card Reports** (Health Card Admin only)
   - Filter by: date range, purpose (food/non-food), barangay
   - Shows: health card issuance, validity status, renewal due
   - Export: PDF, Excel (future)

#### Trend Predictions:
- 12 months historical data + 2 months forecasted
- Uses SARIMA (Seasonal AutoRegressive Integrated Moving Average) model
- Displays prediction intervals (lower/upper bounds)
- Available for: appointments, disease cases, health card issuance

---

### 6. Walk-in Patient Registration

#### Purpose:
Support patients who need immediate healthcare services without prior registration.

#### Process:
1. Healthcare admin accesses "Register Patient" page
2. Fills patient details (no email/user account required)
3. Fields: full_name, contact_number, address, date_of_birth, sex, barangay_id
4. Optional: Select service for immediate appointment
5. System creates patient record with `user_id: null`
6. Appointment automatically created if service selected

#### Access:
- All healthcare admin categories can register walk-in patients
- Previously restricted to Medical Records Admin only

#### Note:
Walk-in patients cannot:
- Log into the system
- Book appointments online
- View digital health cards
- Access medical records portal

---

### 7. Notification System

#### Notification Types:
- **Appointment Created:** Notify patient and relevant healthcare admins
- **Appointment Updated:** Status changes (confirmed, cancelled, no-show)
- **Patient Approved:** Account activation notification
- **Health Card Ready:** Digital health card available for download
- **Appointment Reminder:** 24 hours before appointment (future)

#### Features:
- In-app notifications with bell icon
- Real-time notification count
- Notification detail modal
- Mark as read functionality
- Category-based routing (notifications sent only to relevant admins)

#### Access:
- All authenticated users have access to their own notifications
- Notification center accessible via bell icon in navigation

---

## 📁 Project Structure

```
healthcardgo/
├── app/
│   ├── Console/
│   │   └── Commands/              # Artisan commands
│   ├── Enums/
│   │   ├── AdminCategoryEnum.php  # Healthcare admin categories
│   │   └── HealthCardPurposeEnum.php  # Food/Non-food
│   ├── Http/
│   │   ├── Controllers/           # HTTP controllers
│   │   ├── Middleware/            # Custom middleware
│   │   └── Requests/              # Form requests
│   ├── Livewire/
│   │   ├── Admin/                 # Super admin components
│   │   ├── Auth/                  # Authentication components
│   │   ├── HealthcareAdmin/       # Healthcare admin components
│   │   ├── Patient/               # Patient portal components
│   │   ├── Home/                  # Public pages
│   │   ├── Notifications/         # Notification center
│   │   └── Settings/              # User settings
│   ├── Models/                    # Eloquent models
│   ├── Policies/                  # Authorization policies
│   ├── Providers/                 # Service providers
│   └── Services/                  # Business logic services
│       ├── AppointmentService.php
│       ├── DiseaseSurveillanceService.php
│       ├── HealthCardPredictionService.php
│       ├── HealthCardService.php
│       ├── MedicalRecordService.php
│       ├── NotificationService.php
│       ├── PatientRegistrationService.php
│       ├── ReportService.php
│       └── SarimaPredictionService.php
├── bootstrap/
│   ├── app.php                    # Application bootstrap
│   └── cache/                     # Bootstrap cache
├── config/                        # Configuration files
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
│       ├── AdminSeeder.php        # Healthcare admin test accounts
│       ├── BarangaySeeder.php     # 50+ barangays
│       ├── DatabaseSeeder.php     # Master seeder
│       ├── DiseaseSeeder.php      # Sample disease data
│       ├── HistoricalDiseaseDataSeeder.php
│       ├── RoleSeeder.php         # System roles
│       └── ServiceSeeder.php      # Healthcare services
├── public/
│   ├── assets/                    # Static assets
│   ├── build/                     # Vite build output
│   └── storage/                   # Public storage symlink
├── resources/
│   ├── css/
│   │   └── app.css                # TailwindCSS entry point
│   ├── js/
│   │   └── app.js                 # JavaScript entry point
│   └── views/
│       ├── components/
│       │   └── layouts/
│       │       ├── app.blade.php       # Main layout
│       │       ├── admin.blade.php     # Admin layout
│       │       └── patient.blade.php   # Patient layout
│       └── livewire/              # Livewire component views
├── routes/
│   ├── auth.php                   # Fortify authentication routes
│   ├── console.php                # Artisan console routes
│   └── web.php                    # Web routes
├── storage/
│   ├── app/                       # Application storage
│   ├── framework/                 # Framework storage
│   └── logs/                      # Application logs
├── tests/
│   ├── Feature/                   # Feature tests
│   │   ├── Admin/
│   │   ├── HealthcareAdmin/
│   │   └── Patient/
│   └── Unit/                      # Unit tests
├── .env.example                   # Environment template
├── artisan                        # Artisan CLI
├── boost.json                     # Laravel Boost configuration
├── composer.json                  # PHP dependencies
├── package.json                   # Node dependencies
├── phpunit.xml                    # PHPUnit configuration
├── project-prd.md                 # Product Requirements Document
├── README.md                      # This file
└── vite.config.js                 # Vite build configuration
```

---

## 🚀 Development

### Start Development Server
```bash
# Terminal 1: Start Laravel development server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev

# Or use Concurrently to run both:
npm run dev-all
```

### Available Scripts
```bash
# Frontend
npm run dev          # Start Vite dev server
npm run build        # Build for production
npm run lint         # Lint JavaScript

# Backend
php artisan serve    # Start Laravel dev server
php artisan queue:work  # Start queue worker
php artisan schedule:work  # Run scheduled tasks

# Database
php artisan migrate  # Run migrations
php artisan db:seed  # Seed database
php artisan migrate:fresh --seed  # Fresh migration + seed

# Code Quality
composer format      # Format code with Laravel Pint
composer test        # Run tests
composer analyse     # Static analysis (if configured)
```

### Development Tools
- **Laravel Boost:** MCP server for rapid Laravel development
- **Laravel Pint:** Code formatter
- **PestPHP:** Testing framework
- **Vite:** Fast build tool with HMR

---

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Or use Pest directly
./vendor/bin/pest

# Run specific test file
php artisan test --filter=AppointmentManagementTest

# Run with coverage
php artisan test --coverage
```

### Test Structure
```
tests/
├── Feature/
│   ├── Admin/
│   │   ├── AppointmentManagementTest.php
│   │   ├── PatientManagementTest.php
│   │   └── UsersManagementTest.php
│   ├── HealthcareAdmin/
│   │   ├── HealthcareAdminDashboardTest.php
│   │   ├── RegisterPatientTest.php
│   │   └── AppointmentManagementTest.php
│   └── Patient/
│       ├── PatientDashboardTest.php
│       ├── BookAppointmentTest.php
│       └── ViewHealthCardTest.php
└── Unit/
    ├── Services/
    │   ├── AppointmentServiceTest.php
    │   └── NotificationServiceTest.php
    └── Models/
        └── UserTest.php
```

### Key Test Coverage
- ✅ User authentication and registration
- ✅ Appointment booking and management
- ✅ Patient approval workflow
- ✅ Walk-in patient registration
- ✅ Healthcare admin category filtering
- ✅ Disease surveillance
- ✅ Health card generation
- ✅ Notification system
- ✅ Reports generation

---

## 🚢 Deployment

### Production Checklist

#### 1. Environment Configuration
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use secure database credentials
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=healthcardgo_prod
DB_USERNAME=db_user
DB_PASSWORD=strong_password

# Configure mail server
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls

# Set secure session/cache drivers
SESSION_DRIVER=database
CACHE_DRIVER=redis  # Or memcached
QUEUE_CONNECTION=database  # Or redis, sqs

# Configure file storage
FILESYSTEM_DISK=s3  # Or local, if using local storage
```

#### 2. Optimize Application
```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Build frontend assets
npm run build
```

#### 3. Database Migration
```bash
# Run migrations (production)
php artisan migrate --force

# Seed only necessary data (skip test data)
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=BarangaySeeder --force
php artisan db:seed --class=ServiceSeeder --force
```

#### 4. Security
```bash
# Generate application key (if not already done)
php artisan key:generate

# Set proper file permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create storage symlink
php artisan storage:link

# Enable HTTPS (configure web server)
```

#### 5. Web Server Configuration

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/healthcardgo/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Apache Example (.htaccess):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### 6. Queue Worker Setup (Supervisor)
```ini
[program:healthcardgo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/healthcardgo/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/healthcardgo/storage/logs/worker.log
```

#### 7. Cron Jobs
```bash
# Add to crontab
* * * * * cd /var/www/healthcardgo && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 Troubleshooting

### Common Issues

#### 1. Appointment Not Showing for Healthcare Admin
**Symptoms:** Healthcare admin sees 0 appointments despite notifications.

**Solutions:**
- Ensure admin_category matches service category mapping:
  - `healthcard` → `health_card`
  - `hiv` → `hiv_testing`
  - `pregnancy` → `pregnancy_care`
- Check AppointmentManagement.php filters: `whereHas('service', ...)` with proper category map
- Verify role_id === 2 check in render() method

#### 2. Null Pointer Errors for Walk-in Patients
**Symptoms:** "Attempt to read property 'name' on null"

**Solutions:**
- Use null coalescing operators in blade templates:
  ```blade
  {{ $patient->user->name ?? $patient->full_name }}
  {{ $patient->user->email ?? 'N/A' }}
  {{ $patient->user->status ?? 'Walk-in' }}
  ```
- Check for null user_id before accessing user relationship
- Add walk-in badge for visual distinction

#### 3. Dashboard Statistics Not Filtering by Category
**Symptoms:** Dashboard shows all patients instead of category-specific count.

**Solutions:**
- Override total_patients in HealthcareAdminDashboard.php:
  ```php
  if ($user->admin_category && $user->admin_category !== AdminCategoryEnum::MedicalRecords) {
      $statistics['total_patients'] = Patient::whereHas('appointments', function($q) use($categoryMap) {
          $q->whereHas('service', fn($sq) => $sq->where('category', $categoryMap));
      })->distinct()->count();
  }
  ```

#### 4. Vite/Node Errors
**Symptoms:** `npm run dev` fails or assets not loading.

**Solutions:**
```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

#### 5. Composer Dependency Conflicts
**Symptoms:** `composer install` fails with version conflicts.

**Solutions:**
```bash
# Update composer
composer self-update

# Clear composer cache
composer clear-cache

# Remove vendor and reinstall
rm -rf vendor composer.lock
composer install
```

#### 6. Database Connection Errors
**Symptoms:** "SQLSTATE[HY000] [2002] Connection refused"

**Solutions:**
- Check MySQL service is running: `systemctl status mysql`
- Verify database credentials in `.env`
- Ensure database exists: `CREATE DATABASE healthcardgo;`
- Check DB_HOST (use `127.0.0.1` instead of `localhost` on Windows)

#### 7. Storage Symlink Not Working
**Symptoms:** Images/files not loading from storage.

**Solutions:**
```bash
# Remove existing symlink
rm public/storage

# Recreate symlink
php artisan storage:link

# On Windows (run as Administrator):
php artisan storage:link --force
```

#### 8. Permission Denied Errors
**Symptoms:** Laravel can't write to storage or cache.

**Solutions:**
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows: Run as Administrator
icacls storage /grant Users:(OI)(CI)F /T
icacls bootstrap/cache /grant Users:(OI)(CI)F /T
```

---

## 📄 License

This project is proprietary software developed for the City Health Office of Panabo City, Davao del Norte. All rights reserved.

---

## 📞 Support

For technical support or questions:
- **Project Owner:** City Health Office of Panabo City
- **Email:** [Your Contact Email]
- **Documentation:** See `project-prd.md` for detailed requirements

---

## 🙏 Acknowledgments

- City Health Office of Panabo City for project sponsorship
- Laravel Framework team
- Livewire Flux UI team
- All open-source contributors

---

**Built with ❤️ using Laravel 12 and Livewire Flux**
