# Session Expiration Testing Guide

## 🎯 Quick 2-Minute Test

### Setup (Do Once)
1. Open `.env` file
2. Find `SESSION_LIFETIME=120`
3. Change to `SESSION_LIFETIME=2`
4. Run commands:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### Test Flow
```
Minute 0:00 → Login to application
Minute 1:50 → ⚠️ Warning popup appears
              "Your session is about to expire in 5 minutes"
              
Action: Click "Cancel" (don't extend session)

Minute 2:00 → 🔒 Auto-logout
              Alert: "Your session has expired"
              Redirect to login page
```

## 🧪 Detailed Test Cases

### Test Case 1: Session Warning with Extension
**Expected Result:** Session extends when user clicks OK

1. Login to application
2. Wait 1 minute 50 seconds
3. Warning popup appears
4. Click **"OK"** button
5. ✅ **Should**: Session extends, timer resets, you stay logged in
6. Wait another 1 minute 50 seconds
7. ✅ **Should**: Warning appears again

**Status:** [ ] PASS  [ ] FAIL

---

### Test Case 2: Session Warning Ignored (Auto-Logout)
**Expected Result:** Auto-logout after warning ignored

1. Login to application
2. Wait 1 minute 50 seconds
3. Warning popup appears
4. Click **"Cancel"** or close the popup
5. Wait 10 more seconds
6. ✅ **Should**: Alert "Your session has expired"
7. ✅ **Should**: Auto-redirect to login page

**Status:** [ ] PASS  [ ] FAIL

---

### Test Case 3: Try to Navigate After Session Expires
**Expected Result:** Graceful redirect to login

1. Login to application
2. Wait 2+ minutes (session expires)
3. Click any navigation link (e.g., "Dashboard", "Temporary Permit")
4. ✅ **Should**: Redirect to login page
5. ✅ **Should**: See message "Your session has expired. Please login again."
6. ❌ **Should NOT**: See any error page or "419" error

**Status:** [ ] PASS  [ ] FAIL

---

### Test Case 4: Try to Submit Form After Session Expires
**Expected Result:** Redirect to login with message

1. Login to application
2. Go to "Temporary Permit" form
3. Fill out the form (don't submit yet)
4. Wait 2+ minutes
5. Click "Check Availability" or "Add to List"
6. ✅ **Should**: Alert "Your session has expired. Please login again."
7. ✅ **Should**: Redirect to login page
8. ❌ **Should NOT**: See "TokenMismatchException" or "419 error"

**Status:** [ ] PASS  [ ] FAIL

---

### Test Case 5: AJAX Call After Session Expires
**Expected Result:** Alert and redirect

1. Login to application
2. Wait 2+ minutes
3. Open browser console (F12)
4. Run this code:
   ```javascript
   fetch('/dashboard', {
       method: 'GET',
       headers: { 'X-Requested-With': 'XMLHttpRequest' }
   });
   ```
5. ✅ **Should**: Alert appears
6. ✅ **Should**: Redirect to login

**Status:** [ ] PASS  [ ] FAIL

---

### Test Case 6: Manual Cookie Clear (Instant Logout)
**Expected Result:** Immediate redirect on next action

1. Login to application
2. Open browser console (F12)
3. Run this code:
   ```javascript
   document.cookie.split(";").forEach(c => {
       document.cookie = c.replace(/^ +/, "")
           .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
   });
   ```
4. Try to navigate or submit a form
5. ✅ **Should**: Redirect to login
6. ✅ **Should**: Message appears

**Status:** [ ] PASS  [ ] FAIL

---

## 🔧 Testing Different Session Lifetimes

### 1-Minute Test (Very Quick)
```env
SESSION_LIFETIME=1
```
- Warning at: 55 seconds
- Expires at: 1 minute

### 5-Minute Test (Quick)
```env
SESSION_LIFETIME=5
```
- Warning at: 4 minutes 55 seconds
- Expires at: 5 minutes

### Production Setting (2 Hours)
```env
SESSION_LIFETIME=120
```
- Warning at: 1 hour 55 minutes
- Expires at: 2 hours

## 📊 Verification Checklist

After testing, verify:

- [ ] No "419 Page Expired" error shown
- [ ] No "TokenMismatchException" error shown
- [ ] No "AuthenticationException" error shown
- [ ] Clear message: "Your session has expired. Please login again."
- [ ] Smooth redirect to login page
- [ ] Warning appears 5 minutes before expiry
- [ ] Option to extend session works
- [ ] AJAX calls handle expiry gracefully
- [ ] Form submissions handle expiry gracefully

## 🐛 Troubleshooting

### Issue: Warning never appears
**Solution:**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Check browser console for JavaScript errors
# Press F12 and look for errors in Console tab
```

### Issue: Still seeing error pages
**Solution:**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Restart server
# Close terminal and run: php artisan serve
```

### Issue: Session expires immediately
**Check:**
```bash
# View current session lifetime
php artisan tinker
>>> config('session.lifetime');
# Should show: 2 (or your set value)
```

## 🎬 Video Test Script

**For creating demo video:**

```
[00:00] Show current time and SESSION_LIFETIME=2 in .env
[00:10] Login to application
[00:20] Navigate around normally
[01:50] Warning popup appears on screen
[01:55] Click "Cancel" to reject extension
[02:00] Alert appears: "Session expired"
[02:05] Auto-redirect to login page
[02:10] Try to access dashboard → redirects to login
[02:15] See message: "Your session has expired. Please login again."
[02:20] Login again successfully
[02:25] End
```

## 📝 Reset After Testing

**IMPORTANT:** After testing, restore production settings:

```env
# In .env file
SESSION_LIFETIME=120  # Change back from 2 to 120
```

Then clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

---

**Test Date:** _______________  
**Tester:** _______________  
**Result:** [ ] All Tests Passed  [ ] Issues Found

**Notes:**
________________________________
________________________________
________________________________
