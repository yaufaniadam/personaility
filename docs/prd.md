# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## Product Name (Working Title)
Personality Growth Platform (Web MVP)

---

# 1. Vision
Membangun web-based personality growth platform berbasis Big Five Personality Model dengan AI-powered reflection untuk membantu pengguna memahami diri, mengidentifikasi blind spot, dan membangun sistem pertumbuhan personal.

Phase 1.5 menambahkan fitur direktori psikolog lokal sebagai value tambahan (non-core, non-clinical).

---

# 2. Product Scope (Phase 1 - MVP)

## Core Features
1. Big Five Assessment (30–50 items)
2. Likert Scoring (1–5)
3. Reverse Scoring
4. Trait Calculation (OCEAN)
5. AI Narrative Insight
6. Optional Reflective Notes (selected questions)
7. User Authentication
8. Assessment History
9. Basic Dashboard
10. Privacy Policy & Terms of Service

---

# 3. Phase 1.5 – Psychologist Directory (Value Add)

## Purpose
Memberikan akses ke direktori psikolog lokal tanpa menjadi penyedia layanan kesehatan.

## Scope
- Listing psikolog per kota
- Verifikasi STR & SIP
- Informasi spesialisasi
- Kontak langsung (WhatsApp / Website)
- Tanpa booking system
- Tanpa pembayaran via platform

## Disclaimer
Platform hanya menyediakan informasi profesional terverifikasi. Seluruh layanan dan tanggung jawab berada pada psikolog terkait.

---

# 4. User Flow

## A. Assessment Flow
Landing Page → Start Assessment → Consent → Questions → Optional Notes → Submit → AI Insight Page → Save Result → Share

## B. Growth Retention Flow
Dashboard → View History → Compare Trait → Reflect → Monthly Reminder

## C. Directory Flow
Menu → Temukan Profesional → Filter Kota → View Detail → Hubungi Langsung

---

# 5. Functional Requirements

## 5.1 Assessment Engine
- 30–50 item
- Likert 1–5
- Reverse scoring logic
- Trait average calculation
- Store raw score + normalized score
- Timestamp each assessment

## 5.2 AI Narrative Layer
Input:
- Trait score
- High/Low markers
- Selected user notes

Output:
- Core Strength
- Blind Spot
- Growth Suggestion
- Stress Pattern

AI does NOT:
- Diagnose
- Label disorders
- Provide medical advice

## 5.3 Reflective Notes
- Optional on selected items (5–8 questions)
- Stored separately from trait scoring
- Used only for narrative context

## 5.4 Dashboard
- Latest result
- Historical comparison
- Trait chart visualization
- Re-assessment CTA

## 5.5 Psychologist Directory
Fields:
- Name
- STR Number
- SIP (optional)
- City
- Specialization
- Contact
- Short bio

No scheduling.
No payment.

---

# 6. Non-Functional Requirements

## Security
- HTTPS
- Password hashing (bcrypt/argon)
- Role-based admin access
- Data encryption at rest (optional stage 2)

## Privacy
- Explicit consent before assessment
- Right to delete account
- Clear privacy policy

## Performance
- Page load < 2.5s
- AI response under 10s

---

# 7. Tech Stack

## Backend
- Laravel 12
- Livewire 5
- Filament 5 (Admin Panel)

## Frontend
- Inertia.js v2
- Blade / HTML templates from `/stitch' folder
- Tailwind CSS (if included in template)

## Database
- MySQL / MariaDB

## AI Integration
- External LLM API
- Prompt templating in backend
- Logging AI responses

---

# 8. Database Structure (High-Level)

## users
- id
- name
- email
- password
- created_at

## assessments
- id
- user_id
- openness_score
- conscientiousness_score
- extraversion_score
- agreeableness_score
- neuroticism_score
- normalized_json
- created_at

## assessment_answers
- id
- assessment_id
- question_id
- likert_score
- note_text (nullable)

## psychologists
- id
- name
- str_number
- sip_number
- city
- specialization
- contact
- bio
- verified_status

---

# 9. Monetization (Future)

Freemium Model:
Free:
- Full assessment
- Basic insight

Premium (20–30k/month):
- Deep insight
- Historical comparison
- Growth system
- Advanced reflection

Directory monetization later:
- Referral model
- Featured listing

---

# 10. Metrics to Track (Phase 1)

- Completion rate
- Share rate
- D7 retention
- D30 retention
- Repeat assessment rate
- AI insight engagement time

---

# 11. Risks & Mitigation

Risk: Data breach
Mitigation: Secure hosting + encryption

Risk: Misinterpretation as therapy
Mitigation: Clear disclaimer

Risk: Low retention
Mitigation: Improve insight depth + reminder system

---

# 12. Out of Scope (Phase 1)

- Marketplace booking
- Payment integration
- Clinical diagnosis
- Machine learning custom model

---

# 13. Timeline (Suggested)

Month 1:
- Assessment engine
- Basic scoring
- AI integration

Month 2:
- Dashboard
- History
- Legal pages

Month 3:
- Directory feature
- UX polish
- Beta launch

---

END OF DOCUMENT

