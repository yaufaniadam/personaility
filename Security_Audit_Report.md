# Laporan Audit Keamanan - Code Review (Laravel + Inertia.js)
**Auditor**: Senior DevSecOps & Penetration Tester (OSCP)
**Target**: `app/Http/Controllers`, `app/Models`, `routes`, `resources/js`
**Fokus**: Kerentanan Arsitektur Inertia.js & OWASP Top 10

Berdasarkan *scanning* dan analisis komprehensif pada codebase, berikut adalah temuan kerentanan keamanan yang dikelompokkan berdasarkan tingkat keparahan (severity):

---

## 🔴 CRITICAL

### 1. Data Over-Exposure & Information Disclosure (Inertia)
Arsitektur Inertia.js akan mengekspos seluruh data model ke dalam bentuk JSON yang bisa dibaca melalui *browser devtools*. Ditemukan beberapa endpoint yang mengirim *raw Eloquent Model* tanpa disaring.

**Temuan 1:**
- **Lokasi File:** `app/Http/Controllers/DashboardController.php` (Baris 33)
- **Skenario Serangan:** Variabel `$latest` (Model `Assessment` beserta relasi `aiInsight`) dikirim mentah ke *frontend*. Peretas dengan mudah membuka tab *Network* atau elemen Vue Devtools untuk melihat seluruh kolom di database, termasuk *timestamps*, ID internal, atau data *Insight* sensitif yang mungkin tidak ditampilkan di antarmuka pengguna namun terekspos di *payload*.
- **Remediation:** Gunakan *API Resources* atau manipulasi koleksi.
  ```php
  // Solusi
  return Inertia::render('Dashboard', [
      // Lebih disarankan menggunakan AssessmentResource::make($latest)
      'latestAssessment' => $latest ? $latest->only('id', 'completed_at', 'openness_score', 'etc') : null,
      'latestInsight'    => $latest?->aiInsight ? $latest->aiInsight->only('id', 'summary', 'etc') : null,
      'history'          => $history, // Ini sudah aman karena membatasi kolom di query
  ]);
  ```

**Temuan 2:**
- **Lokasi File:** `app/Http/Controllers/PsychologistController.php` (Baris 54)
- **Skenario Serangan:** Endpoint `show()` mengirimkan data utuh dari model `Psychologist`. Jika model ini menyimpan informasi seperti NIK, nomor telepon pribadi, email, atau status internal administratif (yang sebetulnya di-hide secara UI namun di-fetch di Eloquent), maka data ini akan terekspos publik (karena endpoint tidak ber-autentikasi).
- **Remediation:** 
  ```php
  // Solusi
  return Inertia::render('Psychologists/Show', [
      'psychologist' => $psychologist->only(['id', 'name', 'city', 'specialization', 'contact_phone', 'contact_whatsapp', 'website']),
  ]);
  // Atau lebih baik buat PsychologistDetailResource::make($psychologist)
  ```

**Temuan 3:**
- **Lokasi File:** `app/Http/Controllers/AssessmentController.php` (Baris 122 & 140)
- **Skenario Serangan:** Fungsi `result()` dan `history()` mengekspos *raw models* `Assessment` dan `AiInsight`. Serupa dengan temuan sebelumnya, hal ini membuka *information disclosure*.
- **Remediation:** Sama dengan di atas, inisiasikan `AssessmentResource` dan bungkus kembalian datanya sebelum masuk ke pemanggilan `Inertia::render()`.

---

## 🟠 HIGH

### 1. Race Conditions (Business Logic Flaws)
**Temuan:**
- **Lokasi File:** `app/Http/Controllers/AssessmentController.php` (Baris 147 - `generateInsight()`)
- **Skenario Serangan:** Fitur penciptaan *insight AI* (`$this->aiService->generate()`) memanggil API pihak ketiga yang memakan waktu (dan berbiaya). Tidak ditemukan mekanisme *Locking* atau pembatasan (Rate Limit/Transaction). Jika *user* jahat atau tidak sabar menekan tombol "Generate Insight" berulangkali secara cepat (*rapid request/double-click*), aplikasi akan menerima *request* paralel. Ini mengakibatkan model `AiInsight` terhapus/dibuat ganda secara bersamaan (*Race Condition*), membebani API eksternal, dan menyebabkan anomali tagihan AI (*financial loss*).
- **Remediation:** Gunakan **Atomic Lock** menggunakan Redis/Cache Laravel.
  ```php
  public function generateInsight(Assessment $assessment): RedirectResponse
  {
      $this->authorize('view', $assessment);

      // Mutex Lock selama 30 detik agar user tidak click-spamming
      $lock = \Illuminate\Support\Facades\Cache::lock("gen_insight_{$assessment->id}", 30);

      if (!$lock->get()) {
          // Jika proses sedang berjalan dari request sebelumnya
          return redirect()->back()->with('error', 'Analisa sedang diproses. Mohon tunggu.');
      }

      try {
          if ($assessment->aiInsight()->exists()) {
              $assessment->aiInsight()->delete();
          }

          $assessment->load('answers.question');
          // ... scoring logic ...
          $this->aiService->generate($assessment, $scores, $levels, $notes, $patterns);

      } finally {
          // Pastikan lock selalu dibuka meskipun gagal (Exception)
          $lock->release();
      }

      return redirect()->route('assessment.result', $assessment->id);
  }
  ```

---

## 🟢 LOW (Aman / Telah Diimplementasikan Sesuai Best Practices)

Poin-poin di bawah ini berada dalam status **CLEAN** dan telah dievaluasi:

**1. Insecure Direct Object Reference (IDOR) & Broken Access Control (CLEAN)**
*   **Inspeksi:** Controller mendeteksi ID Model yang disuntikkan (*Route Model Binding*).
*   **Hasil:** Tidak ditemukan *Broken Access Control*. Setiap pemanggilan sensitif seperti `$this->authorize('view', $assessment);` sudah digunakan dengan tepat (misal pada `generateInsight` dan `result`). Permintaan data juga dilingkupi dengan `auth()->user()->assessments()`.

**2. Mass Assignment Vulnerabilities (CLEAN)**
*   **Inspeksi:** Mencari penggunaan `$guarded = []` pada Model dan `$request->all()` pada Controller.
*   **Hasil:** Model Eloquent berpegang pada metode `$fillable` (secara default `guarded`). Controller modern (contoh: `AssessmentController@store`) menerapkan eksekusi aman via `$request->validated('answers')` dari `StoreAssessmentRequest` yang mencegah *Mass-Assignment Injection*. 

**3. SQL Injection (SQLi) (CLEAN)**
*   **Inspeksi:** Seluruh aplikasi di-*scan* untuk penggunaan Query Mentah (`DB::raw()`, `whereRaw`, dll.).
*   **Hasil:** Tidak ditemukan *Raw Query* yang tidak aman. Seluruh parameter menggunakan *Query Builder* & Eloquent bawaan, sehingga secara otomatis menerapkan PDO parameter binding secara tepat.

**4. Cross-Site Scripting (XSS) (CLEAN)**
*   **Inspeksi:** Komponen Vue (`resources/js`) dan berkas frontend JavaScript dianalisa terkait bahaya injeksi *directive html* secara mentah.
*   **Hasil:** Tidak ada penggunaan `v-html` ditemukan pada frontend, yang berarti Vue menangani semua output string dengan aman (auto-escaped DOM Nodes).
