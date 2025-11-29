# 🏛️ Victory Fellowship Church Management System

A comprehensive church community management platform built with Laravel 12 and Vue 3, designed to streamline Bible study groups, prayer requests, resource sharing, and member engagement.

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=for-the-badge&logo=typescript)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css)

---

## 📋 Table of Contents

- [Features](#-features)
- [Technology Stack](#-technology-stack)
- [System Architecture](#-system-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [User Roles & Permissions](#-user-roles--permissions)
- [Security Features](#-security-features)
- [OTP Verification System](#-otp-verification-system)
- [API Documentation](#-api-documentation)
- [Production Deployment](#-production-deployment)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 👨‍💼 Admin Features
- **User Management**: Full CRUD operations for users with role assignment
- **Group Oversight**: Monitor and manage all Bible study groups
- **Prayer Request Moderation**: Review and moderate community prayer requests
- **Meeting Coordination**: System-wide meeting management
- **Resource Library**: Centralized resource management and sharing
- **Reports & Analytics**: Comprehensive system analytics and reports
- **Security Settings**: Two-factor authentication configuration
- **Account Deletion Requests**: Review and process member account deletion requests

### 👥 Leader Features
- **Group Management**: Create and manage Bible study groups
- **Member Approval**: Review and approve group join requests
- **Assignment Creation**: Create, grade, and track member assignments
- **Meeting Scheduling**: Schedule virtual and in-person meetings with attendance tracking
- **Resource Sharing**: Share documents, videos, and learning materials
- **Prayer Request Oversight**: Monitor and respond to group prayer requests

### 🙏 Member Features
- **Group Discovery**: Browse and join available Bible study groups
- **Assignment Submission**: Complete and submit assignments with file attachments
- **Prayer Wall**: Post and pray for community prayer requests
- **Meeting Attendance**: RSVP and track attendance for group meetings
- **Resource Access**: Access shared learning materials and resources
- **Profile Management**: Update personal information and preferences

### 🔐 Security Features
- **OTP Email Verification**: One-Time Password verification for all new accounts
- **Forced Password Change**: Admin-created users must change password on first login
- **Two-Factor Authentication**: Optional 2FA for enhanced security
- **Google Sign-In**: Secure OAuth2 authentication with Google
- **Role-Based Access Control**: Granular permissions based on user roles
- **Session Management**: Secure session handling with HTTP-only cookies
- **CSRF Protection**: All forms protected against cross-site request forgery
- **File Upload Security**: MIME type validation and UUID file naming
- **Rate Limiting**: Protection against brute force attacks

---

## 🚀 Technology Stack

### Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **Authentication**: Laravel Fortify + Custom OTP System
- **Database**: SQLite (dev) / MySQL/PostgreSQL (production)
- **Mail**: SMTP with Mailable classes
- **Storage**: Laravel Storage with UUID file naming
- **Validation**: Form Request validation classes

### Frontend
- **Framework**: Vue 3 with TypeScript
- **State Management**: Inertia.js for SPA experience
- **UI Library**: Reka UI + shadcn/ui components
- **Styling**: Tailwind CSS with custom design tokens
- **Icons**: Lucide Vue Next
- **Forms**: Form handling with validation

### Development Tools
- **Build Tool**: Vite
- **Testing**: PHPUnit (backend)
- **Code Quality**: PSR-12, ESLint, Prettier
- **Version Control**: Git

---

## 🏗️ System Architecture

### Database Schema

```
users
├── id (primary key)
├── name
├── email (unique)
├── password (hashed)
├── role (admin|leader|member)
├── google_id (nullable)
├── is_active (boolean)
├── requires_password_change (boolean)
├── email_verified_at
└── timestamps

groups
├── id
├── name
├── description
├── leader_id (foreign key → users)
├── is_public
└── timestamps

group_user (pivot table)
├── group_id

 (foreign key → groups)
├── user_id (foreign key → users)
├── status (pending|approved|rejected|banned)
└── timestamps

assignments
├── id
├── group_id (foreign key)
├── title
├── description
├── due_date
├── attachments (JSON)
└── timestamps

prayer_requests
├── id
├── user_id (foreign key)
├── title
├── content
├── status (pending|answered|closed)
├── is_urgent
├── visibility (public|group|private)
└── timestamps

meetings
├── id
├── group_id (foreign key)
├── title
├── description
├── meeting_type (virtual|in_person)
├── meeting_link
├── location
├── scheduled_at
└── timestamps

resources
├── id
├── group_id (foreign key)
├── title
├── description
├── type (file|link|audio)
├── file_path
├── url
└── timestamps

otps
├── id
├── identifier (email)
├── token (hashed)
├── type (registration|password_reset)
├── expires_at
└── created_at
```

### Multi-Role System

1. **Admin** - Full system access and oversight
2. **Leader** - Group management and member coordination
3. **Member** - Community participation and learning

---

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18.x or higher
- MySQL 8.0+ or PostgreSQL 13+ (for production)
- SQLite (for development)

### Step 1: Clone Repository
```bash
git clone <repository-url>
cd church254
```

### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup
```bash
# Run migrations
php artisan migrate

# Seed default data (creates admin user)
php artisan db:seed
```

### Step 5: Storage Setup
```bash
# Create storage link
php artisan storage:link
```

### Step 6: Start Development Servers
```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite dev server  
npm run dev
```

Visit `http://localhost:8000` in your browser.

---

## ⚙️ Configuration

### Database Configuration (.env)
```env
DB_CONNECTION=sqlite
# For MySQL in production:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=church_management
# DB_USERNAME=your_username
# DB_PASSWORD=your_password
```

### Mail Configuration (.env)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@victoryf.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Google OAuth (.env)
```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

### Session Configuration (.env)
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true  # Production only (HTTPS)
```

---

## 👥 User Roles & Permissions

### Admin Capabilities
- ✅ Manage all users (create, update, delete)
- ✅ Oversee all groups and activities
- ✅ Moderate prayer requests
- ✅ Manage system-wide resources
- ✅ View comprehensive reports
- ✅ Configure system settings
- ✅ Review account deletion requests
- ✅ Access security settings

### Leader Capabilities
- ✅ Create and manage own groups
- ✅ Approve/reject member join requests
- ✅ Create and grade assignments
- ✅ Schedule meetings
- ✅ Share group resources
- ✅ Moderate group prayer requests
- ✅ Track group analytics
- ✅ Ban/unban members (with appeal process)

### Member Capabilities
- ✅ Browse and join groups
- ✅ Submit assignments
- ✅ Post prayer requests
- ✅ RSVP to meetings
- ✅ Access shared resources
- ✅ Update own profile
- ✅ Request account deletion
- ✅ Participate in prayer wall

---

## 🔒 Security Features

### Authentication & Authorization
- **Laravel Fortify**: Industry-standard authentication
- **OTP Email Verification**: Required for all new accounts
- **Password Hashing**: Bcrypt with 12 rounds
- **Rate Limiting**: 5 login attempts per minute
- **Two-Factor Authentication**: Optional TOTP support
- **Google OAuth**: Secure third-party authentication

### Data Protection
- **Role-Based Access Control**: Granular permissions
- **Group Data Isolation**: Members only see their group's content
- **CSRF Protection**: All state-changing operations protected
- **XSS Prevention**: All user input properly escaped
- **SQL Injection Prevention**: Eloquent ORM with parameter binding

### File Security
- **UUID Naming**: Prevents path traversal attacks
- **MIME Type Validation**: Only allowed file types accepted
- **Size Restrictions**: 50MB maximum file size
- **Storage Isolation**: Files stored outside web root
- **Download Permissions**: Authorization checked before serving files

**Allowed File Types:**
- Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT
- Images: JPG, JPEG, PNG, GIF
- Media: MP3, MP4, WAV, AVI, MOV
- Archives: ZIP

**Blocked File Types:**
- Executables: .exe, .bat, .sh, .cmd
- Server Scripts: .php, .asp, .jsp
- System Files: .dll, .sys

---

## 📧 OTP Verification System

### How It Works

1. **User Registration**: 
   - User creates account
   - 6-digit OTP sent to email
   - User redirected to verification page

2. **OTP Verification**:
   - User enters OTP code
   - System verifies against hashed token
   - Account activated upon successful verification

3. **Password Reset**:
   - User requests password reset
   - OTP sent instead of traditional link
   - User verifies OTP before setting new password

4. **Google Sign-In (New Users)**:
   - New Google users receive OTP
   - Must verify email before accessing dashboard

5. **Admin-Created Accounts**:
   - Admin creates user account
   - User receives credentials via email
   - On first login, OTP sent for verification
   - After verification, prompted to change password

### OTP Security Features
- **Hashed Storage**: OTPs hashed before database storage
- **10-Minute Expiration**: Tokens expire after 10 minutes
- **One-Time Use**: OTPs deleted after successful verification
- **Type-Specific**: Separate OTPs for registration vs password reset
- **Rate Limiting**: Protection against brute force attacks

### Resending OTP
Users can request a new OTP if:
- Previous OTP expired
- Email not received
- OTP entered incorrectly multiple times

---

## 🌐 API Documentation

### Authentication Endpoints

```http
POST /register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

```http
POST /login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

```http
POST /logout
```

### OTP Endpoints

```http
GET /verify-otp
# Shows OTP verification page
```

```http
POST /verify-otp
Content-Type: application/json

{
  "otp_code": "123456"
}
```

```http
POST /resend-otp
```

### Google OAuth

```http
GET /auth/google
# Redirects to Google OAuth consent screen
```

```http
GET /auth/google/callback
# Google redirects here after authentication
```

---

## 🚀 Production Deployment

### 1. Environment Configuration

```env
APP_ENV=production
APP_DEBUG=false  # CRITICAL: Must be false
APP_URL=https://yourdomain.com

# Generate new application key
php artisan key:generate

# Database (MySQL/PostgreSQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=church_management
DB_USERNAME=secure_user
DB_PASSWORD=strong_password

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.yourdomain.com

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# Google OAuth
GOOGLE_CLIENT_ID=your-production-client-id
GOOGLE_CLIENT_SECRET=your-production-client-secret
```

### 2. Server Requirements

**PHP Extensions:**
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD

**Recommended PHP Settings:**
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M
```

### 3. Deployment Steps

```bash
# 1. Install dependencies (production only)
composer install --optimize-autoloader --no-dev

# 2. Build frontend assets
npm run build

# 3. Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run migrations
php artisan migrate --force

# 5. Seed database (optional - creates admin user)
php artisan db:seed

# 6. Set file permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Link storage
php artisan storage:link
```

### 4. Security Hardening

- Enable HTTPS redirect
- Configure firewall rules
- Set up automated backups
- Enable error logging
- Configure rate limiting
- Add security headers:
  - X-Frame-Options: SAMEORIGIN
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block

### 5. Default Credentials

After seeding, default admin account:
- **Email**: lintasan2098@gmail.com
- **Password**: password

**⚠️ IMPORTANT**: Change this password immediately!

---

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/Auth/LoginTest.php

# Run specific test method
php artisan test --filter=testUserCanLogin
```

### Test Categories
- **Feature Tests**: End-to-end workflow testing
- **Unit Tests**: Model and service logic testing
- **Security Tests**: Authorization and validation testing

---

## 📊 Default Admin Account

After running `php artisan db:seed`, you can log in with:

- **Email**: lintasan2098@gmail.com
- **Password**: password
- **Role**: Admin

**Security Note**: Change the default password immediately after first login.

---

## 📝 Code Standards

This project follows:
- **PSR-12**: PHP coding standards
- **Laravel Conventions**: Framework best practices
- **TypeScript**: Type-safe frontend code
- **ESLint**: JavaScript/Vue code quality
- **Prettier**: Consistent code formatting

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Contribution Guidelines
- Follow existing code style
- Write tests for new features
- Update documentation as needed
- Ensure all tests pass before submitting PR

---

## 🐛 Bug Reports

Found a bug? Please open an issue with:
- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Environment details (PHP version, OS, etc.)

---

## 📄 License

This project is licensed under the MIT License. See the `LICENSE` file for details.

---

## 👏 Acknowledgments

Built with:
- [Laravel](https://laravel.com/) - The PHP Framework
- [Vue.js](https://vuejs.org/) - Progressive JavaScript Framework
- [Inertia.js](https://inertiajs.com/) - Modern Monolith Architecture
- [Tailwind CSS](https://tailwindcss.com/) - Utility-First CSS Framework
- [Reka UI](https://www.reka-ui.com/) - Vue Components Library
- [Lucide](https://lucide.dev/) - Beautiful Icon Set

---

## 📞 Support

For support and questions:
- **Documentation**: [Laravel Docs](https://laravel.com/docs)
- **Security Issues**: Report via GitHub Issues (mark as security)
- **General Questions**: Open a discussion on GitHub

---

## 🔄 Version History

### Version 1.0.0 (Current)
- ✅ Complete authentication system with OTP verification
- ✅ Google OAuth integration
- ✅ Multi-role user system (Admin, Leader, Member)
- ✅ Group management with join request workflow
- ✅ Assignment creation and submission
- ✅ Prayer wall with community engagement
- ✅ Meeting scheduling and attendance tracking
- ✅ Resource library with progress tracking
- ✅ Modern UI with role-specific themes
- ✅ Comprehensive security features
- ✅ Forced password change for admin-created users

---

**Made with ❤️ for church communities worldwide**

**Development Philosophy**: "Build with love, secure with wisdom, design with empathy."
