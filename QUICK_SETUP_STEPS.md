# ⚡ Quick Setup - 5 Minutes

## 🎯 Goal: WordPress এ leads automatically save করা

---

## 📝 Step 1: Backend Upload (2 min)

1. **Download:** `wordpress-backend.php` file
2. **Upload to:** `/wp-content/themes/YOUR-THEME/visa-checker-api/api.php`
3. **Edit:** Form ID change করুন (line 15)

```php
define('FLUENT_FORMS_ID', 123); // আপনার Form ID দিন
```

---

## 📝 Step 2: Fluent Forms Setup (2 min)

**WordPress Admin → Fluent Forms → Add New Form**

**Fields যোগ করুন:**
- Name (`input_name`) - Required
- Phone (`input_phone`) - Required
- Email (`input_email`) - Optional
- Country (`input_country`)
- Score (`input_score`)
- Percentage (`input_percentage`)

**Form ID নোট করুন:** (URL থেকে - যেমন: `form_id=123`)

---

## 📝 Step 3: Frontend Update (1 min)

**index.html এ এই code যোগ করুন:**

`submitLead` function এর পরে এটা add করুন:

```javascript
async function submitToWordPress(leadInfo) {
    const WORDPRESS_URL = 'https://goflybd.com';
    const THEME_NAME = 'your-theme'; // 🔧 Change this!

    try {
        const response = await fetch(
            `${WORDPRESS_URL}/wp-content/themes/${THEME_NAME}/visa-checker-api/api.php`,
            {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    name: leadInfo.name,
                    phone: leadInfo.phone,
                    email: leadInfo.email,
                    country: leadInfo.country,
                    score: leadInfo.score,
                    percentage: leadInfo.percentage
                })
            }
        );

        const result = await response.json();
        console.log(result.success ? '✅ Saved!' : '❌ Failed');
    } catch (error) {
        console.error('❌ Error:', error);
    } finally {
        currentStep = 'result';
        saveSession();
        render();
        hideLoading();
    }
}
```

**এবং `submitLead` function এ change করুন:**

```javascript
// এই line টা:
setTimeout(() => {
    currentStep = 'result';
    saveSession();
    render();
    hideLoading();
}, 300);

// Replace করুন এটা দিয়ে:
submitToWordPress(leadInfo);
```

---

## ✅ Test করুন:

1. Deploy করুন:
   ```bash
   git add index.html
   git commit -m "Add backend integration"
   git push
   ```

2. Live site এ test lead submit করুন

3. Check করুন:
   ```
   WordPress → Fluent Forms → Entries
   ```

---

## 🔧 Theme Name কিভাবে জানবেন:

**cPanel:**
```
File Manager → /wp-content/themes/ → folder name
```

**Common theme names:**
- `astra`
- `oceanwp`
- `generatepress`
- `kadence`
- `twentytwentyfour`

---

## 📧 Email Notification (Optional):

**Fluent Forms → Your Form → Settings → Email Notifications**

```
To: your@email.com
Subject: New Visa Lead: {inputs.country}

Message:
Name: {inputs.input_name}
Phone: {inputs.input_phone}
Country: {inputs.input_country}
Score: {inputs.input_score}%
```

---

## ⚠️ Common Issues:

**CORS Error?**
Add to api.php top:
```php
header('Access-Control-Allow-Origin: *');
```

**Not saving?**
Check:
1. ✅ Form ID correct?
2. ✅ Theme name correct?
3. ✅ File uploaded to right path?

**Browser Console:**
```
F12 → Console → Check for errors
F12 → Network → See API response
```

---

## 🎉 Done!

Leads এখন automatically WordPress এ save হবে! 🚀

**Full Guide:** `FLUENT_FORMS_SETUP_BENGALI.md`
