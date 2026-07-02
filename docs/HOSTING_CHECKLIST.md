# 🚀 Hosting Deployment Checklist

## 📋 Files NOT Pushed to GitHub (Protected by .gitignore)

These files contain sensitive information and are automatically excluded:

### ✅ Protected Files
- ✅ `.env` - **Your actual environment configuration**
- ✅ `.env.backup` - Backup of environment
- ✅ `.env.production` - Production environment
- ✅ `.env.staging` - Staging environment
- ✅ `auth.json` - Composer authentication
- ✅ `/vendor` - PHP dependencies
- ✅ `/node_modules` - Node dependencies
- ✅ `/storage/*.key` - Encryption keys
- ✅ `/bootstrap/cache/*` - Cached config files

## 🔧 Configuration Changes for Hosting

### 1. Environment Variables (.env)

**⚠️ IMPORTANT: You need to manually configure these on your hosting platform**

#### **Session Configuration**
```env
SESSION_LIFETIME=240        # 4 hours (recommended for permit system)
SESSION_DRIVER=database     # Use database for better persistence
SESSION_ENCRYPT=false       # Set to true if handling very sensitive data
```

#### **Application Settings**
```env
APP_NAME="SLPA Permit System"
APP_ENV=production          # Change from 'local' to 'production'
APP_DEBUG=false             # CRITICAL: Must be false in production
APP_URL=https://your-domain.com  # Your actual hosting URL
```

#### **Database Configuration**
```env
DB_CONNECTION=mysql
DB_HOST=your-database-host  # Provided by hosting
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-secure-password
```

#### **Mail Configuration** (if using email)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@slpa.lk
MAIL_FROM_NAME="SLPA Permit System"
```

#### **Security Settings**
```env
SESSION_SECURE_COOKIE=true  # Requires HTTPS
SESSION_SAME_SITE=lax       # CSRF protection
```

---

### 2. Files That ARE Pushed to GitHub

These are safe and needed for deployment:

#### ✅ Configuration Files (Safe to Push)
```
✅ .env.example              # Template (no sensitive data)
✅ nixpacks.toml             # Build configuration
✅ railway.toml              # Deployment configuration
✅ composer.json             # PHP dependencies list
✅ package.json              # Node dependencies list
✅ config/*.php              # Laravel config files
✅ routes/*.php              # Application routes
✅ database/migrations/*     # Database schema
```

---

### 3. Hosting Platform Environment Variables

**Set these directly in your hosting dashboard:**

| Variable | Value | Notes |
|----------|-------|-------|
| `APP_ENV` | `production` | Never `local` |
| `APP_DEBUG` | `false` | Never `true` in production |
| `APP_KEY` | `base64:...` | Generate new: `php artisan key:generate` |
| `APP_URL` | `https://yourapp.com` | Your actual domain |
| `SESSION_LIFETIME` | `240` | 4 hours recommended |
| `SESSION_DRIVER` | `database` | Better than file |
| `DB_*` | (your values) | From hosting provider |

---

### 4. Railway.app Specific Configuration

**Already configured in `railway.toml`:**
```toml
[deploy]
healthcheckPath = "/health"           # ✅ Already set
healthcheckTimeout = 300              # ✅ Already set
restartPolicyType = "ON_FAILURE"      # ✅ Already set
restartPolicyMaxRetries = 10          # ✅ Already set
```

**No changes needed** - these are good defaults.

---

### 5. Build Configuration (nixpacks.toml)

**Already configured:**
```toml
[phases.setup]
nixPkgs = ["nodejs", "php82", "php82Packages.composer"]  # ✅ Correct

[phases.install]
cmds = ["composer install --no-dev --optimize-autoloader"]  # ✅ Good for production

[phases.build]
cmds = [
  "php artisan config:cache",   # ✅ Cache config
  "php artisan view:cache"      # ✅ Cache views
]

[start]
cmd = "php artisan route:clear && php artisan migrate --force && php artisan user:create-admin admin@slpa.lk Admin123 || true"
```

**⚠️ SECURITY WARNING:** The admin password `Admin123` is visible in this file!

**Recommended Change:**
```toml
[start]
cmd = "php artisan route:clear && php artisan migrate --force"
```

Then manually create admin via SSH:
```bash
php artisan user:create-admin admin@slpa.lk YourSecurePassword123!
```

---

## 🔐 Security Checklist Before Deployment

### Pre-Deployment
- [ ] `.env` file is NOT in repository (check with `git status`)
- [ ] Generated new `APP_KEY` for production
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Changed database password from default
- [ ] Set strong admin password (not in nixpacks.toml)
- [ ] Configured HTTPS (`APP_URL` starts with `https://`)
- [ ] Set `SESSION_SECURE_COOKIE=true`

### Post-Deployment
- [ ] Test session expiration works (set `SESSION_LIFETIME=2` temporarily)
- [ ] Verify login/logout works
- [ ] Check all forms submit correctly
- [ ] Test database connection
- [ ] Verify emails send (if configured)
- [ ] Check error pages don't show debug info
- [ ] Test session modal appears correctly
- [ ] Verify auto-logout after session timeout

---

## 📝 Environment Variables Summary

### Current Settings (from .env.example)
```env
SESSION_LIFETIME=120        # Default: 2 hours
SESSION_DRIVER=database     # ✅ Good for production
SESSION_ENCRYPT=false       # ✅ Okay for most cases
```

### Recommended Production Settings
```env
SESSION_LIFETIME=240        # 4 hours (better for permit forms)
SESSION_DRIVER=database     # Keep this
SESSION_ENCRYPT=false       # Keep this unless very sensitive
SESSION_SECURE_COOKIE=true  # ADD THIS (requires HTTPS)
SESSION_SAME_SITE=lax       # ADD THIS (CSRF protection)
```

---

## 🎯 Deployment Steps

### Step 1: Prepare Local .env
```bash
# Copy example
cp .env.example .env

# Generate app key
php artisan key:generate

# Test locally
php artisan serve
```

### Step 2: Push to GitHub
```bash
git add .
git commit -m "Ready for production deployment"
git push origin main
```

**Note:** `.env` file will NOT be pushed (protected by .gitignore)

### Step 3: Configure Hosting Platform

**Railway.app / Similar Platforms:**
1. Connect GitHub repository
2. Go to "Variables" tab
3. Add environment variables:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY=base64:...` (copy from local .env)
   - `APP_URL=https://your-app-url.com`
   - `SESSION_LIFETIME=240`
   - `DB_*` (provided by platform)
   
### Step 4: Deploy
- Platform will auto-deploy from GitHub
- Run migrations: `php artisan migrate --force`
- Create admin: `php artisan user:create-admin admin@slpa.lk SecurePass123!`

### Step 5: Verify
- Visit your app URL
- Test login
- Test session expiration
- Check all features work

---

## 🔄 Update Workflow

**When making changes:**

1. **Code changes** → Push to GitHub ✅
2. **Environment variables** → Update in hosting dashboard ⚠️
3. **Database changes** → Migrations auto-run on deploy ✅

**Files to update in hosting dashboard (not GitHub):**
- Environment variables only
- Everything else goes through GitHub

---

## 📞 Quick Reference

### What Goes Where?

| Type | Location | Pushed to GitHub? |
|------|----------|-------------------|
| Code changes | Local → GitHub → Hosting | ✅ Yes |
| `.env` file | Hosting dashboard only | ❌ No |
| Environment variables | Hosting dashboard | ❌ No |
| Config files (`config/*.php`) | GitHub | ✅ Yes |
| Migrations | GitHub | ✅ Yes |
| Build config (`nixpacks.toml`) | GitHub | ✅ Yes |
| Deploy config (`railway.toml`) | GitHub | ✅ Yes |

---

## 🚨 Common Mistakes to Avoid

❌ **DON'T:**
- Push `.env` file to GitHub
- Use `APP_DEBUG=true` in production
- Use default passwords
- Hardcode passwords in code
- Skip HTTPS in production

✅ **DO:**
- Set environment variables in hosting dashboard
- Use `APP_DEBUG=false` in production
- Use strong, unique passwords
- Use environment variables for secrets
- Enable HTTPS

---

**Last Updated:** November 2025  
**For:** SLPA Port Entry Permit System
