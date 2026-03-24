# 📋 Fluent Forms - Questions এবং Answers সহ Save করা

## 🎯 লক্ষ্য:

প্রতিটা customer কোন কোন question এ কী answer দিয়েছে সেটা Fluent Forms এ দেখতে চান।

---

## 📊 আগে vs পরে:

### ❌ আগে (শুধু basic info):
```
- Name: Test User
- Phone: 01712345678
- Email: test@goflybd.com
- Country: নেপাল
- Score: 85
- Percentage: 85%
```

### ✅ পরে (Questions + Answers):
```
- Name: Test User
- Phone: 01712345678
- Country: নেপাল
- Difficulty: easy
- Score: 85%

Questions & Answers:
- Q1: আপনার পাসপোর্টের মেয়াদ কত মাস আছে?
  A1: ৬ মাসের বেশি

- Q2: আপনার কি রিটার্ন টিকেট আছে?
  A2: হ্যাঁ

- Q3: আপনার কি হোটেল বুকিং আছে?
  A3: হ্যাঁ

- Q4: আপনার কি পর্যাপ্ত ভ্রমণ খরচ আছে?
  A4: হ্যাঁ
```

---

## 🚀 Setup (3 Steps):

### Step 1: Frontend Update (Already Done! ✅)

আমি index.html update করেছি। এখন এটা পাঠায়:
- Basic info (name, phone, email, country, score)
- **+ All questions and answers** (formatted)

**কী পাঠাচ্ছে:**
```json
{
  "name": "Test User",
  "phone": "01712345678",
  "country": "নেপাল",
  "difficulty": "easy",
  "score": 85,
  "percentage": 85,
  "answers": {
    "passport_validity": {
      "question": "আপনার পাসপোর্টের মেয়াদ কত মাস আছে?",
      "answer": "৬ মাসের বেশি"
    },
    "return_ticket": {
      "question": "আপনার কি রিটার্ন টিকেট আছে?",
      "answer": "হ্যাঁ"
    },
    ...
  }
}
```

---

### Step 2: Backend Update (api.php)

**2.1 Download করুন:**
```
File: api-with-answers.php (GitHub repository)
```

**2.2 Upload করুন cPanel এ:**
```
cPanel → File Manager
→ /wp-content/themes/travel-agency/visa-checker-api/

1. পুরানো api.php backup: Rename → api-backup.php
2. Upload: api-with-answers.php
3. Rename: api-with-answers.php → api.php
```

**2.3 Form ID update করুন (line 101):**
```php
$form_id = 6959; // ← আপনার Form ID দিন
```

---

### Step 3: Fluent Forms এ Fields যোগ করুন

Fluent Forms form এ নতুন fields add করতে হবে questions এর জন্য।

**3.1 WordPress → Fluent Forms → Your Form → Edit**

**3.2 যে fields আছে (keep them):**
- Name (`input_name`)
- Phone (`input_phone`)
- Email (`input_email`)
- Country (`input_country`)
- Score (`input_score`)
- Percentage (`input_percentage`)

**3.3 নতুন fields যোগ করুন:**

**Add করুন (drag & drop):**

1. **Difficulty** (Text Input)
   - Label: "Difficulty Level"
   - Field Name: `input_difficulty`

2. **Question 1 - Question** (Text Area)
   - Label: "Question 1"
   - Field Name: `input_q1_question`

3. **Question 1 - Answer** (Text Area)
   - Label: "Answer 1"
   - Field Name: `input_q1_answer`

4. **Question 2 - Question** (Text Area)
   - Label: "Question 2"
   - Field Name: `input_q2_question`

5. **Question 2 - Answer** (Text Area)
   - Label: "Answer 2"
   - Field Name: `input_q2_answer`

**...এভাবে Q10 পর্যন্ত** (সবচেয়ে বেশি questions very-hard difficulty তে 9টি)

**Total fields হবে:**
- Basic: 6 fields (name, phone, email, country, score, percentage)
- Difficulty: 1 field
- Questions: 10 × 2 = 20 fields (q1_question, q1_answer, q2_question, q2_answer, ...)

**= Total: 27 fields**

---

## 📝 Quick Field Names List:

Copy করুন এগুলো Fluent Forms এ:

```
Basic Fields:
- input_name
- input_phone
- input_email
- input_country
- input_country_id
- input_difficulty
- input_score
- input_percentage

Question Fields:
- input_q1_question
- input_q1_answer
- input_q2_question
- input_q2_answer
- input_q3_question
- input_q3_answer
- input_q4_question
- input_q4_answer
- input_q5_question
- input_q5_answer
- input_q6_question
- input_q6_answer
- input_q7_question
- input_q7_answer
- input_q8_question
- input_q8_answer
- input_q9_question
- input_q9_answer
- input_q10_question
- input_q10_answer
```

---

## 🧪 Test করুন:

### Test 1: Deploy Frontend

```bash
git add index.html
git commit -m "Add question answers to lead submission"
git push
```

Vercel automatically deploy করবে (2-3 minutes)

### Test 2: Submit Test Lead

**Live site এ:**
1. Country select করুন (যেমন: Nepal - easy difficulty)
2. Questions answer দিন
3. Lead form submit করুন

**Console দেখবেন:**
```javascript
✅ Lead saved successfully with all answers! {
  submission_id: 456,
  name: "Test User",
  country: "নেপাল",
  percentage: "85%",
  questions_saved: 4  // ← Number of questions
}
```

### Test 3: WordPress Verify

**Fluent Forms → Entries → Latest Entry:**

```
Name: Test User
Phone: 01712345678
Country: নেপাল
Difficulty: easy
Score: 85
Percentage: 85%

Question 1: আপনার পাসপোর্টের মেয়াদ কত মাস আছে?
Answer 1: ৬ মাসের বেশি

Question 2: আপনার কি রিটার্ন টিকেট আছে?
Answer 2: হ্যাঁ

Question 3: আপনার কি হোটেল বুকিং আছে?
Answer 3: হ্যাঁ

Question 4: আপনার কি পর্যাপ্ত ভ্রমণ খরচ আছে?
Answer 4: হ্যাঁ
```

**Perfect!** 🎉

---

## 💡 এটা কিভাবে কাজ করে:

### Frontend (index.html):
```javascript
// Questions থেকে answers collect করে
answersWithLabels = {
  passport_validity: {
    question: "আপনার পাসপোর্টের মেয়াদ কত মাস আছে?",
    answer: "৬ মাসের বেশি"
  },
  ...
}

// API তে পাঠায়
POST to api.php with answers object
```

### Backend (api.php):
```php
// Receives answers
$answers = $data['answers'];

// Converts to numbered fields
$submission_data = [
  'input_q1_question' => "আপনার পাসপোর্টের মেয়াদ...",
  'input_q1_answer' => "৬ মাসের বেশি",
  'input_q2_question' => "আপনার কি রিটার্ন...",
  'input_q2_answer' => "হ্যাঁ",
  ...
];

// Saves to Fluent Forms
```

### Fluent Forms:
```
Displays all Q&A pairs in entry details
Export করতে পারবেন CSV তে
Email notifications এ include করতে পারবেন
```

---

## 📧 Email Notification Setup:

**যদি questions email এও চান:**

**Fluent Forms → Your Form → Settings → Email Notifications:**

```
Subject: 🎯 New Visa Lead: {inputs.input_country}

Body:
📝 Basic Info:
Name: {inputs.input_name}
Phone: {inputs.input_phone}
Email: {inputs.input_email}
Country: {inputs.input_country}
Difficulty: {inputs.input_difficulty}
Score: {inputs.input_score} / {inputs.input_percentage}%

❓ Questions & Answers:
Q1: {inputs.input_q1_question}
A1: {inputs.input_q1_answer}

Q2: {inputs.input_q2_question}
A2: {inputs.input_q2_answer}

Q3: {inputs.input_q3_question}
A3: {inputs.input_q3_answer}

... (continue for all questions)
```

---

## 🔍 Troubleshooting:

### সমস্যা: Questions দেখা যাচ্ছে না

**Solution:**
1. Fluent Forms এ fields add করেছেন কিনা check করুন
2. Field names সঠিক কিনা verify করুন
3. api.php এ `questions_saved` number দেখুন console এ

### সমস্যা: শুধু কিছু questions দেখা যাচ্ছে

**Reason:** Different difficulties have different numbers of questions:
- Easy: 4 questions
- Medium: 5 questions
- Hard: 10 questions
- Very-Hard: 9 questions

**Solution:** সব questions এর জন্য fields add করুন (Q1-Q10)

### সমস্যা: Bengali characters garbled

**Solution:**
1. Database charset: utf8mb4 হতে হবে
2. Fluent Forms settings → Encoding → UTF-8

---

## 📊 Different Difficulty Questions:

### Easy (4 questions):
1. Passport validity
2. Return ticket
3. Hotel booking
4. Travel funds

### Medium (5 questions):
1. Passport validity
2. Employment status
3. Bank balance
4. Previous travel
5. Sponsor

### Hard (10 questions):
1. Passport validity
2. Employment status
3. Monthly income
4. Bank balance
5. Property
6. Previous travel
7. Visa rejection
8. Purpose
9. Invitation
10. Travel insurance

### Very-Hard (9 questions):
1. Passport validity
2. Employment status
3. Job duration
4. Monthly income
5. Bank balance
6. Property
7. Family ties
8. Previous travel
9. Visa rejection
10. Purpose

---

## ✅ Setup Checklist:

- [ ] index.html updated (already done ✅)
- [ ] api-with-answers.php uploaded
- [ ] api.php replaced with new version
- [ ] Form ID updated in api.php
- [ ] Fluent Forms fields added (27 total)
- [ ] Frontend deployed to Vercel
- [ ] Test lead submitted
- [ ] Questions visible in WordPress
- [ ] Email notification configured (optional)

---

## 🎯 Benefits:

**এখন আপনি পাবেন:**

✅ **Lead Quality Analysis:**
- কোন customer কতটা qualified
- কোন questions এ weak answers
- Follow-up করার জন্য specific points

✅ **Better Lead Filtering:**
- High score leads priority দিন
- Low score leads কে extra support দিন
- Specific needs বুঝুন

✅ **Data-Driven Decisions:**
- Common weak points identify করুন
- Service improvement areas খুঁজুন
- Success patterns analyze করুন

✅ **Personalized Follow-up:**
- "দেখছি আপনার passport validity কম..."
- "আপনার bank balance ভালো আছে, চলুন apply করি..."
- Specific advice দিতে পারবেন

---

## 📈 CSV Export Example:

**Fluent Forms → Entries → Export CSV:**

```csv
Name,Phone,Email,Country,Difficulty,Score,Q1,A1,Q2,A2,...
Test User,01712345678,test@email,নেপাল,easy,85,"পাসপোর্ট মেয়াদ?","৬ মাস+","রিটার্ন টিকেট?","হ্যাঁ",...
John Doe,01898765432,john@email,থাইল্যান্ড,medium,70,"পাসপোর্ট মেয়াদ?","৬-১২ মাস","চাকরি?","ব্যবসায়ী",...
```

**Analysis করতে পারবেন:**
- Excel এ open করে
- Sort by score
- Filter by difficulty
- Charts তৈরি করুন

---

**Files Ready:**
- ✅ `index.html` - Updated with answers
- ✅ `api-with-answers.php` - Backend with Q&A support
- ✅ `FLUENT_FORMS_WITH_ANSWERS.md` - This guide

**Next: Deploy frontend এবং backend update করুন!** 🚀
