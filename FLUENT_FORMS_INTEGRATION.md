# Fluent Forms Integration Guide

## Overview
This guide shows how to integrate the goFLY Visa Eligibility Checker with Fluent Forms to automatically save leads to your WordPress database.

---

## Prerequisites

- ✅ WordPress website with Fluent Forms installed
- ✅ Fluent Forms Pro (for REST API access) - **Recommended**
- ✅ Basic PHP knowledge (for Method 2 & 3)
- ✅ SSL certificate (HTTPS) for secure data transmission

---

## Method 1: Fluent Forms REST API (Recommended) ⭐

### Step 1: Create Form in Fluent Forms

1. Go to WordPress Admin → **Fluent Forms → Add New Form**
2. Create a form with these fields:
   - **Name** (Text Input)
   - **Phone** (Phone Number)
   - **Email** (Email Input)
   - **Country** (Text Input / Dropdown)
   - **Score** (Numeric Field)
   - **Percentage** (Numeric Field)

3. Note your **Form ID** (e.g., `123`)

### Step 2: Enable REST API in Fluent Forms

1. Go to **Fluent Forms → Settings → Advanced**
2. Enable **"Allow REST API Access"**
3. Generate an **API Key**
4. Save the API Key securely

### Step 3: Update index.html

Replace the `submitLead` function in `index.html`:

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

    // Submit to Fluent Forms
    submitToFluentForms(leadInfo);
}

async function submitToFluentForms(leadInfo) {
    const WORDPRESS_URL = 'https://goflybd.com'; // Your WordPress site URL
    const FORM_ID = '123'; // Your Fluent Forms form ID
    const API_KEY = 'your_api_key_here'; // Your Fluent Forms API key

    try {
        // Prepare form data matching Fluent Forms field names
        const formData = {
            'names[first_name]': leadInfo.name, // Adjust field name to match your form
            'input_phone': leadInfo.phone,
            'input_email': leadInfo.email,
            'input_country': leadInfo.country,
            'input_score': leadInfo.score,
            'input_percentage': leadInfo.percentage
        };

        // Submit to Fluent Forms REST API
        const response = await fetch(`${WORDPRESS_URL}/wp-json/fluentform/v1/forms/${FORM_ID}/submissions`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${API_KEY}`
            },
            body: JSON.stringify({
                data: formData
            })
        });

        const result = await response.json();

        if (response.ok && result.message === 'success') {
            console.log('✅ Lead submitted to Fluent Forms successfully');

            // Move to result page
            currentStep = 'result';
            saveSession();
            render();
            hideLoading();
        } else {
            throw new Error(result.message || 'Submission failed');
        }

    } catch (error) {
        console.error('❌ Fluent Forms submission error:', error);

        // Still show results even if submission fails
        alert('তথ্য সংরক্ষণে সমস্যা হয়েছে, তবে আপনার ফলাফল দেখতে পারবেন। দয়া করে সরাসরি কল করুন: 01713-289170');

        currentStep = 'result';
        saveSession();
        render();
        hideLoading();
    }
}
```

---

## Method 2: Custom WordPress REST API Endpoint

### Step 1: Create Custom Endpoint in WordPress

Add this code to your theme's `functions.php` or create a custom plugin:

```php
<?php
/**
 * Custom REST API endpoint for goFLY Visa Checker
 */

add_action('rest_api_init', function () {
    register_rest_route('gofly/v1', '/submit-visa-lead', array(
        'methods' => 'POST',
        'callback' => 'gofly_submit_visa_lead',
        'permission_callback' => '__return_true', // Add proper security later
    ));
});

function gofly_submit_visa_lead($request) {
    $params = $request->get_json_params();

    // Validate required fields
    if (empty($params['name']) || empty($params['phone'])) {
        return new WP_Error('missing_fields', 'Name and phone are required', array('status' => 400));
    }

    // Prepare data for Fluent Forms
    $form_id = 123; // Your Fluent Forms form ID

    $form_data = array(
        'names' => array('first_name' => sanitize_text_field($params['name'])),
        'input_phone' => sanitize_text_field($params['phone']),
        'input_email' => sanitize_email($params['email']),
        'input_country' => sanitize_text_field($params['country']),
        'input_score' => intval($params['score']),
        'input_percentage' => intval($params['percentage']),
        'input_timestamp' => sanitize_text_field($params['timestamp'])
    );

    // Submit to Fluent Forms
    $result = wpFluent()->table('fluentform_submissions')
        ->insert(array(
            'form_id' => $form_id,
            'response' => json_encode($form_data),
            'status' => 'read',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));

    if ($result) {
        // Send email notification to admin
        $to = 'info@goflybd.com';
        $subject = '🎯 New Visa Lead: ' . $params['country'] . ' (' . $params['percentage'] . '%)';
        $message = "New visa eligibility lead received:\n\n";
        $message .= "Name: " . $params['name'] . "\n";
        $message .= "Phone: " . $params['phone'] . "\n";
        $message .= "Email: " . $params['email'] . "\n";
        $message .= "Country: " . $params['country'] . "\n";
        $message .= "Score: " . $params['score'] . "/" . $params['maxScore'] . " (" . $params['percentage'] . "%)\n";
        $message .= "Time: " . $params['timestamp'] . "\n\n";
        $message .= "WhatsApp: https://wa.me/88" . $params['phone'] . "\n";

        wp_mail($to, $subject, $message);

        return array(
            'success' => true,
            'message' => 'Lead submitted successfully',
            'lead_id' => $result
        );
    } else {
        return new WP_Error('submission_failed', 'Failed to save lead', array('status' => 500));
    }
}
```

### Step 2: Update index.html

```javascript
async function submitToFluentForms(leadInfo) {
    const WORDPRESS_URL = 'https://goflybd.com';

    try {
        const response = await fetch(`${WORDPRESS_URL}/wp-json/gofly/v1/submit-visa-lead`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(leadInfo)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            console.log('✅ Lead submitted successfully');
            currentStep = 'result';
            saveSession();
            render();
            hideLoading();
        } else {
            throw new Error(result.message || 'Submission failed');
        }

    } catch (error) {
        console.error('❌ Submission error:', error);
        alert('তথ্য সংরক্ষণে সমস্যা হয়েছে। দয়া করে সরাসরি কল করুন: 01713-289170');
        currentStep = 'result';
        saveSession();
        render();
        hideLoading();
    }
}
```

---

## Method 3: Fluent Forms Webhook Integration

### Step 1: Enable Webhooks in Fluent Forms

1. Go to your form → **Settings & Integrations → Webhooks**
2. Click **Add New Webhook**
3. Enter webhook URL: `https://goflybd.com/wp-json/gofly/v1/webhook-receiver`
4. Select trigger: **On Form Submission**

### Step 2: Create Webhook Receiver

Add to `functions.php`:

```php
<?php
add_action('rest_api_init', function () {
    register_rest_route('gofly/v1', '/webhook-receiver', array(
        'methods' => 'POST',
        'callback' => 'gofly_webhook_receiver',
        'permission_callback' => '__return_true',
    ));
});

function gofly_webhook_receiver($request) {
    $data = $request->get_json_params();

    // Log webhook data for debugging
    error_log('Fluent Forms Webhook: ' . print_r($data, true));

    // Send WhatsApp notification via API (optional)
    // send_whatsapp_notification($data);

    return array('status' => 'success');
}
```

---

## Security Best Practices

### 1. Add CORS Headers

Add to WordPress `functions.php`:

```php
<?php
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: https://yourdomain.com');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        return $value;
    });
}, 15);
```

### 2. Add Rate Limiting

```php
<?php
function gofly_check_rate_limit($ip) {
    $transient_key = 'gofly_rate_limit_' . md5($ip);
    $requests = get_transient($transient_key);

    if ($requests && $requests > 10) { // Max 10 requests per hour
        return false;
    }

    set_transient($transient_key, ($requests ? $requests + 1 : 1), HOUR_IN_SECONDS);
    return true;
}

function gofly_submit_visa_lead($request) {
    $ip = $request->get_header('X-Forwarded-For') ?: $_SERVER['REMOTE_ADDR'];

    if (!gofly_check_rate_limit($ip)) {
        return new WP_Error('rate_limit', 'Too many requests', array('status' => 429));
    }

    // ... rest of the code
}
```

### 3. Add Honeypot Field

Add to form in index.html:

```html
<!-- Hidden honeypot field to catch bots -->
<input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
```

Check in backend:

```php
if (!empty($params['website'])) {
    // Bot detected, reject silently
    return array('success' => true); // Fake success
}
```

---

## Testing

### 1. Test Locally First

```javascript
// Add console logging
console.log('Submitting lead:', leadInfo);
console.log('Response:', result);
```

### 2. Test with Postman

```bash
POST https://goflybd.com/wp-json/gofly/v1/submit-visa-lead
Content-Type: application/json

{
  "name": "Test User",
  "phone": "01712345678",
  "email": "test@example.com",
  "country": "মালয়েশিয়া",
  "score": 75,
  "percentage": 75
}
```

### 3. Check Fluent Forms Entries

1. Go to **Fluent Forms → Entries**
2. Select your form
3. Verify submissions appear

---

## Notifications Setup

### WhatsApp Notification (via API)

```php
function send_whatsapp_notification($lead_data) {
    $whatsapp_api_url = 'https://api.whatsapp.com/send'; // Replace with actual API
    $phone = '8801713289170';

    $message = "🎯 নতুন ভিসা লিড!\n\n";
    $message .= "নাম: {$lead_data['name']}\n";
    $message .= "ফোন: {$lead_data['phone']}\n";
    $message .= "দেশ: {$lead_data['country']}\n";
    $message .= "স্কোর: {$lead_data['percentage']}%\n";

    // Send via WhatsApp API
    wp_remote_post($whatsapp_api_url, array(
        'body' => array(
            'phone' => $phone,
            'message' => $message
        )
    ));
}
```

### Email Notification (Built-in)

Fluent Forms automatically sends email notifications. Configure in:
**Form → Settings & Integrations → Email Notifications**

---

## Troubleshooting

### Issue 1: CORS Error

**Solution:** Add CORS headers (see Security section above)

### Issue 2: 404 Not Found

**Solution:**
1. Go to **Settings → Permalinks** in WordPress
2. Click **Save Changes** to flush rewrite rules

### Issue 3: Submissions Not Appearing

**Solution:**
1. Check Fluent Forms field names match your data keys
2. Verify Form ID is correct
3. Check WordPress error logs: `wp-content/debug.log`

### Issue 4: API Key Not Working

**Solution:**
1. Regenerate API key in Fluent Forms
2. Ensure Fluent Forms Pro is activated
3. Check API is enabled in settings

---

## Production Checklist

- [ ] SSL certificate installed (HTTPS)
- [ ] API keys stored securely (not in public code)
- [ ] CORS headers configured
- [ ] Rate limiting implemented
- [ ] Error handling added
- [ ] Email notifications configured
- [ ] Backup storage in localStorage working
- [ ] Test submissions successful
- [ ] Webhook tested (if using)
- [ ] Form validation working
- [ ] Privacy policy updated

---

## Alternative: Google Sheets Integration

If you prefer Google Sheets instead of Fluent Forms:

```javascript
async function submitToGoogleSheets(leadInfo) {
    const GOOGLE_SCRIPT_URL = 'https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec';

    try {
        const response = await fetch(GOOGLE_SCRIPT_URL, {
            method: 'POST',
            mode: 'no-cors',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(leadInfo)
        });

        console.log('✅ Submitted to Google Sheets');
        currentStep = 'result';
        render();
        hideLoading();
    } catch (error) {
        console.error('❌ Google Sheets error:', error);
        // Show results anyway
        currentStep = 'result';
        render();
        hideLoading();
    }
}
```

---

## Support

For integration help:
- **Email:** support@goflybd.com
- **Phone:** 01713-289170
- **Fluent Forms Docs:** https://fluentforms.com/docs/

---

**Last Updated:** January 17, 2026
