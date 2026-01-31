# iframe Embedding Fix - শুধু 2 মিনিটে সমাধান

## সমস্যা কী?
Vercel এখন **main** branch থেকে deploy করছে, কিন্তু সঠিক iframe fix আছে **claude/visa-eligibility-checker-oZjzM** branch এ।

## সমাধান (শুধু 3 টি ক্লিক):

### অপশন ১: Production Branch পরিবর্তন করুন (সুপারিশকৃত)

1. **এই লিংকে ক্লিক করুন**: https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/settings/git

2. **"Production Branch"** খুঁজুন এবং পরিবর্তন করুন:
   - পুরাতন: `main`
   - নতুন: `claude/visa-eligibility-checker-oZjzM`
   - **Save** বাটন ক্লিক করুন

3. **Redeploy করুন**: https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/deployments
   - সবচেয়ে উপরের deployment এ **⋯** (three dots) ক্লিক করুন
   - **Redeploy** করুন

✅ **সম্পন্ন!** 1-2 মিনিটের মধ্যে iframe কাজ করবে।

---

### অপশন ২: Header Override (যদি অপশন ১ কাজ না করে)

1. **এই লিংকে যান**: https://vercel.com/mahabub-alam-razus-projects/gofly-visa-eligibility-checker/settings/headers

2. **Add Another Override** ক্লিক করুন

3. নিচের তথ্য দিন:
   ```
   Path: /*
   Header Name: X-Frame-Options
   Value: [খালি রাখুন বা DELETE করুন]
   ```

4. **Save** করুন এবং redeploy করুন

---

## যাচাই করুন:

এই কোড দিয়ে iframe test করুন:

```html
<iframe
  src="https://gofly-visa-eligibility-checker.vercel.app/"
  width="100%"
  height="800px"
  frameborder="0"
  allowfullscreen>
</iframe>
```

**গুরুত্বপূর্ণ**: WordPress template থেকে `sandbox` attribute সরিয়ে দিন!

---

## কী ঠিক আছে:

✅ Code সঠিক (claude branch এ)
✅ vercel.json সঠিক (iframe blocking নাই)
✅ GitHub এ push করা আছে
❌ শুধু Vercel কে বলতে হবে claude branch deploy করতে

---

**প্রশ্ন থাকলে বলুন!** 🚀
