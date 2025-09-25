# CSA Website - Computer Science Association at HCC

A production-ready website for the Computer Science Association at Houston Community College, built with HTML, CSS, JavaScript, PHP, and optional Python utilities.

## Features

### Public Website
- **Modern, Responsive Design** - Mobile-first with dark mode support
- **Member Registration** - Secure signup with email verification
- **Event Management** - Display upcoming workshops, hackathons, and meetings
- **STEM Inclusive** - Open to all Science, Technology, Engineering, and Math majors
- **Accessibility** - WCAG AA compliant with keyboard navigation
- **Privacy-First** - Clear privacy policy and GDPR-style data handling

### Admin Dashboard
- **Member Management** - View, filter, and manage member accounts
- **Statistics & Analytics** - Registration trends, major distribution, campus stats
- **Event Administration** - Create and manage events
- **Data Export** - CSV exports for reporting and analysis
- **Security** - Role-based access with secure authentication

### Security Features
- **CSRF Protection** - All forms protected against cross-site request forgery
- **Rate Limiting** - IP and email-based throttling
- **Email Verification** - Required verification with expiring tokens
- **CAPTCHA Integration** - reCAPTCHA v3 or hCaptcha support
- **Secure Headers** - CSP, XSS protection, and frame options
- **Password Security** - bcrypt hashing for admin accounts

## Requirements

### Server Requirements
- **PHP 8.1+** (recommended) or PHP 7.4+
- **MySQL 5.7+** or **MariaDB 10.3+** (or SQLite 3.8+ as fallback)
- **Apache** or **Nginx** web server
- **SSL Certificate** (recommended for production)

### PHP Extensions
- `pdo_mysql` (for MySQL/MariaDB) or `pdo_sqlite` (for SQLite)
- `openssl` (for token generation)
- `filter` (for email validation)
- `session` (for admin authentication)
- `curl` (for CAPTCHA verification)

### Optional Dependencies
- **Python 3.6+** (for utility scripts)
- `mysql-connector-python` (for Python MySQL scripts)

## Installation

### 1. Download and Upload Files

```bash
# Download the project
git clone <repository-url> csa-website
cd csa-website

# Upload to your web server
# Make sure files are in your web root directory (e.g., public_html, www, htdocs)
```

### 2. Database Setup

#### Option A: MySQL/MariaDB (Recommended)

1. Create a database and user:
```sql
CREATE DATABASE csa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'csa_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON csa.* TO 'csa_user'@'localhost';
FLUSH PRIVILEGES;
```

2. Import the schema:
```bash
mysql -u csa_user -p csa < sql/schema.sql
```

3. (Optional) Import seed data:
```bash
mysql -u csa_user -p csa < sql/seed.sql
```

#### Option B: SQLite (Shared Hosting Fallback)

1. Create the SQLite database:
```bash
mkdir -p data
sqlite3 data/csa.sqlite < sql/schema_sqlite.sql
chmod 664 data/csa.sqlite
chmod 775 data/
```

### 3. Configuration

1. Copy and edit the configuration file:
```bash
cp config/config.php config/config.php.backup
```

2. Edit `config/config.php` with your settings:

```php
return [
    'db' => [
        'driver' => 'mysql', // or 'sqlite'
        'host'   => 'localhost',
        'name'   => 'csa',
        'user'   => 'csa_user',
        'pass'   => 'your_database_password', // UPDATE THIS
        'charset'=> 'utf8mb4',
        // For SQLite, uncomment and set:
        // 'path' => __DIR__ . '/../data/csa.sqlite'
    ],
    
    'smtp' => [
        'host' => 'smtp.gmail.com', // Your SMTP server
        'port' => 587,
        'user' => 'your-email@gmail.com', // UPDATE THIS
        'pass' => 'your-app-password',    // UPDATE THIS
        'from_email' => 'no-reply@yourdomain.com',
        'from_name'  => 'CSA at HCC'
    ],
    
    'security' => [
        'captcha_provider' => 'recaptcha', // or 'hcaptcha'
        'captcha_site_key' => 'your_site_key',   // UPDATE THIS
        'captcha_secret'   => 'your_secret_key', // UPDATE THIS
        // ... other settings
    ],
    
    'app' => [
        'name' => 'Computer Science Association',
        'domain' => 'yourdomain.com',          // UPDATE THIS
        'base_url' => 'https://yourdomain.com', // UPDATE THIS
        'admin_email' => 'president@hccs.edu', // UPDATE THIS
        // ... other settings
    ]
];
```

### 4. Set File Permissions

```bash
# Make scripts executable
chmod +x scripts/*.py

# Set proper permissions for web files
find public -type f -exec chmod 644 {} \;
find public -type d -exec chmod 755 {} \;

# Protect sensitive files
chmod 600 config/config.php
```

### 5. CAPTCHA Setup

#### reCAPTCHA v3 (Recommended)
1. Go to [Google reCAPTCHA](https://www.google.com/recaptcha/)
2. Register your domain
3. Get your site key and secret key
4. Add them to `config/config.php`

#### hCaptcha (Alternative)
1. Go to [hCaptcha](https://www.hcaptcha.com/)
2. Register your domain
3. Get your site key and secret key
4. Update config: `'captcha_provider' => 'hcaptcha'`

### 6. Email Configuration

#### Gmail SMTP
1. Enable 2-factor authentication on your Google account
2. Generate an App Password (not your regular password)
3. Use these settings:
   ```php
   'smtp' => [
       'host' => 'smtp.gmail.com',
       'port' => 587,
       'user' => 'your-email@gmail.com',
       'pass' => 'your-16-char-app-password'
   ]
   ```

#### Other SMTP Providers
- **Outlook/Hotmail**: `smtp-mail.outlook.com`, port 587
- **Yahoo**: `smtp.mail.yahoo.com`, port 587
- **Custom**: Contact your hosting provider for SMTP settings

### 7. Create Admin Account

1. Visit your website
2. Register as a regular member first
3. Manually add yourself as an admin in the database:

```sql
-- Replace with your email and generate a password hash
INSERT INTO admins (email, pass_hash, role) VALUES 
('your-email@hccs.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'PRESIDENT');
```

To generate a password hash:
```php
<?php echo password_hash('your_password', PASSWORD_DEFAULT); ?>
```

### 8. Test Your Installation

1. **Visit the public website**: `https://yourdomain.com`
2. **Test member registration**: Complete the signup process
3. **Check email delivery**: Verify you receive the verification email
4. **Access admin dashboard**: Go to `https://yourdomain.com/admin/`
5. **Review security headers**: Use tools like [Security Headers](https://securityheaders.com/)

## 🔧 Configuration Details

### Database Drivers

#### MySQL/MariaDB (Production)
- Best performance and features
- Supports concurrent access
- Better for high-traffic sites
- Requires database server setup

#### SQLite (Development/Small Sites)
- Single file database
- No server setup required
- Good for shared hosting
- Limited concurrent access

### CAPTCHA Providers

#### reCAPTCHA v3
- Invisible to users
- Risk-based scoring
- Free for most sites
- Google integration

#### hCaptcha
- Privacy-focused alternative
- GDPR compliant
- Can earn rewards
- Independent from Google

### Email Settings

The system supports any SMTP provider. Common configurations:

```php
// Gmail
'smtp' => [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'user' => 'your-email@gmail.com',
    'pass' => 'app-password'
]

// Shared hosting (ask your provider)
'smtp' => [
    'host' => 'mail.yourdomain.com',
    'port' => 587,
    'user' => 'no-reply@yourdomain.com',
    'pass' => 'email-password'
]
```

## 🔐 Security Considerations

### Required Security Measures

1. **Change Default Passwords**
   - Update the default admin password immediately
   - Use strong, unique passwords for all accounts

2. **Configure HTTPS**
   - Obtain and install an SSL certificate
   - Update `base_url` in config to use `https://`
   - Enable HSTS headers

3. **Set Strong CAPTCHA Keys**
   - Never use example/test keys in production
   - Keep secret keys confidential

4. **Secure File Permissions**
   ```bash
   chmod 600 config/config.php
   chmod 644 public/.htaccess
   ```

5. **Regular Updates**
   - Keep PHP updated
   - Monitor for security patches
   - Review access logs regularly

### Optional Security Enhancements

1. **Database Security**
   - Use a dedicated database user with minimal privileges
   - Enable database logging
   - Regular backups with encryption

2. **Rate Limiting**
   - The system includes basic rate limiting
   - Consider Cloudflare or similar for additional protection

3. **Monitoring**
   - Set up log monitoring
   - Monitor failed login attempts
   - Track unusual activity patterns

## Admin Features

### Dashboard Overview
- Total member statistics
- Registration trends
- Popular majors and campuses
- Recent member activity

### Member Management
- View all members with filtering
- Search by name, email, major, or campus
- Bulk actions (export, email, verification)
- Member status management (verified/pending/blocked)

### Event Management
- Create and edit events
- Set dates, locations, and descriptions
- Optional RSVP integration
- Event analytics

### Data Export
- CSV export of member data
- Filtering by status, date range, campus
- Privacy-compliant data handling
- Regular backup scheduling

## Python Utilities

### Member Export Script

Export member data to CSV for analysis:

```bash
# Export all verified members
python3 scripts/export_members.py --verified-only

# Export with custom filename
python3 scripts/export_members.py --output members-2024.csv

# Export including blocked members
python3 scripts/export_members.py --include-blocked
```

**Dependencies**: `mysql-connector-python` (for MySQL) or built-in `sqlite3`

### Weekly Digest Email

Send automated weekly reports to officers:

```bash
# Send digest to default admin email
python3 scripts/weekly_digest.py

# Preview without sending
python3 scripts/weekly_digest.py --dry-run

# Send to specific recipients
python3 scripts/weekly_digest.py --recipients officer1@hccs.edu officer2@hccs.edu

# Save HTML preview
python3 scripts/weekly_digest.py --save-html digest-preview.html
```

#### Cron Setup
Add to crontab for automatic weekly emails:

```bash
# Send digest every Monday at 9 AM
0 9 * * 1 cd /path/to/csa-website && python3 scripts/weekly_digest.py
```

## Customization

### Branding

1. **Logo and Images**
   - Replace `public/assets/img/hcc-logo-placeholder.png`
   - Add your organization's branding
   - Update favicon files

2. **Colors and Styling**
   - Edit CSS variables in `public/assets/css/global.css`
   - Update the `:root` section for color scheme
   - Modify typography and spacing as needed

3. **Content**
   - Update mission statement in `config/config.php`
   - Customize page content in respective PHP files
   - Modify email templates in `vendor/phpmailer/PHPMailer.php`

### Adding Features

The codebase is designed for easy extension:

1. **New Pages**: Add PHP files to `public/`
2. **API Endpoints**: Add to `public/api/`
3. **Admin Features**: Extend `public/admin/`
4. **Database Changes**: Update schema and migration scripts

## Troubleshooting

### Common Issues

#### "Database connection failed"
- Check database credentials in `config/config.php`
- Verify database server is running
- Test connection manually: `mysql -u csa_user -p`

#### "CAPTCHA verification failed"
- Verify site key and secret key are correct
- Check domain registration with CAPTCHA provider
- Test with browser developer tools

#### "Email sending failed"
- Verify SMTP settings and credentials
- Check firewall settings (port 587)
- Test with a simple SMTP test script

#### "Permission denied" errors
- Check file permissions: `ls -la`
- Verify web server user can read files
- Ensure database directory is writable (SQLite)

#### Admin login issues
- Verify admin exists in database: `SELECT * FROM admins;`
- Reset password with SQL: `UPDATE admins SET pass_hash = ? WHERE email = ?`
- Check session configuration in PHP

### Debug Mode

Enable debug output by adding to `config/config.php`:

```php
'debug' => true
```

This will show more detailed error messages (disable in production).

### Log Files

Check these locations for error information:
- PHP error log (usually `/var/log/apache2/error.log`)
- Database logs
- Web server access logs

## File Structure

```
csa-website/
├── config/
│   ├── config.php          # Main configuration
│   └── database.php        # Database connection
├── public/                 # Web root directory
│   ├── admin/             # Admin dashboard
│   ├── api/               # API endpoints
│   ├── assets/            # CSS, JS, images
│   ├── partials/          # Reusable components
│   ├── .htaccess          # Apache configuration
│   ├── index.php          # Homepage
│   ├── about.php          # About page
│   ├── join.php           # Registration page
│   ├── events.php         # Events listing
│   ├── involved.php       # Get involved page
│   ├── privacy.php        # Privacy policy
│   └── verify.php         # Email verification
├── scripts/               # Python utilities
│   ├── export_members.py  # CSV export script
│   └── weekly_digest.py   # Email digest script
├── sql/                   # Database schemas
│   ├── schema.sql         # MySQL/MariaDB schema
│   ├── schema_sqlite.sql  # SQLite schema
│   └── seed.sql           # Sample data
├── vendor/                # Dependencies
│   └── phpmailer/         # Email library
└── README.md              # This file
```

## Contributing

### Development Setup

1. Clone the repository
2. Set up a local development environment (XAMPP, MAMP, etc.)
3. Use SQLite for easy local development
4. Test all features before submitting changes

### Code Standards

- Follow PSR-12 PHP coding standards
- Use semantic HTML5 markup
- Write accessible JavaScript
- Comment complex logic
- Test across multiple browsers

### Submitting Changes

1. Test thoroughly on a staging environment
2. Verify security implications
3. Update documentation as needed
4. Follow semantic versioning for releases

## License

This project is developed for the Computer Science Association at Houston Community College. 

### Usage Rights
- Use for educational institutions
- Modify for your organization's needs
- Deploy on your own servers
- Commercial redistribution without permission

### Attribution
If you use this codebase for your student organization, please credit:
"Website built with CSA Website Template by Houston Community College CSA"

## Support

### Getting Help

1. **Documentation**: Check this README first
2. **Community**: Join the CSA Discord for peer support
3. **Issues**: Report bugs or request features via GitHub issues
4. **Professional**: Contact CSA leadership for consultation

### Contact Information

- **Technical Issues**: Contact the CSA Technology Committee
- **General Questions**: president@hccs.edu
- **Emergency**: Use the HCC IT support channels

---

**Built with ❤️ by the CSA President**

*Computer Science Association • Houston Community College*

---

## Maintenance

### Regular Tasks

**Weekly**:
- Review new member registrations
- Check for pending verifications
- Monitor error logs
- Backup database

**Monthly**:
- Update member statistics
- Review and clean old rate limit records
- Security audit of admin access
- Performance review

**Quarterly**:
- Update dependencies
- Review and update documentation
- Security assessment
- Disaster recovery test

**Annually**:
- Privacy policy review
- Complete security audit
- Server/hosting review
- Feature planning and updates
