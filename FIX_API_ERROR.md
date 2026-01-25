# 🔧 Fix "Class entry does not exist" Error

## সমস্যা:
```
❌ Failed to save lead: Class "entry" does not exist
```

**কারণ:** আগের api.php file wrong class ব্যবহার করছে।

---

## ✅ সমাধান (2 Minutes):

### Step 1: নতুন api.php Download করুন

**File:** `api-simple.php` (এই repository তে আছে)

---

### Step 2: cPanel এ Upload করুন

**2.1 cPanel File Manager এ যান:**
```
cPanel → File Manager → /wp-content/themes/travel-agency/visa-checker-api/
```

**2.2 পুরানো api.php delete করুন:**
- `api.php` select করুন → Right-click → Delete

**2.3 নতুন file upload করুন:**
- Upload button click করুন
- `api-simple.php` select করুন
- Upload complete হলে:
  - `api-simple.php` select করুন
  - Right-click → Rename → `api.php` করুন

---

### Step 3: Form ID Update করুন

**3.1 api.php file edit করুন:**
- `api.php` → Right-click → Edit

**3.2 Line 53 খুঁজুন:**
```php
$form_id = 6959; // CHANGE THIS to your actual Form ID
```

**3.3 আপনার actual Form ID দিন:**

**কিভাবে Form ID পাবেন:**
```
WordPress Admin → Fluent Forms → All Forms
→ Your form এ hover করুন
→ URL দেখুন: ...?form_id=123
```

Example:
```php
$form_id = 123; // আপনার Form ID
```

**3.4 Save করুন**

---

### Step 4: Test করুন

**4.1 Live site এ যান এবং test lead submit করুন**

**4.2 Browser Console দেখুন (F12):**

**Success:**
```
✅ Lead saved successfully! {success: true, submission_id: 456, ...}
```

**4.3 WordPress চেক করুন:**
```
WordPress Admin → Fluent Forms → Entries
```

আপনার lead দেখতে পাবেন! ✅

---

## 🔍 এই নতুন api.php কী করে:

**পুরানো Version (যা কাজ করে নি):**
- ❌ Fluent Forms classes use করছিল (যা load হয় নি)
- ❌ Class "entry" খুঁজছিল

**নতুন Version (যা কাজ করবে):**
- ✅ Direct database queries use করে
- ✅ `fluentform_submissions` table এ save করে
- ✅ `fluentform_entry_details` table এও save করে
- ✅ Fluent Forms hooks trigger করে (email notifications এর জন্য)
- ✅ Better error handling
- ✅ CORS headers included
- ✅ Detailed error logging

---

## 📊 Database Tables:

নতুন api.php এই tables এ data save করে:

**1. wp_fluentform_submissions:**
```sql
- form_id: 6959
- response: JSON data
- ip: User IP
- status: unread
- created_at: Timestamp
```

**2. wp_fluentform_entry_details:**
```sql
- submission_id: Auto
- field_name: input_name
- field_value: Test User
(আরও fields...)
```

---

## ⚙️ Configuration:

**api.php এ যা change করতে হবে:**

**Line 53 - Form ID (Required):**
```php
$form_id = 6959; // ← আপনার Form ID দিন
```

**কিভাবে Form ID পাবেন:**
1. WordPress → Fluent Forms → All Forms
2. Your form এ hover করুন
3. URL bottom এ দেখবেন: `...form_id=123`
4. সেই number use করুন

---

## 🧪 Test Data:

**Submit করার পর Console এ দেখবেন:**

```json
{
  "success": true,
  "message": "Lead saved successfully",
  "submission_id": 456,
  "data": {
    "name": "Test User",
    "phone": "01712345678",
    "country": "নেপাল",
    "percentage": 85
  }
}
```

---

## 🔒 Security Features:

নতুন api.php এ যা আছে:

✅ **CORS Headers:** Vercel থেকে request allow করে
✅ **Input Sanitization:** SQL injection prevent করে
✅ **POST Only:** শুধু POST requests accept করে
✅ **Data Validation:** Required fields check করে
✅ **Error Logging:** Errors WordPress error log এ save হয়
✅ **IP Tracking:** User IP record করে

---

## 📧 Email Notifications:

এই api.php automatic trigger করে Fluent Forms email notifications।

**Setup (যদি না থাকে):**
```
Fluent Forms → Your Form → Settings → Email Notifications
→ Add Notification
→ Send to: your@email.com
→ Subject: New Visa Lead: {inputs.input_country}
```

---

## 🐛 Troubleshooting:

### Error: "Fluent Forms is not active"

**Solution:**
```
WordPress → Plugins → Verify Fluent Forms active আছে
```

### Error: "Database insert failed"

**Solution:**
1. Check database connection
2. Verify table exists:
   ```sql
   wp_fluentform_submissions
   wp_fluentform_entry_details
   ```

### Error: "Form not found"

**Solution:**
- Verify Form ID সঠিক
- Form published আছে কিনা check করুন

### No errors but entry না দেখা গেলে:

**Check করুন:**
1. Form ID সঠিক কিনা
2. WordPress → Fluent Forms → Entries → Filter by form
3. Database এ entry আছে কিনা:
   ```sql
   SELECT * FROM wp_fluentform_submissions ORDER BY id DESC LIMIT 10;
   ```

---

## 📝 Quick Steps Summary:

1. ✅ Download `api-simple.php`
2. ✅ Upload to `/wp-content/themes/travel-agency/visa-checker-api/`
3. ✅ Rename to `api.php` (replace old one)
4. ✅ Edit line 53: Update Form ID
5. ✅ Save file
6. ✅ Test lead submission
7. ✅ Check WordPress Fluent Forms → Entries
8. ✅ Verify console shows: `✅ Lead saved successfully!`

---

## 🎯 Expected Result:

**Before Fix:**
```
❌ Failed to save lead: Class "entry" does not exist
```

**After Fix:**
```
✅ Lead saved successfully! {submission_id: 456}
```

**WordPress:**
```
Fluent Forms → Entries → Your lead appears! 🎉
```

---

**File Ready:** `api-simple.php`
**Time Required:** 2-3 minutes
**Difficulty:** Easy (just upload & change Form ID)

---

**এখন করুন এবং test করে জানান!** 🚀
