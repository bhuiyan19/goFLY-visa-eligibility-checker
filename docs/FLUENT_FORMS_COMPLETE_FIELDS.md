# 📋 Fluent Forms - Complete Fields List

## 🎯 Total Fields: 28

### Basic Info Fields (8):
1. Name
2. Phone
3. Email
4. Country
5. Country ID
6. Difficulty
7. Score
8. Percentage

### Question & Answer Fields (20):
9-28. Questions 1-10 (প্রতিটার জন্য 2টি field)

---

## 📝 Complete Field List:

### ✅ Basic Fields (Already থাকা উচিত):

| # | Field Type | Label | Field Name | Required |
|---|------------|-------|------------|----------|
| 1 | Text Input | Name | `input_name` | Yes |
| 2 | Phone | Phone | `input_phone` | Yes |
| 3 | Email | Email | `input_email` | No |
| 4 | Text Input | Country | `input_country` | Yes |
| 5 | Text Input | Country ID | `input_country_id` | No |
| 6 | Text Input | Difficulty | `input_difficulty` | No |
| 7 | Numeric | Score | `input_score` | No |
| 8 | Numeric | Percentage | `input_percentage` | No |

### ✅ Question & Answer Fields (নতুন add করতে হবে):

| # | Field Type | Label | Field Name | Required |
|---|------------|-------|------------|----------|
| 9 | Text Area | Question 1 | `input_q1_question` | No |
| 10 | Text Area | Answer 1 | `input_q1_answer` | No |
| 11 | Text Area | Question 2 | `input_q2_question` | No |
| 12 | Text Area | Answer 2 | `input_q2_answer` | No |
| 13 | Text Area | Question 3 | `input_q3_question` | No |
| 14 | Text Area | Answer 3 | `input_q3_answer` | No |
| 15 | Text Area | Question 4 | `input_q4_question` | No |
| 16 | Text Area | Answer 4 | `input_q4_answer` | No |
| 17 | Text Area | Question 5 | `input_q5_question` | No |
| 18 | Text Area | Answer 5 | `input_q5_answer` | No |
| 19 | Text Area | Question 6 | `input_q6_question` | No |
| 20 | Text Area | Answer 6 | `input_q6_answer` | No |
| 21 | Text Area | Question 7 | `input_q7_question` | No |
| 22 | Text Area | Answer 7 | `input_q7_answer` | No |
| 23 | Text Area | Question 8 | `input_q8_question` | No |
| 24 | Text Area | Answer 8 | `input_q8_answer` | No |
| 25 | Text Area | Question 9 | `input_q9_question` | No |
| 26 | Text Area | Answer 9 | `input_q9_answer` | No |
| 27 | Text Area | Question 10 | `input_q10_question` | No |
| 28 | Text Area | Answer 10 | `input_q10_answer` | No |

---

## 🔧 কিভাবে Add করবেন (Step-by-Step):

### Method 1: UI থেকে (Recommended - সবচেয়ে সহজ):

#### Step 1: Form Edit করুন

```
WordPress Admin → Fluent Forms → All Forms
→ Your form এ hover করুন
→ "Edit" click করুন
```

#### Step 2: Fields যোগ করুন

**2.1 Left Sidebar থেকে field drag করুন:**

**Basic Fields (যদি না থাকে):**

1. **Name Field:**
   - Drag "Text Input" from left
   - Label: `Name`
   - Field Settings (right panel):
     - Element Label: `Name`
     - Name Attribute: `input_name`
     - Required: Yes (checkbox ✓)

2. **Phone Field:**
   - Drag "Phone Number" from left
   - Label: `Phone`
   - Name Attribute: `input_phone`
   - Required: Yes

3. **Email Field:**
   - Drag "Email" from left
   - Label: `Email`
   - Name Attribute: `input_email`
   - Required: No

4. **Country Field:**
   - Drag "Text Input"
   - Label: `Country`
   - Name Attribute: `input_country`
   - Required: Yes

5. **Country ID Field:**
   - Drag "Text Input"
   - Label: `Country ID`
   - Name Attribute: `input_country_id`
   - Required: No

6. **Difficulty Field:**
   - Drag "Text Input"
   - Label: `Difficulty`
   - Name Attribute: `input_difficulty`
   - Required: No

7. **Score Field:**
   - Drag "Numeric Field"
   - Label: `Score`
   - Name Attribute: `input_score`
   - Required: No

8. **Percentage Field:**
   - Drag "Numeric Field"
   - Label: `Percentage`
   - Name Attribute: `input_percentage`
   - Required: No

**Question & Answer Fields:**

9. **Question 1:**
   - Drag "Text Area"
   - Label: `Question 1`
   - Name Attribute: `input_q1_question`
   - Required: No

10. **Answer 1:**
    - Drag "Text Area"
    - Label: `Answer 1`
    - Name Attribute: `input_q1_answer`
    - Required: No

11-28. **Repeat করুন Q2 থেকে Q10 পর্যন্ত**
    - `input_q2_question`, `input_q2_answer`
    - `input_q3_question`, `input_q3_answer`
    - ... Q10 পর্যন্ত

#### Step 3: Save করুন

```
Top-right → "Save Form" button click করুন
```

---

### Method 2: JSON Import (দ্রুত - Advanced Users):

যদি JSON import করতে পারেন:

#### Step 1: JSON তৈরি করুন

নিচের JSON copy করুন এবং notepad এ save করুন।

#### Step 2: Import করুন

```
Fluent Forms → Tools → Import Forms
→ JSON file upload করুন
```

---

## 📊 Field Settings বিস্তারিত:

### প্রতিটা Field এ কী কী setting করবেন:

**Text Input / Text Area Fields:**
```
General:
- Element Label: [Field name]
- Name Attribute: [input_xxx]
- Placeholder Text: (খালি রাখুন)
- Default Value: (খালি রাখুন)

Advanced:
- Required: No (শুধু name, phone, country = Yes)
- Validation Rules: None
- Conditional Logic: None
```

**Numeric Fields (Score, Percentage):**
```
General:
- Element Label: Score / Percentage
- Name Attribute: input_score / input_percentage
- Number Format: Number
- Min Value: 0
- Max Value: 100

Advanced:
- Required: No
```

**Phone Field:**
```
General:
- Element Label: Phone
- Name Attribute: input_phone
- Phone Field Type: General

Advanced:
- Required: Yes
- Validation: Phone number
```

**Email Field:**
```
General:
- Element Label: Email
- Name Attribute: input_email

Advanced:
- Required: No
- Validation: Email
```

---

## 🎨 Form Organization (সুন্দর করে সাজান):

### Section 1: Basic Information
```
━━━━━━━━━━━━━━━━━━━━━━━
📋 Basic Information
━━━━━━━━━━━━━━━━━━━━━━━
- Name
- Phone
- Email
- Country
- Country ID
```

### Section 2: Assessment Details
```
━━━━━━━━━━━━━━━━━━━━━━━
📊 Assessment Details
━━━━━━━━━━━━━━━━━━━━━━━
- Difficulty
- Score
- Percentage
```

### Section 3: Questions & Answers
```
━━━━━━━━━━━━━━━━━━━━━━━
❓ Questions & Answers
━━━━━━━━━━━━━━━━━━━━━━━
- Question 1
- Answer 1
- Question 2
- Answer 2
... Q10 পর্যন্ত
```

**Section কিভাবে add করবেন:**
```
Left Sidebar → Section Break drag করুন
→ Section Title দিন
```

---

## ✅ Verification Checklist:

Field add করার পর verify করুন:

- [ ] Total 28 fields আছে
- [ ] Name field: `input_name` (Required)
- [ ] Phone field: `input_phone` (Required)
- [ ] Email field: `input_email` (Optional)
- [ ] Country field: `input_country` (Required)
- [ ] Country ID: `input_country_id`
- [ ] Difficulty: `input_difficulty`
- [ ] Score: `input_score`
- [ ] Percentage: `input_percentage`
- [ ] Q1-Q10: Question fields (×10)
- [ ] Q1-Q10: Answer fields (×10)
- [ ] Form saved successfully

---

## 🧪 Test Your Form:

### Test করার জন্য:

**Option 1: Manual Test (WordPress Admin থেকে):**
```
Fluent Forms → Entries → Add New Entry
→ সব fields দেখা যাচ্ছে কিনা check করুন
→ Test data দিয়ে submit করুন
```

**Option 2: Live Site Test:**
```
1. api-with-answers.php upload করুন
2. Form ID update করুন
3. Live site থেকে lead submit করুন
4. Fluent Forms → Entries check করুন
```

---

## 💡 Pro Tips:

### Tip 1: Field Organization
```
একসাথে সব fields না দেখতে চাইলে:
→ Advanced → Conditional Logic use করুন
→ শুধু filled fields show করুন
```

### Tip 2: Admin Column Display
```
Fluent Forms → Your Form → Settings
→ Admin Column Display
→ Select: Name, Phone, Country, Percentage
→ Entry list এ এই columns দেখবেন
```

### Tip 3: Email Notification
```
Settings → Email Notifications → Edit
→ Include করুন:
  {inputs.input_q1_question}
  {inputs.input_q1_answer}
→ Email এ Q&A দেখবেন
```

### Tip 4: CSV Export
```
Entries → Export Entries
→ All fields সহ CSV পাবেন
→ Excel এ analysis করতে পারবেন
```

---

## 🔍 Common Issues:

### Issue 1: Field name দেখা যাচ্ছে না

**Solution:**
```
Field click করুন → Right panel
→ "Advanced Options" expand করুন
→ Name Attribute দেখবেন
```

### Issue 2: Data save হচ্ছে না

**Check করুন:**
```
1. Field names exact match করছে কিনা
2. Spelling mistakes নেই তো?
3. api.php এ Form ID সঠিক কিনা
```

### Issue 3: Too many fields - form slow

**Solution:**
```
এটা normal - 28 fields বেশি না
যদি slow হয়:
→ Hosting upgrade করুন
→ PHP memory limit বাড়ান
```

---

## 📸 Screenshot Guide:

### যেভাবে field add করবেন (Visual):

```
1. Left Sidebar:
   ┌─────────────────┐
   │ Input Fields    │
   │ ┌─────────────┐ │
   │ │ Text Input  │ │ ← Drag this
   │ └─────────────┘ │
   │ ┌─────────────┐ │
   │ │ Text Area   │ │ ← Drag this
   │ └─────────────┘ │
   └─────────────────┘

2. Center (Form Builder):
   ┌─────────────────┐
   │ [Name Field]    │ ← Drop here
   │ [Phone Field]   │
   │ [Email Field]   │
   └─────────────────┘

3. Right Panel (Settings):
   ┌─────────────────┐
   │ Element Label   │
   │ Name            │
   │                 │
   │ Name Attribute  │
   │ input_name      │ ← এটা important!
   │                 │
   │ ☑ Required      │
   └─────────────────┘
```

---

## 🎯 Quick Copy-Paste Field Names:

**Basic Fields:**
```
input_name
input_phone
input_email
input_country
input_country_id
input_difficulty
input_score
input_percentage
```

**Question Fields:**
```
input_q1_question
input_q1_answer
input_q2_question
input_q2_answer
input_q3_question
input_q3_answer
input_q4_question
input_q4_answer
input_q5_question
input_q5_answer
input_q6_question
input_q6_answer
input_q7_question
input_q7_answer
input_q8_question
input_q8_answer
input_q9_question
input_q9_answer
input_q10_question
input_q10_answer
```

---

## ⏱️ Time Required:

```
Basic Fields (8)           → 5 minutes
Question Fields (20)       → 10 minutes
Organization (sections)    → 3 minutes
Testing                    → 2 minutes
──────────────────────────────────────
Total:                       20 minutes
```

---

## 🚀 After Setup:

**আপনি পাবেন:**

✅ Complete lead details
✅ All questions visible
✅ All answers readable
✅ CSV export ready
✅ Email notifications with Q&A
✅ Better lead analysis
✅ Personalized follow-up data

---

## 📞 Need Help?

**যদি stuck হন:**
1. Screenshot নিন current form এর
2. কোন step এ আটকেছেন বলুন
3. আমাকে জানান, help করব!

---

**এখন করুন:**
1. ✅ WordPress → Fluent Forms → Edit Form
2. ✅ 28 fields add করুন (এই list follow করে)
3. ✅ Save করুন
4. ✅ Test lead submit করুন

**Success হলে screenshot পাঠান!** 🎉
