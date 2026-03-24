# 📧 Email Setup করুন - Step by Step Guide

## ⚠️ Current Status

**Frontend (Vercel):** ✅ Working perfectly
**WordPress API:** ❌ Not uploaded yet
**Fluent Forms:** ❌ Fields not setup
**Email:** ❌ Can't send (needs above 2 steps)

---

## 🎯 3 Steps to Fix Email

### Step 1: Upload API File to WordPress
### Step 2: Setup Fluent Forms Fields (28 total)
### Step 3: Configure Email Notification

---

# Step 1: Upload API File to WordPress

## Method A: Using WordPress File Manager (Easiest)

### 1. Open File Manager
```
WordPress Admin → Tools → File Manager
OR
cPanel → File Manager
```

### 2. Navigate to Location
```
/public_html/wp-content/themes/travel-agency/
```

### 3. Create Folder (if doesn't exist)
```
Right click → New Folder
Name: visa-checker-api
```

### 4. Upload File
```
Open folder: visa-checker-api
Upload: api-final-with-email.php
Rename to: api.php
```

### 5. Set Permissions
```
Right click api.php → Permissions
Set to: 644
```

---

## Method B: Using Theme File Editor

### 1. Go to Theme Editor
```
WordPress Admin → Appearance → Theme File Editor
```

### 2. Select Theme
```
Select Theme: travel-agency (Active)
```

### 3. Create New File
```
Right side → Templates
Add New Template File
Name: visa-checker-api/api.php
```

### 4. Copy-Paste Code
```
Open: api-final-with-email.php
Copy ALL code (line 1 to end)
Paste in Theme Editor
Save
```

---

## ✅ Verify API is Working

### Test Endpoint
Open in browser:
```
https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
```

**Expected Response:**
```json
{
  "success": true,
  "message": "API is ready!",
  "data": {
    "wordpress_version": "X.X.X",
    "fluent_forms_active": "Yes",
    "email_hooks": "Enhanced notification system enabled"
  }
}
```

**If you see this ✅ API uploaded successfully!**

---

# Step 2: Setup Fluent Forms Fields

## Go to Fluent Forms Editor

```
WordPress Admin → Fluent Forms → All Forms
Click on: Form ID 12 (your visa form)
Click: Edit
```

## Add 28 Fields (7 Basic + 21 Q&A)

### Basic Fields (7 total)

#### Field 1: Name
```
Type: Single Line Text
Label: Name
Admin Field Label: input_name
Placeholder: Full Name
Required: Yes
```

#### Field 2: Phone
```
Type: Phone
Label: Phone Number
Admin Field Label: input_phone
Placeholder: 01XXXXXXXXX
Required: Yes
Validation: 11 digits
```

#### Field 3: Email
```
Type: Email
Label: Email Address
Admin Field Label: input_email
Placeholder: example@email.com
Required: No
```

#### Field 4: Country ID
```
Type: Single Line Text
Label: Country ID
Admin Field Label: input_country_id
Placeholder: e.g., nepal
Required: No
```

#### Field 5: Difficulty
```
Type: Single Line Text
Label: Visa Category
Admin Field Label: input_difficulty
Placeholder: easy/medium/hard/very-hard
Required: No
```

#### Field 6: Score
```
Type: Numeric
Label: Score
Admin Field Label: input_score
Placeholder: e.g., 8
Required: No
```

#### Field 7: Percentage
```
Type: Numeric
Label: Success Rate %
Admin Field Label: input_percentage
Placeholder: e.g., 80
Required: No
```

---

### Question/Answer Fields (21 fields = 10 pairs + 1 extra)

**Pattern:** For each Q (1-10), add 2 fields:
- Question field (Paragraph Text)
- Answer field (Single Line Text)

#### Q1 Pair:

**Field 8: Q1 Question**
```
Type: Paragraph Text
Label: Question 1
Admin Field Label: input_q1_question
Rows: 2
Required: No
```

**Field 9: Q1 Answer**
```
Type: Single Line Text
Label: Answer 1
Admin Field Label: input_q1_answer
Required: No
```

#### Q2 Pair:

**Field 10: Q2 Question**
```
Type: Paragraph Text
Label: Question 2
Admin Field Label: input_q2_question
Rows: 2
Required: No
```

**Field 11: Q2 Answer**
```
Type: Single Line Text
Label: Answer 2
Admin Field Label: input_q2_answer
Required: No
```

#### Q3-Q10 Pairs:

**Repeat same pattern:**
```
Field 12-13: Q3 (input_q3_question, input_q3_answer)
Field 14-15: Q4 (input_q4_question, input_q4_answer)
Field 16-17: Q5 (input_q5_question, input_q5_answer)
Field 18-19: Q6 (input_q6_question, input_q6_answer)
Field 20-21: Q7 (input_q7_question, input_q7_answer)
Field 22-23: Q8 (input_q8_question, input_q8_answer)
Field 24-25: Q9 (input_q9_question, input_q9_answer)
Field 26-27: Q10 (input_q10_question, input_q10_answer)
```

**Total: 28 fields**

### ⚠️ Important Notes:

1. **Admin Field Label must match exactly:**
   - input_name (not Name or name)
   - input_phone (not Phone)
   - input_q1_question (not question1)

2. **Case sensitive:**
   - Use lowercase
   - Use underscores

3. **Question fields:**
   - Use Paragraph Text (not Single Line)
   - Minimum 2 rows

4. **All Q&A fields:**
   - Make them NOT required
   - Some users may answer fewer questions

---

# Step 3: Configure Email Notification

## Go to Email Notifications

```
Fluent Forms → Edit Form 12 → Settings & Integrations → Email Notifications
```

## Add New Notification

### Basic Settings:

**Name:**
```
New Visa Lead Notification
```

**Send To Email:**
```
goflybd@gmail.com
```

**From Email:**
```
wordpress@goflybd.com
OR
noreply@goflybd.com
```

**Email Subject:**
```
🎉 New Visa Lead: {input_name} - {input_country_id}
```

---

### Email Body Template:

```
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; }
        .section { background: #f9fafb; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .field { margin: 10px 0; }
        .label { font-weight: bold; color: #374151; }
        .value { color: #1f2937; }
        .score { font-size: 24px; font-weight: bold; color: #10b981; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 New Visa Lead Received!</h1>
        </div>

        <div class="section">
            <h2>📋 Basic Information</h2>
            <div class="field">
                <span class="label">Name:</span>
                <span class="value">{input_name}</span>
            </div>
            <div class="field">
                <span class="label">Phone:</span>
                <span class="value">{input_phone}</span>
            </div>
            <div class="field">
                <span class="label">Email:</span>
                <span class="value">{input_email}</span>
            </div>
        </div>

        <div class="section">
            <h2>🌍 Visa Details</h2>
            <div class="field">
                <span class="label">Country:</span>
                <span class="value">{input_country_id}</span>
            </div>
            <div class="field">
                <span class="label">Category:</span>
                <span class="value">{input_difficulty}</span>
            </div>
            <div class="field">
                <span class="label">Score:</span>
                <span class="score">{input_score} ({input_percentage}%)</span>
            </div>
        </div>

        <div class="section">
            <h2>❓ Questions & Answers</h2>

            <div class="field">
                <p><strong>Q1:</strong> {input_q1_question}</p>
                <p><strong>A1:</strong> {input_q1_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q2:</strong> {input_q2_question}</p>
                <p><strong>A2:</strong> {input_q2_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q3:</strong> {input_q3_question}</p>
                <p><strong>A3:</strong> {input_q3_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q4:</strong> {input_q4_question}</p>
                <p><strong>A4:</strong> {input_q4_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q5:</strong> {input_q5_question}</p>
                <p><strong>A5:</strong> {input_q5_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q6:</strong> {input_q6_question}</p>
                <p><strong>A6:</strong> {input_q6_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q7:</strong> {input_q7_question}</p>
                <p><strong>A7:</strong> {input_q7_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q8:</strong> {input_q8_question}</p>
                <p><strong>A8:</strong> {input_q8_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q9:</strong> {input_q9_question}</p>
                <p><strong>A9:</strong> {input_q9_answer}</p>
            </div>

            <div class="field">
                <p><strong>Q10:</strong> {input_q10_question}</p>
                <p><strong>A10:</strong> {input_q10_answer}</p>
            </div>
        </div>

        <div class="section">
            <p><strong>📅 Submitted:</strong> {submission.created_at}</p>
            <p><strong>🔗 View in WordPress:</strong>
            <a href="https://goflybd.com/wp-admin/admin.php?page=fluent_forms_entries&form_id=12">View Entries</a></p>
        </div>
    </div>
</body>
</html>
```

### Advanced Settings:

**Conditional Logic:** None
**Status:** Active ✅

**Save notification!**

---

# Testing Complete Flow

## Step 1: Submit Test Lead

1. Go to: https://gofly-visa-eligibility-checker.vercel.app/
2. Select country: Nepal
3. Answer all questions
4. Fill lead form:
   - Name: Test User
   - Phone: 01712345678
   - Email: test@example.com
5. Submit

## Step 2: Check WordPress

```
Go to: WordPress Admin → Fluent Forms → Entries
Select: Form ID 12
```

**You should see:**
- ✅ New entry with test data
- ✅ Name, Phone, Email filled
- ✅ Country, Score, Percentage filled
- ✅ Questions and answers in Bengali

## Step 3: Check Email

```
Check: goflybd@gmail.com
Subject: 🎉 New Visa Lead: Test User - nepal
```

**Email should contain:**
- ✅ Name, Phone, Email
- ✅ Country, Category, Score
- ✅ All questions and answers

## Step 4: Check Spam Folder

If email not in inbox:
- Check Spam/Junk folder
- Add wordpress@goflybd.com to contacts

---

# Troubleshooting

## Issue 1: API Not Working

**Check:**
```
https://goflybd.com/wp-content/themes/travel-agency/visa-checker-api/api.php
```

**If 404 error:**
- File not uploaded correctly
- Wrong location
- File permissions

**Solution:**
- Re-upload api.php
- Check exact path
- Set permissions to 644

---

## Issue 2: Data Not Saving

**Check Console:**
```
Open Vercel site
Press F12 → Console tab
Submit a lead
Look for errors
```

**Common errors:**
- CORS error → API file issue
- 500 error → PHP error in api.php
- Network error → API path wrong

**Solution:**
- Check API file uploaded
- Check PHP error logs
- Verify API endpoint URL

---

## Issue 3: Email Not Sending

**Check SMTP:**
```
WordPress → Fluent Forms → Settings → Email Settings
```

**Verify:**
- ✅ SMTP configured (you said it's already working)
- ✅ Test email works
- ✅ From email valid

**Check notification:**
```
Fluent Forms → Form 12 → Settings → Email Notifications
```

**Verify:**
- ✅ Notification is Active
- ✅ Recipient email correct
- ✅ Subject has placeholders

**Solution:**
- Send test email from Fluent Forms
- Check spam folder
- Verify SMTP settings

---

# Quick Checklist

- [ ] API file uploaded to correct location
- [ ] API endpoint returns success message
- [ ] 28 fields added to Fluent Forms
- [ ] Field labels match exactly (input_name, etc.)
- [ ] Email notification created
- [ ] Email recipient is goflybd@gmail.com
- [ ] Notification is Active
- [ ] Test submission works
- [ ] Data appears in WordPress entries
- [ ] Email received (check spam too)

---

# Summary

**3 Steps:**
1. Upload `api-final-with-email.php` → rename to `api.php`
2. Add 28 fields to Fluent Forms Form ID 12
3. Configure email notification with template

**After setup:**
- Frontend sends data to WordPress ✅
- WordPress saves to Fluent Forms ✅
- Email notification sent ✅
- You receive lead details ✅

**Files you need:**
- api-final-with-email.php (already in repository)
- Email template (above)
- Field list (above)

এখন আপনি WordPress এ গিয়ে setup শুরু করতে পারেন! 🚀
