# Fluent Forms Setup Guide (Bengali)
## goFLY Visa Eligibility Checker এর জন্য সহজ Setup

**Date:** January 24, 2026
**Status:** Step-by-Step Guide

---

## 🎯 কী করবো:

আপনার Visa Checker থেকে যখন কেউ lead submit করবে, সেটা automatically WordPress এ Fluent Forms এ save হবে।

**Result:**
- ✅ Lead data WordPress database এ save হবে
- ✅ Email notification পাবেন
- ✅ Fluent Forms dashboard এ সব leads দেখতে পারবেন
- ✅ CRM integration করতে পারবেন পরে

---

## 📋 যা লাগবে:

- ✅ WordPress site (goflybd.com)
- ✅ Fluent Forms Pro plugin (আপনার কাছে আছে - screenshot দেখেছি)
- ✅ FTP/File Manager access (cPanel বা Hosting)
- ✅ 15-20 minutes

---

## 🚀 Method 1: Simple Custom Endpoint (সবচেয়ে সহজ)

এটা সবচেয়ে easy এবং reliable। Fluent Forms API এর দরকার নেই।

### Step 1: WordPress এ Backend Code Upload করুন

**1.1 File Download করুন:**
- File: `wordpress-backend.php` (already আপনার GitHub repository তে আছে)
- Location: `/home/user/goFLY-visa-eligibility-checker/wordpress-backend.php`

**1.2 cPanel File Manager এ যান:**
```
cPanel → File Manager → public_html/wp-content/themes/your-theme/
```

**1.3 নতুন folder তৈরি করুন:**
```
Right-click → New Folder → "visa-checker-api"
```

**1.4 wordpress-backend.php upload করুন:**
```
visa-checker-api folder এ → Upload → wordpress-backend.php select করুন
```

**1.5 File rename করুন:**
```
wordpress-backend.php → api.php (rename করুন)
```

**Final path হবে:**
```
/wp-content/themes/your-theme/visa-checker-api/api.php
```

---

### Step 2: Fluent Forms এ Form তৈরি করুন

**2.1 WordPress Admin যান:**
```
goflybd.com/wp-admin → Fluent Forms → Add New Form
```

**2.2 Form Title দিন:**
```
"Visa Eligibility Checker Leads"
```

**2.3 এই fields যোগ করুন (drag & drop):**

1. **Name** (Text Input)
   - Label: "Name"
   - Field Name: `input_name`
   - Required: Yes

2. **Phone** (Phone Number)
   - Label: "Phone"
   - Field Name: `input_phone`
   - Required: Yes

3. **Email** (Email)
   - Label: "Email"
   - Field Name: `input_email`
   - Required: No

4. **Country** (Text Input)
   - Label: "Country"
   - Field Name: `input_country`
   - Required: Yes

5. **Score** (Numeric)
   - Label: "Score"
   - Field Name: `input_score`
   - Required: No

6. **Percentage** (Numeric)
   - Label: "Percentage"
   - Field Name: `input_percentage`
   - Required: No

**2.4 Form Save করুন এবং Form ID নোট করুন:**
```
URL দেখুন: .../form-builder/?form_id=123
এখানে 123 হলো আপনার Form ID
```

---

### Step 3: api.php File Edit করুন

**3.1 File Manager এ api.php open করুন:**
```
File Manager → visa-checker-api/api.php → Right-click → Edit
```

**3.2 এই line খুঁজুন (line 15 এর কাছে):**
```php
define('FLUENT_FORMS_ID', 123); // Change this to your form ID
```

**3.3 আপনার Form ID দিয়ে replace করুন:**
```php
define('FLUENT_FORMS_ID', 456); // আপনার actual Form ID দিন
```

**3.4 Save করুন**

---

### Step 4: Frontend (index.html) Update করুন

**4.1 index.html file খুলুন (local computer এ বা GitHub এ)**

**4.2 `submitLead` function খুঁজুন (line 944 এর কাছে):**

এই পুরো function টা replace করুন:

```javascript
function submitLead(event) {
    event.preventDefault();

    // Validate required fields
    if (!leadData.name || !leadData.phone) {
        alert('দয়া করে নাম এবং মোবাইল নম্বর দিন');
        return;
    }

    // Validate phone format
    if (!/^[0-9]{11}$/.test(leadData.phone)) {
        alert('দয়া করে সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন');
        return;
    }

    showLoading();

    // Prepare lead data
    const leadInfo = {
        name: leadData.name,
        phone: leadData.phone,
        email: leadData.email || '',
        country: selectedCountry.nameBn,
        countryId: selectedCountry.id,
        score: score,
        percentage: Math.round((score / QUESTIONS[selectedCountry.difficulty].maxScore) * 100),
        timestamp: new Date().toISOString()
    };

    // Store in localStorage as backup
    const leads = JSON.parse(localStorage.getItem('visaLeads') || '[]');
    leads.push(leadInfo);
    localStorage.setItem('visaLeads', JSON.stringify(leads));

    // Submit to WordPress backend
    submitToWordPress(leadInfo);
}

async function submitToWordPress(leadInfo) {
    // 🔧 CHANGE THIS to your WordPress site URL
    const WORDPRESS_URL = 'https://goflybd.com';

    try {
        const response = await fetch(`${WORDPRESS_URL}/wp-content/themes/your-theme/visa-checker-api/api.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: leadInfo.name,
                phone: leadInfo.phone,
                email: leadInfo.email,
                country: leadInfo.country,
                score: leadInfo.score,
                percentage: leadInfo.percentage
            })
        });

        const result = await response.json();

        if (result.success) {
            console.log('✅ Lead saved successfully!', result);
            // Move to result page
            currentStep = 'result';
            saveSession();
            render();
        } else {
            console.error('❌ Failed to save lead:', result.message);
            // Still show result to user (data is in localStorage backup)
            currentStep = 'result';
            saveSession();
            render();
        }
    } catch (error) {
        console.error('❌ Network error:', error);
        // Still show result to user (data is in localStorage backup)
        currentStep = 'result';
        saveSession();
        render();
    } finally {
        hideLoading();
    }
}
```

**4.3 Important: Theme name change করুন:**

এই line টা খুঁজুন:
```javascript
const WORDPRESS_URL = 'https://goflybd.com';
```

এবং নিচের line এ আপনার actual theme name দিন:
```javascript
fetch(`${WORDPRESS_URL}/wp-content/themes/your-theme/visa-checker-api/api.php`, {
```

**আপনার theme name জানার জন্য:**
```
cPanel → File Manager → /wp-content/themes/ → folder name দেখুন
```

উদাহরণ:
```javascript
// If theme name is "astra"
fetch(`${WORDPRESS_URL}/wp-content/themes/astra/visa-checker-api/api.php`, {

// If theme name is "oceanwp"
fetch(`${WORDPRESS_URL}/wp-content/themes/oceanwp/visa-checker-api/api.php`, {
```

**4.4 Save করুন**

---

### Step 5: Test করুন

**5.1 Local Test (optional):**
```bash
python3 -m http.server 8000
# Open: http://localhost:8000
```

**5.2 Live Test:**
1. Vercel এ deploy করুন:
   ```bash
   git add index.html
   git commit -m "Add Fluent Forms integration"
   git push
   ```

2. Live site এ যান
3. একটা test lead submit করুন:
   - Name: Test User
   - Phone: 01712345678
   - Email: test@example.com
   - Country: Nepal select করুন
   - Questions answer দিন
   - Lead form submit করুন

**5.3 WordPress এ Check করুন:**
```
WordPress Admin → Fluent Forms → Entries
```

আপনার test lead দেখতে পাবেন! ✅

---

### Step 6: Email Notification Setup (Optional)

**6.1 Fluent Forms এ:**
```
Your Form → Settings → Email Notifications
```

**6.2 Admin Notification add করুন:**
```
Send To: your@email.com
Subject: New Visa Lead: {inputs.country}
Message:
Name: {inputs.input_name}
Phone: {inputs.input_phone}
Email: {inputs.input_email}
Country: {inputs.input_country}
Score: {inputs.input_score}
Percentage: {inputs.input_percentage}%
```

**6.3 Save করুন**

এখন প্রতিটা lead এ email পাবেন! 📧

---

## 🔍 Troubleshooting

### সমস্যা 1: "Failed to save lead" error

**Solution:**
1. api.php file path সঠিক আছে কিনা check করুন
2. Browser Console খুলুন (F12) → Network tab দেখুন
3. api.php file এর permissions check করুন (644)

### সমস্যা 2: CORS error

**Solution:**
api.php file এর top এ এটা যোগ করুন:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

### সমস্যা 3: Lead save হচ্ছে না Fluent Forms এ

**Solution:**
1. Form ID সঠিক আছে কিনা check করুন
2. Field names match করছে কিনা check করুন
3. WordPress admin → Fluent Forms → Entries check করুন

### সমস্যা 4: Email আসছে না

**Solution:**
1. WordPress → Settings → General → Admin Email check করুন
2. SMTP plugin install করুন (WP Mail SMTP)
3. Fluent Forms → Settings → Email Notifications verify করুন

---

## 📊 এখন কী কী হবে:

✅ Lead submit করলে automatically WordPress এ save হবে
✅ Email notification পাবেন instantly
✅ Fluent Forms dashboard এ সব leads দেখতে পারবেন
✅ Export করতে পারবেন CSV তে
✅ Integration করতে পারবেন CRM এর সাথে

---

## 🎯 Advanced Features (পরে করতে পারেন):

### 1. WhatsApp Notification
Fluent Forms Pro তে WhatsApp integration আছে। Setup করলে leads এর WhatsApp notification পাবেন।

### 2. Google Sheets Integration
Automatically leads Google Sheets এ sync করতে পারবেন।

### 3. CRM Integration
Fluent Forms integrate করতে পারে:
- Salesforce
- HubSpot
- Zoho CRM
- Custom webhooks

### 4. Lead Scoring
Fluent Forms এ conditional logic দিয়ে lead quality score করতে পারবেন।

---

## 📞 Support

**যদি কোনো সমস্যা হয়:**
1. Browser Console (F12) এ error দেখুন
2. WordPress debug log check করুন
3. api.php file এর error log দেখুন

**Files Location:**
- Backend: `/wp-content/themes/your-theme/visa-checker-api/api.php`
- Frontend: `index.html` (Vercel hosted)
- Documentation: `FLUENT_FORMS_INTEGRATION.md` (detailed guide)

---

## ✅ Setup Complete Checklist:

- [ ] wordpress-backend.php uploaded as api.php
- [ ] Form created in Fluent Forms (Form ID noted)
- [ ] Form ID updated in api.php
- [ ] Theme name updated in index.html
- [ ] submitLead function updated in index.html
- [ ] Code committed and pushed to GitHub
- [ ] Vercel deployed automatically
- [ ] Test lead submitted successfully
- [ ] Lead visible in Fluent Forms → Entries
- [ ] Email notification received (optional)

---

**🎉 Setup হয়ে গেলে সব leads automatically WordPress এ আসবে!**

**Next:** Test lead submit করে দেখান screenshot! 📸
