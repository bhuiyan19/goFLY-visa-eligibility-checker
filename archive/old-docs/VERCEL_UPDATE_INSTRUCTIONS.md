# Vercel Deployment Update Instructions

**Issue:** Vercel deployment at https://gofly-visa-eligibility-checker.vercel.app/ is not showing latest updates

**Reason:** Vercel is likely configured to deploy from `master` branch, but all updates are in `claude/visa-eligibility-checker-oZjzM` branch

---

## ✅ Solution Options

### Option 1: Change Vercel Branch Settings (Recommended)

1. Go to Vercel Dashboard: https://vercel.com/dashboard
2. Select project: **gofly-visa-eligibility-checker**
3. Go to **Settings** → **Git**
4. Under "Production Branch", change from `master` to `claude/visa-eligibility-checker-oZjzM`
5. Click **Save**
6. Go to **Deployments** and click **Redeploy** on the latest deployment

**Result:** Vercel will now auto-deploy from the claude branch

---

### Option 2: Manual Redeploy from Vercel Dashboard

1. Go to: https://vercel.com/dashboard
2. Select: **gofly-visa-eligibility-checker**
3. Go to **Deployments** tab
4. Find the deployment from branch: `claude/visa-eligibility-checker-oZjzM`
5. Click the **three dots (...)** → **Promote to Production**

**Result:** Latest version will be live immediately

---

### Option 3: Trigger New Deployment via Git

Since the claude branch has all updates, you can trigger a new deployment by:

```bash
# Make a small change to trigger Vercel
git checkout claude/visa-eligibility-checker-oZjzM
echo "# Trigger deployment" >> .vercelignore
git add .vercelignore
git commit -m "Trigger Vercel deployment"
git push origin claude/visa-eligibility-checker-oZjzM
```

**Note:** Only works if Vercel is already watching the claude branch

---

### Option 4: Push Master Branch Manually

**Local Status:**
- ✅ `master` branch has been updated locally with all changes
- ✅ Merge from claude branch completed successfully
- ❌ Cannot push to master via this system (403 restriction)

**You need to:**
1. Pull the repo on your local machine
2. Checkout master branch
3. Push to origin:

```bash
git checkout master
git pull origin master
git push origin master
```

Vercel will auto-deploy once master is pushed.

---

## 📋 What's Updated in Claude Branch

All these changes are ready but not yet deployed to Vercel:

### Critical Fixes:
1. ✅ **Country count:** 42 → 40 (removed Maldives, New Zealand)
2. ✅ **Schengen info:** Removed misleading "২০২৬ থেকে অনলাইন"
3. ✅ **Lead capture UX:** Fixed percentage showing before form submit
4. ✅ **Review count:** 431+ → 450+

### Project Structure:
5. ✅ Organized folders (docs/, api/, wordpress/, archive/)
6. ✅ Cleaned up 40+ old documentation files
7. ✅ Updated all documentation

### Files Changed:
- `index.html` - Main app (Maldives/NZ removed, Schengen fixed, reviews updated)
- `docs/FINAL_RATING.md` - Updated to 40 countries
- `wordpress/template-seo-optimized.php` - Updated
- All documentation files
- New: `DATA_ACCURACY_VERIFICATION_2026.md`
- New: `PROJECT_STRUCTURE.md`

---

## 🔍 Verify Deployment After Update

Once deployed, check:

1. **Country count:**
   - View page source
   - Search for "COUNTRIES = ["
   - Should have exactly 40 entries (no Maldives, no New Zealand)

2. **Schengen description:**
   - Select Schengen country
   - Description should NOT say "২০২৬ থেকে অনলাইন"
   - Should just say "goFLY: ৯০%"

3. **Review count:**
   - Look at footer or stats section
   - Should show "450+" not "431+"

4. **Lead capture:**
   - Complete a questionnaire
   - Button should say "রেজাল্ট দেখুন" (no percentage)
   - Heading should say "🎉 প্রায় শেষ! আপনার তথ্য দিন"

---

## 📞 Need Help?

If Vercel is not updating after trying these options:

1. **Check Vercel build logs** for errors
2. **Clear Vercel cache:** Settings → General → Clear Cache
3. **Check branch settings:** Ensure correct branch is selected
4. **Webhook issue:** May need to reconnect GitHub integration

---

## ✅ Current Status

**GitHub:**
- ✅ `claude/visa-eligibility-checker-oZjzM` - All updates pushed ✅
- ✅ `master` - Updated locally, needs manual push

**Vercel:**
- ⏳ Waiting for deployment trigger
- 🎯 Once updated, will show all 7 critical fixes

**Recommended:** Use Option 1 (Change Vercel branch to claude branch)

---

**Last Updated:** March 24, 2026
**All changes committed to:** `claude/visa-eligibility-checker-oZjzM`
