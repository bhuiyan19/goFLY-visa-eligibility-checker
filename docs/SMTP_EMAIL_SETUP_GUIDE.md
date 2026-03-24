# 📧 WordPress Email Setup Guide - Fluent Forms Notifications

## ⚠️ Problem
Email notifications থেকে পাচ্ছেন না কারণ:

1. **Direct Database Insert**: আমরা Fluent Forms এর normal flow bypass করছি
2. **WordPress Mail Not Configured**: WordPress default mail function কাজ নাও করতে পারে shared hosting এ
3. **SMTP Not Set Up**: Proper email server configuration নেই

---

## ✅ Solution: Install SMTP Plugin

### Step 1: Install WP Mail SMTP Plugin

1. **WordPress Admin → Plugins → Add New**
2. Search করুন: **"WP Mail SMTP"**
3. Install করুন **WP Mail SMTP by WPForms**
4. Activate করুন

---

### Step 2: Configure Gmail SMTP

**WordPress Admin → WP Mail SMTP → Settings**

#### General Settings:
```
From Email: goflybd@gmail.com
From Name: goFLY Visa Checker
Mailer: Gmail / Google Workspace
```

#### Gmail Configuration:

**Option A: Using App Password (Recommended)**

1. **Gmail Account Settings:**
   - Go to: https://myaccount.google.com/security
   - Enable **2-Step Verification** (required)
   - Go to: https://myaccount.google.com/apppasswords
   - Create App Password:
     - Select App: **Mail**
     - Select Device: **Other (Custom name)**
     - Name: **WordPress goFLY**
   - Copy the 16-character password

2. **WP Mail SMTP Settings:**
   ```
   SMTP Host: smtp.gmail.com
   SMTP Port: 587
   Encryption: TLS
   Authentication: ON
   Username: goflybd@gmail.com
   Password: [Your 16-character App Password]
   ```

**Option B: Using OAuth (More Secure)**

1. Create Google Cloud Project
2. Enable Gmail API
3. Get OAuth credentials
4. Follow WP Mail SMTP wizard

---

### Step 3: Test Email

1. **WP Mail SMTP → Email Test**
2. Send To: `goflybd@gmail.com`
3. Click **Send Email**
4. Check inbox and spam folder

---

### Step 4: Update API File

1. **cPanel → File Manager**
2. Navigate to: `/wp-content/themes/travel-agency/visa-checker-api/`
3. **Backup current api.php:**
   - Rename `api.php` → `api-backup-old.php`
4. **Upload new file:**
   - Upload `api-with-email-notifications.php`
   - Rename it to `api.php`

---

## 🎯 Alternative: Other SMTP Options

### Option 1: SendGrid (Free 100 emails/day)
```
SMTP Host: smtp.sendgrid.net
SMTP Port: 587
Encryption: TLS
Username: apikey
Password: [Your SendGrid API Key]
```

### Option 2: Mailgun (Free 5000 emails/month)
```
SMTP Host: smtp.mailgun.org
SMTP Port: 587
Encryption: TLS
Username: [Your Mailgun SMTP Username]
Password: [Your Mailgun SMTP Password]
```

### Option 3: Brevo (ex-Sendinblue) (Free 300 emails/day)
```
SMTP Host: smtp-relay.brevo.com
SMTP Port: 587
Encryption: TLS
Username: [Your Brevo Email]
Password: [Your Brevo SMTP Key]
```

---

## 🔍 Troubleshooting

### Email Still Not Coming?

#### 1. Check Spam Folder
- Gmail এ spam/junk folder check করুন
- If found, mark as "Not Spam"

#### 2. Check Fluent Forms Email Settings
```
WordPress Admin → Fluent Forms → Settings → Email Notifications
✅ Verify "Admin Notification Email" is ENABLED
✅ Check email address: goflybd@gmail.com
```

#### 3. Test with Direct Form Submission
- Go to form: https://goflybd.com/wp-admin/admin.php?page=fluent_forms&form_id=12
- Add test entry directly in WordPress
- See if email arrives
- This confirms SMTP is working

#### 4. Enable Fluent Forms Email Logs
```
Fluent Forms → Settings → Advanced
✅ Enable Email Notifications Logging
```

Then check logs after lead submission.

#### 5. Verify API Triggers Notifications
```javascript
// Test in browser console after submitting form:
// Check response data
{
  "success": true,
  "message": "Lead saved! Email notification sent.",
  "data": {
    "notification_info": "Check your email (goflybd@gmail.com) and spam folder"
  }
}
```

---

## 📝 Email Template Customization

### Current Template (in your screenshot):
```
Subject: [{inputs.names}] New Form Submission

Body:
{all_data}
This form submitted at: {embed_post.permalink}
```

### Recommended Template:
```
Subject: 🎯 New Visa Lead - {inputs.input_name} - {inputs.input_country}

Body:
নতুন Visa Eligibility Lead পেয়েছেন!

👤 Customer Information:
Name: {inputs.input_name}
Phone: {inputs.input_phone}
Email: {inputs.input_email}

🌍 Visa Details:
Country: {inputs.input_country}
Eligibility Score: {inputs.input_percentage}%
Difficulty: {inputs.input_difficulty}

📋 Questions & Answers:
{all_data}

🔗 View Entry: {embed_post.permalink}

---
Submitted at: {submission.created_at}
```

---

## 🚀 Quick Start Checklist

- [ ] Install WP Mail SMTP plugin
- [ ] Configure Gmail App Password
- [ ] Test email delivery
- [ ] Upload new api-with-email-notifications.php
- [ ] Rename to api.php
- [ ] Submit test lead from live site
- [ ] Check email inbox (and spam)
- [ ] Verify lead appears in Fluent Forms → Entries

---

## 🎉 Expected Result

After setup, when someone submits the visa checker form:

1. ✅ Lead saves to WordPress database
2. ✅ Entry appears in Fluent Forms → Entries
3. ✅ Email notification sends to goflybd@gmail.com
4. ✅ Email contains all question/answer details
5. ✅ You can follow up with the lead

---

**Need Help?**
- Check WP Mail SMTP Debug Log
- Check Fluent Forms Email Log
- Verify Gmail App Password is correct
- Test with simple WordPress email first
