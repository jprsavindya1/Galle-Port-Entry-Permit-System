# Session Expiration Handling

## 🎯 Overview

This document explains how session expiration is handled in the SLPA Permit System to prevent users from encountering error pages when their session expires.

## 🔧 What Was Implemented

### 1. Server-Side Exception Handling

**File:** `bootstrap/app.php`

- **AuthenticationException Handler**: Catches when a user's session has expired
  - Redirects to login page with friendly message
  - Returns JSON response for AJAX requests
  
- **TokenMismatchException Handler**: Catches when CSRF token has expired
  - Redirects to login page with friendly message
  - Returns appropriate status code (419)

```php
// Handles: Expired sessions, logged out users
$exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
    return redirect()->route('login')
        ->with('error', 'Your session has expired. Please login again.');
});

// Handles: Expired CSRF tokens
$exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
    return redirect()->route('login')
        ->with('error', 'Your session has expired. Please login again.');
});
```

### 2. Session Activity Tracking

**File:** `app/Http/Middleware/CheckSessionExpiration.php`

- Tracks user activity on each request
- Updates `last_activity` timestamp in session
- Registered globally for all web routes

### 3. Client-Side Session Management

**File:** `resources/views/layouts/app.blade.php`

Implemented JavaScript handlers for:

#### a) Activity-Based Timer Reset
Monitors user activity (clicks, typing, scrolling) and resets session timer

#### b) Session Expiration Warning
Shows warning 5 minutes before session expires with option to extend

#### c) Auto-Logout
Automatically logs out user when session expires

#### d) AJAX Error Handling
Catches 401 (Unauthorized) and 419 (Token Expired) responses from:
- jQuery AJAX calls
- Fetch API calls

Automatically redirects to login with appropriate message.

## ⚙️ Configuration

### Session Settings (`.env`)

```env
# Session lifetime in minutes (default: 120 = 2 hours)
SESSION_LIFETIME=120

# Session driver (file, database, redis)
SESSION_DRIVER=database

# Expire when browser closes
SESSION_EXPIRE_ON_CLOSE=false

# Encrypt session data (optional)
SESSION_ENCRYPT=false
```

### Recommended Settings

**Development:**
```env
SESSION_LIFETIME=480  # 8 hours
SESSION_DRIVER=file
```

**Production:**
```env
SESSION_LIFETIME=120  # 2 hours
SESSION_DRIVER=database  # or redis for better performance
SESSION_EXPIRE_ON_CLOSE=false
```

**High Security:**
```env
SESSION_LIFETIME=30   # 30 minutes
SESSION_DRIVER=database
SESSION_EXPIRE_ON_CLOSE=true
```

## 🎬 User Experience Flow

### Normal Session Expiration

1. User is inactive for (SESSION_LIFETIME - 5) minutes
2. **Warning appears**: "Your session is about to expire in 5 minutes. Click OK to stay logged in."
3. User clicks OK → Session extends, timer resets
4. User clicks Cancel or ignores → Session expires after 5 minutes

### Expired Session Detection

1. User tries to access protected page
2. **Server detects**: Session expired or CSRF token invalid
3. **User sees**: Redirect to login page with message "Your session has expired. Please login again."
4. User logs in and continues work

### AJAX Request with Expired Session

1. User submits form via AJAX
2. **Server returns**: 401 or 419 status code
3. **JavaScript detects**: Session expired
4. **Alert shows**: "Your session has expired. Please login again."
5. **Auto-redirect**: To login page

## 🚀 Testing Session Expiration

### Method 1: Change Session Lifetime
```env
SESSION_LIFETIME=1  # Set to 1 minute for testing
```

### Method 2: Manual Session Clear
```bash
# Clear all sessions
php artisan session:clear

# Or clear cache
php artisan cache:clear
```

### Method 3: Browser Testing
1. Login to application
2. Wait for session to expire (or manually clear cookies)
3. Try to navigate or submit a form
4. Should redirect to login with message

### Method 4: Test AJAX Calls
```javascript
// In browser console, after session expires:
fetch('/dashboard', {
    method: 'GET',
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => console.log(response.status));
// Should show alert and redirect
```

## 📊 Benefits

### For Users
✅ Clear feedback when session expires  
✅ No confusing error pages  
✅ Warning before automatic logout  
✅ Option to extend session  
✅ Automatic redirection to login  

### For System
✅ Prevents broken form submissions  
✅ Handles AJAX requests gracefully  
✅ Consistent error handling  
✅ Improved security  
✅ Better user tracking  

## 🔒 Security Considerations

### Session Hijacking Prevention
- Use HTTPS in production (`SESSION_SECURE_COOKIE=true`)
- Enable CSRF protection (enabled by default)
- Regenerate session ID on login
- Short session lifetime for sensitive operations

### Production Recommendations
```env
SESSION_DRIVER=database  # or redis
SESSION_LIFETIME=120     # 2 hours max
SESSION_SECURE_COOKIE=true  # Require HTTPS
SESSION_SAME_SITE=lax    # CSRF protection
APP_DEBUG=false          # Never show errors to users
```

## 🛠️ Troubleshooting

### Issue: Session expires too quickly
**Solution:** Increase `SESSION_LIFETIME` in `.env`
```env
SESSION_LIFETIME=240  # 4 hours
```

### Issue: Warning appears too often
**Solution:** Adjust warning time in `app.blade.php`
```javascript
const warningTime = sessionLifetime - (10 * 60 * 1000); // 10 minutes before
```

### Issue: Users not getting redirected
**Solution:** Check exception handler is configured correctly
```bash
php artisan config:clear
php artisan cache:clear
```

### Issue: Database sessions not working
**Solution:** Create sessions table
```bash
php artisan session:table
php artisan migrate
```

## 📝 Files Modified

1. `bootstrap/app.php` - Exception handlers
2. `app/Http/Middleware/CheckSessionExpiration.php` - Session tracking middleware
3. `resources/views/layouts/app.blade.php` - Client-side handlers
4. `ENV_CONFIGURATION.md` - Documentation update

## 🔄 Maintenance

### Monitor Session Storage
```bash
# If using database sessions
php artisan tinker
>>> DB::table('sessions')->count();

# Clean old sessions
php artisan session:gc
```

### Log Analysis
```bash
# Check for authentication errors
tail -f storage/logs/laravel.log | grep "AuthenticationException"
```

## 📚 Additional Resources

- [Laravel Session Documentation](https://laravel.com/docs/session)
- [Laravel Authentication Documentation](https://laravel.com/docs/authentication)
- [Laravel Error Handling](https://laravel.com/docs/errors)

---

**Last Updated:** November 2025  
**Version:** 1.0
