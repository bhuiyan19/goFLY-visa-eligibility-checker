# 🔴 Fix 500 Internal Server Error

## সমস্যা:
```
Failed to load resource: the server responded with a status of 500
❌ Failed to save lead: There has been a critical error on this website
```

---

## ✅ দ্রুত সমাধান (3 Minutes):

### Step 1: Debug Version Upload করুন

**1.1 Download করুন:**
- File: `api-debug.php` (GitHub repository থেকে)

**1.2 cPanel File Manager এ যান:**
```
/wp-content/themes/travel-agency/visa-checker-api/
```

**1.3 পুরানো api.php backup নিন:**
- `api.php` select করুন
- Right-click → Rename → `api-old.php`

**1.4 নতুন file upload করুন:**
- Upload `api-debug.php`
- Rename to `api.php`

---

### Step 2: Test করুন (See Actual Error)

**2.1 Browser এ এই URL visit করুন:**
```
https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
```

**2.2 Response দেখবেন - 2টি possibility:**

**✅ Success (WordPress loads):**
```json
{
  "success": true,
  "message": "API is working! WordPress loaded successfully.",
  "data": {
    "wordpress_version": "6.x.x",
    "fluent_forms_active": "Yes",
    "database_prefix": "wp_"
  }
}
```

**❌ Error (WordPress doesn't load):**
```json
{
  "success": false,
  "message": "WordPress not found at: /path/to/wp-load.php",
  "data": {
    "current_dir": "/home/...",
    "checking_path": "..."
  }
}
```

---

### Step 3: Fix Based on Response

### ✅ যদি Success দেখায় (Fluent Forms: Yes):

**Perfect! এখন live test করুন:**
```
1. Live site এ lead submit করুন
2. Console check করুন
3. WordPress এ entry verify করুন
```

**যদি এখনও error হয়:**
```
Form ID সঠিক নয়!
```

**Fix:**
```
1. api.php edit করুন (line 79)
2. Update: $form_id = 6959; ← আপনার Form ID দিন
3. Save করুন
```

---

### ❌ যদি "WordPress not found" দেখায়:

**Problem:** wp-load.php path wrong

**Solution 1 - Automatic Path Detection:**

api.php এ এই code use করুন (line 28 replace করুন):

```php
// Try multiple paths to find WordPress
$possible_paths = [
    __DIR__ . '/../../../../wp-load.php',        // Standard
    __DIR__ . '/../../../../../wp-load.php',     // One level up
    __DIR__ . '/../../../../../../wp-load.php',  // Two levels up
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'   // Document root
];

$wp_load_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $wp_load_path = $path;
        break;
    }
}

if (!$wp_load_path) {
    send_response(false, 'Could not find WordPress', [
        'tried_paths' => $possible_paths
    ]);
}
```

**Solution 2 - Manual Path (Easiest):**

**Find exact path:**
```
cPanel → File Manager → Navigate to wp-load.php
Right-click → Get full path
```

**Update api.php (line 28):**
```php
// Replace this:
$wp_load_path = __DIR__ . '/../../../../wp-load.php';

// With your exact path:
$wp_load_path = '/home/username/public_html/wp-load.php';
```

---

### ❌ যদি "Fluent Forms plugin is not active" দেখায়:

**Solution:**
```
WordPress Admin → Plugins → Activate Fluent Forms
```

---

### ❌ যদি "Form not found" দেখায়:

**Solution:**

**1. Form ID খুঁজুন:**
```
WordPress → Fluent Forms → All Forms
→ Your form এ hover
→ URL দেখুন: form_id=123
```

**2. api.php update করুন (line 79):**
```php
$form_id = 123; // ← Your actual Form ID
```

---

## 🧪 Full Test After Fix:

### Test 1: GET Request (API Health Check)
```
Visit: https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php

Expected:
{
  "success": true,
  "message": "API is working!",
  "data": {
    "wordpress_version": "6.x.x",
    "fluent_forms_active": "Yes"
  }
}
```

### Test 2: POST Request (Submit Lead)
```
1. Live site → Submit test lead
2. Console should show:
   ✅ Lead saved successfully! {submission_id: 456}
```

### Test 3: WordPress Verify
```
WordPress → Fluent Forms → Entries
→ Your test lead should appear
```

---

## 🔍 Common Issues & Solutions:

### Issue 1: "Class entry does not exist"
**Already fixed** - api-debug.php doesn't use classes

### Issue 2: 500 Error persists
**Check:**
```
1. PHP version (must be 7.4+)
2. WordPress debug log
3. Server error log
```

**Enable WordPress debugging:**
```
Edit: wp-config.php
Add before "That's all, stop editing!":

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

Check: /wp-content/debug.log
```

### Issue 3: CORS error
**Already fixed** - api-debug.php has CORS headers

### Issue 4: Database error
**Check database tables exist:**
```
PhpMyAdmin → Database → Tables:
- wp_fluentform_forms
- wp_fluentform_submissions
- wp_fluentform_entry_details
```

---

## 📋 Debug Checklist:

- [ ] api-debug.php uploaded as api.php
- [ ] Visited API URL directly (GET request)
- [ ] Saw success message (WordPress loads)
- [ ] Fluent Forms shows "Yes"
- [ ] Form ID verified and updated
- [ ] Test lead submitted from live site
- [ ] Console shows ✅ success
- [ ] Entry visible in WordPress

---

## 💡 What api-debug.php Does:

**Better than previous version:**
- ✅ Shows detailed errors (not generic 500)
- ✅ Tests WordPress loading
- ✅ Checks Fluent Forms active
- ✅ Verifies form exists
- ✅ Returns helpful error messages
- ✅ Works with GET (for testing)
- ✅ No class dependencies
- ✅ Better error handling

**Response Format:**
```json
{
  "success": true/false,
  "message": "Clear description",
  "data": {
    "submission_id": 456,
    "debug_info": "..."
  }
}
```

---

## 🎯 Expected Timeline:

**Upload api-debug.php:** 1 minute
**Visit URL to test:** 30 seconds
**Fix any issues:** 2-5 minutes
**Test submission:** 1 minute

**Total:** 5-10 minutes

---

## 📞 If Still Not Working:

**Send me:**
1. 📸 Screenshot of browser visiting api.php directly
2. 📋 The JSON response you see
3. 🖥️ Console errors (if any)

**I'll tell you exactly what to fix!**

---

## ✅ Success Looks Like:

**Browser (GET request):**
```
https://goflybd.com/.../api.php

Response:
{
  "success": true,
  "message": "API is working! WordPress loaded successfully.",
  "data": {
    "wordpress_version": "6.4.2",
    "fluent_forms_active": "Yes",
    "database_prefix": "wp_"
  }
}
```

**Console (POST request):**
```javascript
✅ Lead saved successfully! {
  success: true,
  message: "Lead saved successfully!",
  data: {
    submission_id: 456,
    name: "Test User",
    phone: "01712345678",
    country: "নেপাল",
    percentage: "85%"
  }
}
```

**WordPress:**
```
Fluent Forms → Entries → New entry appears! 🎉
```

---

**Next: Upload api-debug.php এবং URL visit করুন!**
**Response screenshot পাঠান, আমি exact fix বলে দেব!** 🚀
