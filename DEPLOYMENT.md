# 🚀 Enterprise Deployment Guide

This guide outlines the professional procedure for deploying **Jimma Coffee Express** to a production environment (VPS/Cloud Server).

---

## 1. Environment Selection
For an enterprise-grade deployment, we recommend:
- **Provider**: DigitalOcean, AWS, or Google Cloud.
- **OS**: Ubuntu 22.04 LTS or 24.04 LTS.
- **Hardware**: Minimum 1GB RAM / 1 vCPU.

---

## 2. Server Preparation (LAMP Stack)

Connect to your server via SSH and execute the following:

### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### Install Apache, MariaDB, and PHP
```bash
sudo apt install apache2 mariadb-server php libapache2-mod-php php-mysql php-gd php-curl php-mbstring php-xml -y
```

### Secure MariaDB
```bash
sudo mysql_secure_installation
```
*(Follow prompts: Set root password, remove anonymous users, disallow root login remotely, remove test database).*

---

## 3. Database Deployment

1. **Create Database**:
   ```bash
   sudo mysql -u root -p
   # Inside MySQL prompt:
   CREATE DATABASE coffee;
   CREATE USER 'coffee_admin'@'localhost' IDENTIFIED BY 'YOUR_SECURE_PASSWORD';
   GRANT ALL PRIVILEGES ON coffee.* TO 'coffee_admin'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

2. **Import Seed Data**:
   Upload `coffee.sql` to your server and run:
   ```bash
   mysql -u coffee_admin -p coffee < coffee.sql
   ```

---

## 4. Application Deployment

1. **Upload Files**:
   Copy the contents of the `store/` directory to `/var/www/html/`.
   ```bash
   sudo cp -r /path/to/extracted/store/* /var/www/html/
   ```

2. **Configure Permissions**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/
   sudo chmod -R 755 /var/www/html/
   # Ensure upload directories are writable if they exist
   sudo chmod -R 775 /var/www/html/image/
   ```

3. **Production Configuration**:
   Edit `components/config.php` with the production database credentials:
   ```bash
   sudo nano /var/www/html/components/config.php
   ```

---

## 5. Security Hardening (Production Level)

### Install SSL (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-apache -y
sudo certbot --apache -d yourdomain.com
```

### Enable Security Headers
Edit `/etc/apache2/sites-available/000-default-le-ssl.conf` (or your config) and add:
```apache
<IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set Referrer-Policy "no-referrer-when-downgrade"
</IfModule>
```

### Disable Directory Listing
Create or edit `.htaccess` in `/var/www/html/`:
```apache
Options -Indexes
```

---

## 6. Post-Deployment Checklist
- [ ] Verify SSL (HTTPS) is active.
- [ ] Test the Login/Registration flow.
- [ ] Place a test order and verify database entry.
- [ ] Access the Admin Panel and verify product management.
- [ ] Check Apache error logs for any hidden issues: `sudo tail -f /var/log/apache2/error.log`.

---

**Corporate Notice**: Ensure routine backups of the `image/` directory and the `coffee` database are scheduled via CRON jobs.
