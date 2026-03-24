# iframe Fix Verification Checklist

## ধাপ ১: Vercel Production Branch যাচাই করুন

আপনি কি এটা করেছেন?

1. **Vercel Settings → Git** তে গিয়েছেন?
   - লিংক: https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/settings/git

2. **"Production Branch"** দেখতে পাচ্ছেন?
   - এটা এখন কী লেখা আছে? `main` নাকি `claude/visa-eligibility-checker-oZjzM`?

3. যদি এখনো **`main`** লেখা থাকে:
   - এটা পরিবর্তন করে **`claude/visa-eligibility-checker-oZjzM`** লিখুন
   - **Save** বাটনে ক্লিক করুন
   - তারপর আবার redeploy করুন

---

## ধাপ ২: WordPress Template যাচাই করুন

আপনার WordPress iframe code এ কি **`sandbox`** attribute আছে?

### ❌ ভুল (কাজ করবে না):
```html
<iframe
  sandbox="allow-scripts allow-same-origin allow-popups allow-forms allow-top-navigation"
  src="https://gofly-visa-eligibility-checker.vercel.app/"
  width="100%"
  height="800px">
</iframe>
```

### ✅ সঠিক (কাজ করবে):
```html
<iframe
  src="https://gofly-visa-eligibility-checker.vercel.app/"
  width="100%"
  height="800px"
  frameborder="0"
  allowfullscreen>
</iframe>
```

**`sandbox` attribute সম্পূর্ণ সরিয়ে দিন!**

---

## ধাপ ৩: Deployment যাচাই করুন

1. **Vercel Deployments** পেজে যান:
   - https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/deployments

2. সবচেয়ে নতুন deployment দেখুন:
   - **Branch** কলামে কী লেখা? `main` নাকি `claude/visa-eligibility-checker-oZjzM`?
   - **Status** কী? "Ready" হতে হবে

3. যদি Branch এখনো **`main`** দেখায়:
   - তাহলে Production Branch change হয়নি
   - ধাপ ১ আবার করুন

---

## ধাপ ৪: Direct URL Test

Browser এ সরাসরি এই URL খুলুন:
```
https://gofly-visa-eligibility-checker.vercel.app/
```

**প্রশ্ন:**
- পেজ কি লোড হচ্ছে? ✅
- নাকি error দেখাচ্ছে? ❌

---

## দ্রুত সমাধান: Vercel Dashboard Headers Override

যদি উপরের সব কিছু করেও কাজ না হয়:

1. **Vercel Settings → Headers**:
   - https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/settings/headers

2. যদি **"X-Frame-Options"** header দেখেন, সেটা **DELETE** করে দিন

3. **Save** করুন এবং redeploy করুন

---

## পরবর্তী পদক্ষেপ:

**এই checklist অনুসরণ করে স্ক্রিনশট পাঠান:**
1. Vercel Git Settings পেজের screenshot (Production Branch দেখাচ্ছে)
2. Vercel Deployments পেজের screenshot (latest deployment এর Branch দেখাচ্ছে)
3. WordPress iframe code (যদি sandbox attribute আছে কিনা)

এটা দেখলে আমি exact সমস্যা বুঝতে পারবো! 🔍
