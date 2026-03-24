# 🔍 iframe "refused to connect" Debugging Guide

## ✅ আমরা যা ইতিমধ্যে ঠিক করেছি:

1. ✅ vercel.json updated: `Content-Security-Policy: frame-ancestors *`
2. ✅ Cache disabled: `Cache-Control: no-cache, no-store, must-revalidate`
3. ✅ Production branch: `claude/visa-eligibility-checker-oZjzM`
4. ✅ WordPress template: `sandbox` attribute removed
5. ✅ Pushed to GitHub & Redeployed

---

## 🚨 এখন এই Steps Follow করুন:

### Step 1: Hard Refresh Browser Cache (CRITICAL!)

**Chrome/Edge:**
- Windows: `Ctrl + Shift + Delete` → "Cached images and files" → Clear
- Mac: `Cmd + Shift + Delete` → Clear cache
- Or: `Ctrl/Cmd + Shift + R` (hard refresh)

**Firefox:**
- `Ctrl + Shift + Delete` → Clear cache

**Safari:**
- `Cmd + Option + E` → Empty cache

---

### Step 2: Verify Deployment Completed

👉 https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/deployments

**Check করুন:**
1. Latest deployment এর **Status** কী?
   - ✅ "Ready" (green) হতে হবে
   - ❌ "Building" বা "Error" থাকলে সমস্যা

2. **Branch** column এ কী লেখা?
   - ✅ `claude/visa-eligibility-checker-oZjzM` হতে হবে
   - ❌ `main` থাকলে সমস্যা

3. **Deployment Time** কত পুরানো?
   - সবশেষ deploy কখন হয়েছে দেখুন
   - 5+ মিনিট হলে ভালো

---

### Step 3: Direct URL Test (খুবই গুরুত্বপূর্ণ!)

Browser এ **incognito/private mode** খুলুন এবং এই URL যান:

```
https://gofly-visa-eligibility-checker.vercel.app/
```

**প্রশ্ন:**
- ✅ পেজ কি লোড হচ্ছে? (ভিসা checker দেখাচ্ছে?)
- ❌ নাকি error দেখাচ্ছে?

**যদি direct URL কাজ করে কিন্তু iframe এ না করে** = Cache সমস্যা!

---

### Step 4: Check Browser Console (Technical)

WordPress পেজ খুলুন যেখানে iframe আছে:

1. **F12** চাপুন (Developer Tools)
2. **Console** tab এ যান
3. যে error দেখবেন সেটা copy করে পাঠান

**Common errors:**
- `Refused to display ... in a frame because it set 'X-Frame-Options' to 'deny'`
- `... violates the following Content Security Policy directive`
- Connection refused / Network error

---

### Step 5: Check Response Headers (Advanced)

Developer Tools open থাকা অবস্থায়:

1. **Network** tab এ যান
2. পেজ refresh করুন (F5)
3. List এ `gofly-visa-eligibility-checker.vercel.app` খুঁজুন
4. সেটা click করুন
5. **Headers** tab এ যান
6. **Response Headers** দেখুন:

**এগুলো থাকা উচিত:**
```
content-security-policy: frame-ancestors *
cache-control: no-cache, no-store, must-revalidate
```

**এগুলো থাকা উচিত নয়:**
```
x-frame-options: DENY (বা SAMEORIGIN)
```

Screenshot পাঠান!

---

### Step 6: Test on Different Browser

যদি Chrome এ কাজ না করে:
- Firefox try করুন
- Safari try করুন
- Mobile browser try করুন

---

### Step 7: Vercel Edge Cache Clear (যদি সব fail করে)

👉 https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/settings/domains

1. Domain এর পাশে **⋯** (three dots) ক্লিক করুন
2. **Purge** or **Clear Cache** option খুঁজুন
3. Cache clear করুন
4. 2-3 মিনিট wait করুন
5. আবার try করুন

---

### Step 8: Final Nuclear Option - Force New Deployment

👉 https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/deployments

1. Latest deployment এ **⋯** → **Redeploy**
2. **Force redeploy** option টিক দিন (যদি থাকে)
3. Deploy করুন
4. ⏰ **5 মিনিট অপেক্ষা করুন**
5. Browser cache clear করুন (Step 1)
6. Incognito mode এ test করুন (Step 3)

---

## 📊 Report Back:

এই তথ্যগুলো পাঠান:

1. ✅/❌ Direct URL কাজ করছে? (incognito mode এ)
2. ✅/❌ iframe এ "refused to connect" আসছে?
3. 📸 Browser Console screenshot (F12 → Console)
4. 📸 Network Headers screenshot (Response Headers)
5. Latest deployment এর Status কী? (Ready/Building/Error)

এই info দিলে আমি exact সমস্যা ধরতে পারবো! 🎯

---

## 🔧 Quick Test Commands:

আপনি terminal থেকেও test করতে পারেন:

```bash
# Check if site is accessible
curl -I https://gofly-visa-eligibility-checker.vercel.app/

# Check for X-Frame-Options (should NOT appear)
curl -I https://gofly-visa-eligibility-checker.vercel.app/ | grep -i "x-frame"

# Check for CSP frame-ancestors (should appear)
curl -I https://gofly-visa-eligibility-checker.vercel.app/ | grep -i "content-security"
```

---

**এখন Step 1 থেকে শুরু করুন!** 🚀
