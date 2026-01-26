# 🔍 Email কাজ না করার Diagnosis & Fix

## প্রথমে বুঝতে হবে কোথায় সমস্যা

Email কাজ করার জন্য **3টা জিনিস** লাগে:

```
1️⃣ Frontend (Vercel) → Lead form দেখাবে ✅ or ❌?
         ↓
2️⃣ WordPress API → Data receive করবে ✅ or ❌?
         ↓
3️⃣ Fluent Forms → Email পাঠাবে ✅ or ❌?
```

চলুন check করি কোথায় আটকে আছে!

---

## Step 1: Vercel Version Check করুন

### Test করুন:

1. **Open:** https://gofly-visa-eligibility-checker.vercel.app/

2. **Country Select করুন:** Nepal

3. **Questions উত্তর দিন:** সব questions এর উত্তর দিন

4. **এখন দেখুন:**

#### ✅ যদি **Lead Capture Form** দেখেন:
```
Form দেখাবে যেখানে আছে:
- Name field
- Phone field
- Email field
- Submit button

➡️ Good! Frontend ঠিক আছে
➡️ এগিয়ে যান Step 2 এ
```

#### ❌ যদি **Direct Result** দেখেন (Form নেই):
```
Questions এর পর সরাসরি result দেখাচ্ছে
কোনো lead form নেই

➡️ Problem! Vercel এ পুরানো version আছে
➡️ Solution নিচে দেখুন
```

---

## যদি Lead Form না দেখেন (পুরানো version)

### Vercel এ Manual Deploy করুন:

1. **Vercel Dashboard খুলুন:**
   ```
   https://vercel.com/dashboard
   ```

2. **Project Select:**
   ```
   gofly-visa-eligibility-checker
   ```

3. **Deployments Tab:**
   ```
   Click: Deployments (top menu)
   ```

4. **Latest Deployment:**
   ```
   Find latest deployment
   Click: 3 dots (⋮) menu
   Select: Redeploy
   ```

5. **Redeploy Options:**
   ```
   ❌ UNCHECK: "Use existing Build Cache"
   ✅ Click: Redeploy
   ```

6. **Wait:**
   ```
   ⏳ 1-2 minutes
   ```

7. **Test Again:**
   ```
   Go back to site
   Hard refresh: Ctrl+Shift+R
   Try flow again
   ```

---

## Step 2: Lead Form Submit করুন & Console Check

### যদি Lead Form দেখেন:

1. **Browser Console খুলুন:**
   ```
   Press: F12
   Click: Console tab
   ```

2. **Form Fill করুন:**
   ```
   Name: Test User
   Phone: 01712345678
   Email: test@example.com
   ```

3. **Submit করুন:**
   ```
   Click: Submit button
   ```

4. **Console দেখুন কি বলে:**

#### ✅ Success Message:
```
Console shows:
"✅ Lead saved successfully!"
or
{"success": true, "message": "..."}

➡️ Good! WordPress API working
➡️ Check Step 3
```

#### ❌ CORS Error:
```
Console shows:
"CORS policy: No 'Access-Control-Allow-Origin'"
or
"Failed to fetch"

➡️ Problem! API file not uploaded or wrong location
➡️ Fix: Upload api.php (guide below)
```

#### ❌ 404 Error:
```
Console shows:
"404 Not Found"
or
"GET https://goflybd.com/.../api.php 404"

➡️ Problem! API file missing
➡️ Fix: Upload api.php to correct location
```

#### ❌ 500 Error:
```
Console shows:
"500 Internal Server Error"

➡️ Problem! PHP error in api.php
➡️ Fix: Check PHP error logs or re-upload api.php
```

#### ❌ Network Error:
```
Console shows:
"Network request failed"
or
"ERR_CONNECTION_REFUSED"

➡️ Problem! Can't reach WordPress
➡️ Check: WordPress site is accessible
```

---

## Step 3: WordPress API Check করুন

### Test API Endpoint Directly:

**Open in browser:**
```
https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
```

#### ✅ Expected (Good):
```json
{
  "success": true,
  "message": "API is ready!",
  "data": {
    "wordpress_version": "6.x.x",
    "fluent_forms_active": "Yes",
    "email_hooks": "Enhanced notification system enabled"
  }
}
```
**➡️ API uploaded correctly! Go to Step 4**

#### ❌ 404 Error (Bad):
```
404 Not Found
or
Page not found
```
**➡️ API not uploaded or wrong location**

**Fix: Upload API File**

---

## 📤 How to Upload api.php to WordPress

### Method 1: cPanel File Manager (Recommended)

1. **Login to cPanel:**
   ```
   Your hosting cPanel URL
   ```

2. **Open File Manager:**
   ```
   cPanel → Files section → File Manager
   ```

3. **Navigate:**
   ```
   /public_html/wp-content/themes/travel-agency/
   ```

4. **Create Folder:**
   ```
   Right click → New Folder
   Name: visa-checker-api
   Click: Create New Folder
   ```

5. **Enter Folder:**
   ```
   Double click: visa-checker-api
   ```

6. **Upload File:**
   ```
   Click: Upload button (top menu)
   Select: api-final-with-email.php (from repository)
   Wait for upload
   ```

7. **Rename File:**
   ```
   Find: api-final-with-email.php
   Right click → Rename
   New name: api.php
   Save
   ```

8. **Set Permissions:**
   ```
   Right click: api.php
   Click: Permissions
   Set: 644
   Click: Change Permissions
   ```

9. **Test:**
   ```
   Open: https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
   Should see: {"success": true, ...}
   ```

---

### Method 2: WordPress Theme Editor

1. **WordPress Admin:**
   ```
   Login to: https://goflybd.com/wp-admin
   ```

2. **Go to Editor:**
   ```
   Appearance → Theme File Editor
   ```

3. **Select Theme:**
   ```
   Select Theme: travel-agency
   (Active theme)
   ```

4. **Add New File:**
   ```
   Right sidebar → Theme Files
   Look for: Add New Template
   (or similar option)
   ```

5. **Create File:**
   ```
   Name: visa-checker-api/api.php
   ```

6. **Open api-final-with-email.php:**
   ```
   From repository folder
   Open in text editor
   ```

7. **Copy All Code:**
   ```
   Select All (Ctrl+A)
   Copy (Ctrl+C)
   ```

8. **Paste in WordPress:**
   ```
   Paste in the new file
   Click: Update File or Save
   ```

9. **Test:**
   ```
   https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
   ```

---

## Step 4: Check WordPress Entries

### After API is working:

1. **Submit Test Lead:**
   ```
   Vercel site → Fill form → Submit
   Console shows: Success
   ```

2. **Check WordPress:**
   ```
   WordPress Admin → Fluent Forms → Entries
   Click: Form 12 (or your form name)
   ```

#### ✅ যদি Entry দেখেন:
```
You see new entry with:
- Name: Test User
- Phone: 01712345678
- Email: test@example.com

➡️ Great! Data saving to WordPress
➡️ Now check email (Step 5)
```

#### ❌ যদি Entry না দেখেন:
```
No entries showing
or
Empty list

➡️ Problem! Data not reaching Fluent Forms
➡️ Check: Fluent Forms fields setup (Step 6)
```

---

## Step 5: Check Email

### If Data is Saving in WordPress:

1. **Check Inbox:**
   ```
   Email: goflybd@gmail.com
   Look for: New email about visa lead
   ```

2. **Check Spam:**
   ```
   Gmail → Spam folder
   Search: "visa" or "lead" or "goFLY"
   ```

3. **Check Email Settings:**
   ```
   WordPress Admin → Fluent Forms
   Form 12 → Settings → Email Notifications
   ```

#### ✅ যদি Email আসে:
```
Perfect! Everything working!
🎉 Setup complete!
```

#### ❌ যদি Email না আসে:
```
Data saving but no email

➡️ Problem! Email notification not configured
➡️ Fix: Configure notification (Step 7)
```

---

## Step 6: Fluent Forms Fields Check

### Verify Fields Are Setup:

1. **Go to Form Editor:**
   ```
   WordPress → Fluent Forms → All Forms
   Find: Form 12
   Click: Edit
   ```

2. **Check Fields:**
   ```
   Should have these fields (28 total):

   ✅ input_name
   ✅ input_phone
   ✅ input_email
   ✅ input_country_id
   ✅ input_difficulty
   ✅ input_score
   ✅ input_percentage
   ✅ input_q1_question
   ✅ input_q1_answer
   ... (up to q10)
   ```

3. **If Fields Missing:**
   ```
   ➡️ Add them following:
   EMAIL_SETUP_COMPLETE_GUIDE.md
   (Step 2 section)
   ```

---

## Step 7: Email Notification Setup

### Configure Email:

1. **Go to Notifications:**
   ```
   Form 12 → Settings & Integrations → Email Notifications
   ```

2. **Check Existing:**
   ```
   Any notification already there?

   ✅ Yes → Edit it
   ❌ No → Add New
   ```

3. **Configure:**
   ```
   Send To: goflybd@gmail.com
   Subject: 🎉 New Visa Lead: {input_name}
   Body: HTML template (from EMAIL_SETUP_COMPLETE_GUIDE.md)
   Status: Active ✅
   ```

4. **Save:**
   ```
   Click: Save Notification
   ```

5. **Test:**
   ```
   Submit new test lead from Vercel
   Check email
   ```

---

## 🎯 Quick Diagnosis Flowchart

```
Start: Submit lead from Vercel
  ↓
See lead form?
  ├─ NO → Vercel old version → Redeploy Vercel
  └─ YES ↓
        ↓
Console shows success?
  ├─ NO → API error → Upload api.php
  └─ YES ↓
        ↓
Entry in WordPress?
  ├─ NO → Fields missing → Add 28 fields
  └─ YES ↓
        ↓
Email received?
  ├─ NO → Notification not setup → Configure email
  └─ YES → 🎉 DONE!
```

---

## 📋 Complete Checklist

Test each and tell me which step fails:

- [ ] Vercel site shows lead capture form
- [ ] Can fill: Name, Phone, Email
- [ ] Can click Submit button
- [ ] Console shows: Success message (F12 → Console)
- [ ] API test URL works: https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
- [ ] Entry appears in WordPress Fluent Forms
- [ ] Entry has all data (name, phone, questions, answers)
- [ ] Email notification is Active in settings
- [ ] Email received in goflybd@gmail.com (or spam)

---

## 💡 Tell Me Where It Stops

**Test করে আমাকে বলুন:**

1. ✅ Lead form দেখছেন? (YES/NO)
2. ✅ Submit এ click করলে console এ কি দেখায়? (Success/Error/Nothing)
3. ✅ API test link খুললে কি দেখায়? (JSON/404/Error)
4. ✅ WordPress entries তে data আসছে? (YES/NO)
5. ✅ Email আসছে? (YES/NO/SPAM)

**এগুলোর উত্তর দিলে আমি exact problem ধরে fix করে দিতে পারবো!** 🎯

---

## 📄 Reference Guides

- **API Upload:** EMAIL_SETUP_COMPLETE_GUIDE.md (Step 1)
- **Fields Setup:** EMAIL_SETUP_COMPLETE_GUIDE.md (Step 2)
- **Email Template:** EMAIL_SETUP_COMPLETE_GUIDE.md (Step 3)
- **Vercel Deploy:** VERCEL_NOT_UPDATING_FIX.md

All files in repository! ✅
