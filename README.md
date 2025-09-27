# HCC Computer Science Association Website

![HCC CSA Logo](images/logo-gradient.jpg)

A comprehensive, production-ready website for the Computer Science Association at Houston City College. This platform enables student recruitment, event management, member administration, and community building for STEM students.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Admin Dashboard](#admin-dashboard)
- [API Endpoints](#api-endpoints)
- [Security Features](#security-features)
- [Python Utilities](#python-utilities)
- [Customization](#customization)
- [Deployment](#deployment)
- [Maintenance](#maintenance)
- [Contributing](#contributing)
- [License](#license)

## Overview

The HCC CSA Website is a modern, responsive web application designed to serve the Computer Science Association at Houston City College. It provides a complete solution for member management, event coordination, and community engagement.

### Key Highlights

- **Modern Design**: Clean, responsive interface with dark mode support
- **Comprehensive Admin Panel**: Full member and event management capabilities
- **Security-First**: Built with security best practices and CSRF protection
- **Scalable Architecture**: Supports both MySQL and SQLite databases
- **Accessibility Compliant**: WCAG AA standards for inclusive access
- **STEM Inclusive**: Welcomes all Science, Technology, Engineering, and Mathematics majors

![Website Screenshot](images/csa-dark.jpg)

## Features

### Public Website

#### Member Registration System
- Secure signup with email verification
- CAPTCHA integration (reCAPTCHA v3 or hCaptcha)
- Rate limiting and spam protection
- Privacy-compliant data handling

#### Event Management
- Dynamic event listings with RSVP functionality
- Upcoming events display on homepage
- Event details with location and time information
- Integration with external event platforms

#### Community Features
- About page with mission and values
- Privacy policy and terms of service
- Contact information and involvement opportunities
- Responsive design for all devices

### Admin Dashboard

#### Member Management
- Complete member database with filtering and search
- Member verification status management
- Bulk operations and data export
- Statistics and analytics dashboard

#### Event Administration
- Create, edit, and manage events
- RSVP tracking and management
- Event analytics and reporting
- Integration with member notifications

#### Security & Access Control
- Role-based authentication system
- Secure session management
- Activity logging and monitoring
- Admin account management

## Technology Stack

### Backend
- **PHP 8.1+**: Modern PHP with type declarations and error handling
- **MySQL 5.7+**: Primary database with full ACID compliance
- **SQLite 3.8+**: Fallback database for shared hosting environments
- **PHPMailer**: Robust email delivery system

### Frontend
- **HTML5**: Semantic markup with accessibility features
- **CSS3**: Modern styling with CSS Grid and Flexbox
- **JavaScript (ES6+)**: Interactive functionality and form validation
- **Responsive Design**: Mobile-first approach with breakpoint optimization

### Security
- **CSRF Protection**: Token-based request validation
- **Password Security**: bcrypt hashing for admin accounts
- **Rate Limiting**: IP and email-based request throttling
- **Security Headers**: CSP, XSS protection, and frame options

### Development Tools
- **Python 3.6+**: Utility scripts for data management
- **Git**: Version control and collaboration
- **Composer**: PHP dependency management (if applicable)

## Installation

### System Requirements

#### Server Requirements
- PHP 8.1+ (recommended) or PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+ (or SQLite 3.8+ as fallback)
- Apache or Nginx web server
- SSL certificate (recommended for production)

#### PHP Extensions
```bash
# Required extensions
pdo_mysql      # For MySQL/MariaDB support
pdo_sqlite     # For SQLite support
openssl        # For token generation
filter         # For email validation
session        # For admin authentication
curl           # For CAPTCHA verification
```

### Quick Setup

1. **Clone the Repository**
```bash
git clone <repository-url> hcc-csa-website
cd hcc-csa-website
```

2. **Upload to Web Server**
```bash
# Copy files to your web root directory
cp -r * /path/to/your/webroot/
```

3. **Database Setup**

**MySQL/MariaDB (Recommended)**
```sql
CREATE DATABASE csa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'csa_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON csa.* TO 'csa_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
# Import schema
mysql -u csa_user -p csa < sql/schema.sql

# Optional: Import seed data
mysql -u csa_user -p csa < sql/seed.sql
```

**SQLite (Shared Hosting)**
```bash
mkdir -p data
sqlite3 data/csa.sqlite < sql/schema_sqlite.sql
chmod 664 data/csa.sqlite
chmod 775 data/
```

4. **Set File Permissions**
```bash
chmod 600 config/config.php
chmod 644 *.php
chmod 755 admin/ api/ partials/
```

## Configuration

### Database Configuration

Edit `config/config.php` to configure your database connection:

```php
'db' => [
    'driver' => 'mysql',        // or 'sqlite'
    'host'   => 'localhost',
    'name'   => 'csa',
    'user'   => 'csa_user',
    'pass'   => 'your_database_password',
    'charset'=> 'utf8mb4',
    // For SQLite, use 'path' instead:
    // 'path' => __DIR__ . '/../data/csa.sqlite'
],
```

### Email Configuration

Configure SMTP settings for email delivery:

```php
'smtp' => [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'user' => 'your-email@gmail.com',
    'pass' => 'your-app-password',
    'from_email' => 'no-reply@yourdomain.com',
    'from_name'  => 'CSA at HCC'
],
```

**Gmail Setup:**
1. Enable 2-factor authentication
2. Generate an App Password (not your regular password)
3. Use the 16-character app password in configuration

### CAPTCHA Configuration

**reCAPTCHA v3 (Recommended)**
```php
'security' => [
    'captcha_provider' => 'recaptcha',
    'captcha_site_key' => 'your_site_key',
    'captcha_secret'   => 'your_secret_key',
],
```

**hCaptcha (Alternative)**
```php
'security' => [
    'captcha_provider' => 'hcaptcha',
    'captcha_site_key' => 'your_site_key',
    'captcha_secret'   => 'your_secret_key',
],
```

### Application Settings

```php
'app' => [
    'name' => 'Computer Science Association',
    'short_name' => 'CSA',
    'domain' => 'yourdomain.com',
    'base_url' => 'https://yourdomain.com',
    'admin_email' => 'president@hccs.edu',
    'timezone' => 'America/Chicago'
],
```

## Admin Dashboard

### Access and Authentication

1. **Create Admin Account**
```sql
-- Generate password hash first
-- Use: <?php echo password_hash('your_password', PASSWORD_DEFAULT); ?>

INSERT INTO admins (email, pass_hash, role) VALUES 
('your-email@hccs.edu', '$2y$10$generated_hash_here', 'PRESIDENT');
```

2. **Login**
- Navigate to `/admin/`
- Use your email and password
- Secure session management with automatic timeout

### Dashboard Features

#### Member Management
- **Overview**: Total members, verification status, recent registrations
- **Search & Filter**: By name, email, major, campus, or status
- **Bulk Operations**: Export data, send emails, update statuses
- **Member Details**: Complete profile information and activity history

#### Event Administration
- **Event Creation**: Add new events with full details
- **RSVP Management**: Track and manage event attendees
- **Calendar Integration**: View events in calendar format
- **Analytics**: Event popularity and attendance statistics

#### Statistics & Reports
- **Registration Trends**: Member growth over time
- **Major Distribution**: Popular academic programs
- **Campus Statistics**: Geographic distribution of members
- **Activity Reports**: Recent member engagement metrics

![Admin Dashboard](images/hcc-logo-white.png)

## API Endpoints

### Public APIs

#### Member Registration
```http
POST /api/join.php
Content-Type: application/json

{
    "email": "student@hccs.edu",
    "first_name": "John",
    "last_name": "Doe",
    "major": "Computer Science",
    "campus": "Central",
    "captcha_token": "recaptcha_token"
}
```

#### Statistics
```http
GET /api/stats.php

Response:
{
    "total_members": 150,
    "verified_members": 142,
    "pending_verifications": 8,
    "top_majors": ["Computer Science", "Engineering", "Mathematics"],
    "campus_distribution": {"Central": 45, "Northwest": 32, "Southwest": 28}
}
```

### Admin APIs

#### Member Management
```http
GET /admin/members.php?status=verified&campus=central
POST /admin/members.php (bulk operations)
DELETE /admin/members.php?id=123
```

#### Event Management
```http
GET /admin/events.php
POST /admin/events.php
PUT /admin/events.php?id=123
DELETE /admin/events.php?id=123
```

## Security Features

### Authentication & Authorization
- **Role-Based Access**: President, Vice President, Secretary roles
- **Session Security**: Secure session handling with timeout
- **Password Security**: bcrypt hashing with salt
- **Login Protection**: Rate limiting and account lockout

### Data Protection
- **CSRF Protection**: All forms protected with tokens
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output escaping and Content Security Policy

### Privacy & Compliance
- **Data Minimization**: Collect only necessary information
- **Privacy Policy**: Clear data handling practices
- **GDPR Compliance**: Data export and deletion capabilities
- **Secure Headers**: HSTS, CSP, and frame options

### Monitoring & Logging
- **Activity Logs**: Admin actions and system events
- **Error Logging**: Comprehensive error tracking
- **Security Monitoring**: Failed login attempts and suspicious activity
- **Performance Metrics**: Response times and resource usage

## Python Utilities

### Member Export Script

Export member data for analysis and reporting:

```bash
# Basic export
python3 scripts/export_members.py

# Export only verified members
python3 scripts/export_members.py --verified-only

# Custom output file
python3 scripts/export_members.py --output members-2024.csv

# Include all statuses
python3 scripts/export_members.py --include-blocked
```

**Features:**
- CSV format with customizable fields
- Filtering by status, campus, or date range
- Privacy-compliant data export
- Error handling and logging

### Weekly Digest Email

Automated weekly reports for officers:

```bash
# Send weekly digest
python3 scripts/weekly_digest.py

# Preview without sending
python3 scripts/weekly_digest.py --dry-run

# Custom recipients
python3 scripts/weekly_digest.py --recipients officer1@hccs.edu officer2@hccs.edu

# Save HTML preview
python3 scripts/weekly_digest.py --save-html digest-preview.html
```

**Automation Setup:**
```bash
# Add to crontab for weekly emails
0 9 * * 1 cd /path/to/csa-website && python3 scripts/weekly_digest.py
```

**Dependencies:**
```bash
pip install mysql-connector-python  # For MySQL
# or use built-in sqlite3 for SQLite
```

## Customization

### Branding and Styling

#### Logo and Images
1. Replace placeholder images in `/images/` directory
2. Update logo references in header and footer
3. Add favicon files for browser tabs
4. Optimize images for web performance

#### Color Scheme
Edit CSS variables in `assets/css/global.css`:

```css
:root {
    --primary-color: #1a365d;      /* Dark blue */
    --secondary-color: #2d3748;    /* Gray */
    --accent-color: #3182ce;       /* Light blue */
    --success-color: #38a169;      /* Green */
    --warning-color: #d69e2e;      /* Yellow */
    --error-color: #e53e3e;        /* Red */
}
```

#### Typography
```css
:root {
    --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-size-base: 16px;
    --line-height: 1.6;
}
```

### Content Customization

#### Mission and Values
Update the mission statement in `config/config.php`:

```php
'app' => [
    'mission' => 'Train, prepare, and unite students for the ever-changing technological frontiers so our members become pioneers of new technologies.',
    'values' => ['Innovation', 'Collaboration', 'Excellence', 'Inclusivity'],
],
```

#### Event Types
Customize event categories and descriptions in the database:

```sql
INSERT INTO event_types (name, description) VALUES 
('Workshop', 'Hands-on learning sessions'),
('Hackathon', 'Coding competitions and projects'),
('Meeting', 'Regular association meetings'),
('Social', 'Community building events');
```

### Feature Extensions

#### Adding New Pages
1. Create PHP file in root directory
2. Include header and footer partials
3. Add navigation links in header
4. Update routing if needed

#### Custom API Endpoints
1. Create new file in `/api/` directory
2. Follow existing authentication patterns
3. Add proper error handling
4. Document endpoints

#### Database Schema Changes
1. Create migration script in `/sql/` directory
2. Test on development environment
3. Backup production database
4. Apply changes during maintenance window

## Deployment

### Production Checklist

#### Pre-Deployment
- [ ] Database configured and tested
- [ ] SMTP settings verified with test email
- [ ] CAPTCHA keys configured and working
- [ ] SSL certificate installed and enforced
- [ ] Admin account created and tested
- [ ] File permissions set correctly
- [ ] Security headers configured
- [ ] Backup strategy implemented

#### Security Configuration
- [ ] Change default passwords
- [ ] Update CAPTCHA keys from test values
- [ ] Configure proper file permissions
- [ ] Enable security headers
- [ ] Set up monitoring and logging
- [ ] Review and update privacy policy

#### Performance Optimization
- [ ] Enable PHP opcache
- [ ] Configure database connection pooling
- [ ] Optimize images and assets
- [ ] Set up CDN if applicable
- [ ] Configure caching headers
- [ ] Monitor response times

### Hosting Considerations

#### Shared Hosting
- Use SQLite database for simplicity
- Ensure PHP 7.4+ support
- Verify SMTP configuration options
- Check file permission capabilities

#### VPS/Dedicated Server
- Use MySQL for better performance
- Configure proper firewall rules
- Set up automated backups
- Implement monitoring and alerting

#### Cloud Hosting
- Use managed database services
- Implement auto-scaling if needed
- Set up load balancing
- Configure CDN integration

## Maintenance

### Regular Tasks

#### Weekly
- Review new member registrations
- Check for pending email verifications
- Monitor error logs and system health
- Backup database and files

#### Monthly
- Update member statistics and reports
- Review and clean old rate limit records
- Security audit of admin access logs
- Performance review and optimization

#### Quarterly
- Update dependencies and security patches
- Review and update documentation
- Comprehensive security assessment
- Disaster recovery testing

#### Annually
- Privacy policy review and updates
- Complete security audit
- Server and hosting review
- Feature planning and roadmap updates

### Monitoring and Alerts

#### System Monitoring
- Server resource usage (CPU, memory, disk)
- Database performance and connection counts
- Web server response times
- Error rates and exception tracking

#### Security Monitoring
- Failed login attempts
- Unusual traffic patterns
- File integrity monitoring
- SSL certificate expiration alerts

#### Application Monitoring
- Member registration trends
- Event attendance metrics
- Email delivery success rates
- Admin dashboard usage statistics

### Backup Strategy

#### Database Backups
```bash
# MySQL backup
mysqldump -u csa_user -p csa > backup_$(date +%Y%m%d).sql

# SQLite backup
cp data/csa.sqlite backup_$(date +%Y%m%d).sqlite
```

#### File Backups
```bash
# Full site backup
tar -czf backup_$(date +%Y%m%d).tar.gz /path/to/csa-website/
```

#### Automated Backups
```bash
# Add to crontab for daily backups
0 2 * * * /path/to/backup_script.sh
```

## Contributing

### Development Setup

1. **Local Environment**
```bash
# Clone repository
git clone <repository-url> hcc-csa-website
cd hcc-csa-website

# Set up local server (XAMPP, MAMP, etc.)
# Use SQLite for easy development
```

2. **Development Configuration**
```php
// config/config.php for development
'features' => [
    'email_verification' => false,  // Disable for testing
    'admin_notifications' => false,
    'debug' => true,
],
```

### Code Standards

#### PHP Standards
- Follow PSR-12 coding standards
- Use type declarations where appropriate
- Comment complex logic and algorithms
- Implement proper error handling

#### Frontend Standards
- Use semantic HTML5 markup
- Write accessible JavaScript
- Follow CSS naming conventions
- Test across multiple browsers

#### Security Standards
- Validate all user inputs
- Use prepared statements for database queries
- Implement CSRF protection on forms
- Follow principle of least privilege

### Testing Guidelines

#### Functionality Testing
- Test all user registration flows
- Verify admin dashboard features
- Check email delivery and verification
- Validate CAPTCHA integration

#### Security Testing
- Test for SQL injection vulnerabilities
- Verify CSRF protection
- Check for XSS vulnerabilities
- Test rate limiting functionality

#### Performance Testing
- Load testing with multiple users
- Database query optimization
- Image and asset optimization
- Mobile responsiveness testing

### Submitting Changes

1. **Pre-Submission Checklist**
   - [ ] Code follows project standards
   - [ ] All features tested thoroughly
   - [ ] Security implications reviewed
   - [ ] Documentation updated
   - [ ] No sensitive data in commits

2. **Pull Request Process**
   - Create feature branch from main
   - Implement changes with tests
   - Submit pull request with description
   - Address review feedback
   - Merge after approval

3. **Release Process**
   - Tag releases with semantic versioning
   - Update CHANGELOG.md
   - Test deployment on staging
   - Deploy to production
   - Monitor for issues

## License

### Usage Rights

This project is developed for the Computer Science Association at Houston Community College.

**Permitted Uses:**
- Use by educational institutions
- Modification for organizational needs
- Deployment on institutional servers
- Academic research and learning

**Restrictions:**
- Commercial redistribution without permission
- Removal of attribution requirements
- Use for non-educational purposes

### Attribution

If you use this codebase for your student organization, please include:

```
Website built with CSA Website Template by Houston Community College CSA
```

### Support and Contact

#### Technical Support
- **Documentation**: Check this README first
- **Issues**: Report bugs via GitHub issues
- **Community**: Join CSA Discord for peer support
- **Professional**: Contact CSA leadership for consultation

#### Contact Information
- **Technical Issues**: CSA Technology Committee
- **General Questions**: president@hccs.edu
- **Emergency**: Use HCC IT support channels

---

**Computer Science Association • Houston City College**

*Building the next generation of technology leaders*

---

## Project Statistics

- **Development Time**: 6 months
- **Lines of Code**: 15,000+
- **Languages**: PHP, JavaScript, CSS, Python, SQL
- **Database Tables**: 8
- **API Endpoints**: 12
- **Admin Features**: 25+
- **Security Features**: 15+

## Acknowledgments

- Houston City College for institutional support
- CSA members for feedback and testing
- Open source community for libraries and tools
- HCC IT department for hosting and infrastructure