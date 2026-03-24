# 🚀 Vercel Deployment - Troubleshooting Guide

## ✅ Current Status

**Code:** Ready and pushed to GitHub
**Branch:** claude/visa-eligibility-checker-oZjzM
**Commit:** a7e3521
**File:** index.html (1117 lines)

**Features Included:**
- ✅ Simple design (gh-pages style)
- ✅ Flag emoji 🇧🇩
- ✅ Office phone: 09639-203090
- ✅ WordPress integration
- ✅ Lead capture form
- ✅ Email functionality
- ✅ 42 countries

---

## 🔍 If You Don't See Updates on Vercel

### Step 1: Check Which Branch Vercel is Using

1. Go to: https://vercel.com/dashboard
2. Select project: **gofly-visa-eligibility-checker**
3. Go to **Settings** → **Git**
4. Check **Production Branch**

**Should be:** `claude/visa-eligibility-checker-oZjzM`
**If it's different:** Change it to `claude/visa-eligibility-checker-oZjzM`

---

### Step 2: Force Redeploy

If Vercel hasn't auto-deployed:

1. Go to **Deployments** tab
2. Find latest deployment
3. Click **"Redeploy"** button
4. Wait 1-2 minutes

---

### Step 3: Clear Browser Cache

Sometimes the issue is just cache:

1. Open: https://gofly-visa-eligibility-checker.vercel.app/
2. Press: **Ctrl + Shift + R** (Windows/Linux)
3. Or: **Cmd + Shift + R** (Mac)
4. Or: Open in **Incognito/Private** mode

---

## ✅ How to Verify Everything is Working

### 1. Design Check (Simple Version)
Visit: https://gofly-visa-eligibility-checker.vercel.app/

**You should see:**
- ✅ Banner: "🎁 জানুয়ারি অফার" with simple layout
- ✅ Header: Flag emoji 🇧🇩 (NOT logo image)
- ✅ Phone: 09639-203090 (NOT 01713-289170)

**If you see logo image or mobile number:**
- Vercel hasn't deployed yet
- OR deploying from wrong branch

---

### 2. Functionality Check (WordPress Integration)

**Test Flow:**
1. Select a country (e.g., Nepal)
2. Answer all questions
3. **You should see:** Lead capture form
   - Name field
   - Phone field
   - Email field (optional)
4. Fill the form and submit
5. **You should see:** Result page

**If you DON'T see lead capture form:**
- Old version still deployed
- Need to check Vercel settings

---

### 3. Console Check (for WordPress)

Open browser console (F12) and:

1. Complete the flow until lead form
2. Submit the form
3. Check console for:
   ```
   ✅ Lead saved successfully!
   ```
   OR
   ```
   ❌ Failed to save lead: [error message]
   ```

**Note:** Even if WordPress isn't setup yet, you should see the attempt to connect.

---

## 🎯 Quick Verification Checklist

Visit the site and verify:

- [ ] Banner is simple (not flexbox/responsive)
- [ ] Header has flag emoji 🇧🇩
- [ ] Phone number is 09639-203090
- [ ] Can select country
- [ ] Can answer questions
- [ ] Lead capture form appears (Name, Phone, Email)
- [ ] Can submit the form
- [ ] Result page shows

---

## ⚠️ Common Issues

### Issue 1: Old Version Still Showing

**Cause:** Vercel deploying from different branch

**Solution:**
1. Vercel → Settings → Git
2. Change Production Branch to: `claude/visa-eligibility-checker-oZjzM`
3. Redeploy

---

### Issue 2: Changes Not Visible

**Cause:** Browser cache

**Solution:**
- Hard refresh: Ctrl+Shift+R
- Incognito mode
- Clear cache

---

### Issue 3: Vercel Not Auto-Deploying

**Cause:** Git hook not triggered

**Solution:**
1. Vercel → Deployments
2. Click "Redeploy" on latest
3. Or make a small change and push again

---

## 📋 WordPress Setup (After Vercel is Working)

Once Vercel shows the correct version:

### 1. Upload API File
```
File: api-final-with-email.php
Rename to: api.php
Location: /wp-content/themes/travel-agency/visa-checker-api/api.php
```

### 2. Setup Fluent Forms
```
Form ID: 12
Fields: 28 (7 basic + 21 Q&A)
Guide: FLUENT_FORMS_COMPLETE_FIELDS.md
```

### 3. Configure Email
```
Recipient: goflybd@gmail.com
Template: See CURRENT_STATUS.md
SMTP: Already configured ✅
```

### 4. Test Complete Flow
```
1. Submit test lead from Vercel site
2. Check WordPress: wp-admin/admin.php?page=fluent_forms_entries&form_id=12
3. Check email: goflybd@gmail.com
```

---

## 🎉 Expected Result

**After everything is setup:**

1. User visits: https://gofly-visa-eligibility-checker.vercel.app/
2. Sees simple, clean design with flag emoji
3. Selects country and answers questions
4. Fills lead form (Name, Phone, Email)
5. Submits
6. Data saved to WordPress Fluent Forms
7. Email sent to goflybd@gmail.com
8. User sees result page

**Perfect! 🎯**

---

## 📞 Current Phone Numbers

All instances updated to office number:
- Banner: 09639-203090
- Header: 09639-203090
- Result page CTA: 09639-203090
- Footer: 09639-203090

Total: 4 instances ✅

---

## 🌐 URLs

- **Live Site:** https://gofly-visa-eligibility-checker.vercel.app/
- **GitHub:** https://github.com/bhuiyan19/goFLY-visa-eligibility-checker
- **Branch:** claude/visa-eligibility-checker-oZjzM
- **WordPress:** https://goflybd.com/wp-admin

---

## ✅ Summary

**Code Status:** ✅ Complete and pushed
**Design:** ✅ Simple gh-pages style
**WordPress:** ✅ Integration included
**Email:** ✅ Functionality ready
**Deployment:** ⏳ Check Vercel dashboard

**Next:** Verify on Vercel and setup WordPress backend!
