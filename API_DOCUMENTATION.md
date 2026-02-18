# SIES University CMS - REST API Documentation

**Base URL:** `https://sies.test/api/v1`
**API Version:** v1
**Content-Type:** `application/json`

---

## Umumiy ma'lumotlar

### Loyiha haqida
SIES - Samarqand iqtisodiyot va servis instituti rasmiy veb-saytining CMS tizimi. Ko'p tilli (uz/ru/en) kontentni boshqarish va mobile ilovaga API orqali uzatish uchun mo'ljallangan.

### Texnologiyalar (Backend)
- **Framework:** Laravel 12
- **PHP:** 8.3+
- **Auth:** Laravel Sanctum (Bearer Token)
- **Database:** MySQL
- **Cache:** Laravel Cache (file/redis)

---

## Autentifikatsiya

API ikki qismga bo'linadi:
- **Public API** - token talab qilmaydi
- **Auth/Admin API** - `Authorization: Bearer {token}` header talab qiladi

Token olish uchun `POST /auth/login` endpointidan foydalaniladi.

```
Authorization: Bearer 1|abc123xyz...
```

---

## Tilni boshqarish (Localization)

Public API javoblarida faqat **joriy tildagi** ma'lumot qaytariladi. Tilni 2 usulda belgilash mumkin:

| Usul | Namuna | Prioritet |
|------|--------|-----------|
| Query parameter | `?lang=ru` | 1 (yuqori) |
| Header | `Accept-Language: en` | 2 |
| Default | — | `uz` |

**Qo'llab-quvvatlanadigan tillar:** `uz`, `ru`, `en`

Javob headerida `Content-Language: uz` qaytariladi.

---

## Javob formatlari

### Muvaffaqiyatli javob (single)
```json
{
  "data": { ... },
  "meta": {
    "locale": "uz"
  }
}
```

### Muvaffaqiyatli javob (paginated)
```json
{
  "data": [ ... ],
  "meta": {
    "locale": "uz",
    "current_page": 1,
    "per_page": 15,
    "total": 87,
    "last_page": 6
  },
  "links": {
    "first": "https://sies.test/api/v1/pages?page=1",
    "next": "https://sies.test/api/v1/pages?page=2",
    "prev": null,
    "last": "https://sies.test/api/v1/pages?page=6"
  }
}
```

### Xatolik javoblari
```json
{
  "error": {
    "code": "NOT_FOUND",
    "message": "The requested resource was not found."
  },
  "meta": {
    "timestamp": "2026-02-18T15:30:00+05:00"
  }
}
```

### Xatolik kodlari

| HTTP Status | Error Code | Tavsif |
|-------------|-----------|--------|
| 401 | `UNAUTHENTICATED` | Token yo'q yoki noto'g'ri |
| 401 | `INVALID_CREDENTIALS` | Login parol noto'g'ri |
| 403 | `FORBIDDEN` | Ruxsat yo'q |
| 404 | `NOT_FOUND` | Resurs topilmadi |
| 405 | `METHOD_NOT_ALLOWED` | HTTP method noto'g'ri |
| 422 | `VALIDATION_ERROR` | Validatsiya xatosi (details ichida maydon xatolari) |
| 429 | — | Rate limit oshdi |

### Validatsiya xatosi namunasi
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."],
      "password": ["The password must be at least 6 characters."]
    }
  },
  "meta": {
    "timestamp": "2026-02-18T15:30:00+05:00"
  }
}
```

---

## Rate Limiting

| Guruh | Limit | Qo'llaniladi |
|-------|-------|--------------|
| Public API | 60 so'rov/daqiqa (IP bo'yicha) | Barcha public endpointlar |
| Auth API | 120 so'rov/daqiqa (user bo'yicha) | Admin endpointlar |
| Login | 5 urinish/daqiqa (email bo'yicha) | Faqat login endpoint |

Rate limit headerlar: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `Retry-After`

---

## Pagination

Paginatsiya qo'llab-quvvatlaydigan endpointlarda:

| Parameter | Default | Max | Tavsif |
|-----------|---------|-----|--------|
| `page` | 1 | — | Sahifa raqami |
| `per_page` | 15 | 50 | Har sahifadagi elementlar soni |

---

# PUBLIC API

Autentifikatsiya talab qilmaydi.

---

## Homepage

### `GET /homepage`

Bosh sahifa uchun barcha kerakli ma'lumotlarni bitta so'rovda qaytaradi.

**Javob:**
```json
{
  "data": {
    "latest_news": {
      "id": 42,
      "title": "Yangi o'quv yili boshlanmoqda",
      "slug": "yangi-oquv-yili",
      "excerpt": "2026-yil uchun yangi o'quv yili...",
      "image": "https://sies.test/storage/pages/news.webp",
      "date": "2026-02-15",
      "views": 1250,
      "page_type": "blog",
      "tags": [
        {"id": 1, "name": "Yangiliklar", "slug": "yangiliklar", "image": null}
      ]
    },
    "other_news": [
      { "...PageList obyektlari (6 tagacha)" }
    ],
    "announcements": [
      { "...PageList obyektlari (11 tagacha)" }
    ],
    "announcements_with_activity": [
      { "...PageList obyektlari, activity=true (6 tagacha)" }
    ],
    "gallery_images": [
      "https://sies.test/storage/pages/gallery/img1.webp",
      "https://sies.test/storage/pages/gallery/img2.webp"
    ],
    "faculties": [
      { "...PageList obyektlari (4 tagacha)" }
    ],
    "departments": [
      { "...PageList obyektlari (6 tagacha)" }
    ],
    "tags": [
      {"id": 1, "name": "Yangiliklar", "slug": "yangiliklar", "image": null}
    ],
    "stats": {
      "campus_area": 25,
      "green_area": 15,
      "faculties": 5,
      "departments": 23,
      "centers": 8,
      "employees": 450,
      "leadership": 12,
      "scientific": 85,
      "technical": 120,
      "students": 8500,
      "male_students": 5000,
      "female_students": 3500,
      "teachers": 320,
      "dsi": 15,
      "phd_teachers": 45,
      "professors": 28,
      "books": 50000,
      "textbooks": 3000,
      "study": 1500,
      "methodological": 800,
      "monograph": 200
    }
  },
  "meta": {"locale": "uz"}
}
```

---

## Menus (Navigatsiya)

Menyu tuzilmasi 3 darajali: **Menu → Submenu → Multimenu**

### `GET /menus`

To'liq menyu daraxti (nested).

**Javob:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Insitut haqida",
      "slug": "institut-haqida",
      "link": null,
      "menu_type": "default",
      "position": "header",
      "image": "https://sies.test/storage/menus/img.webp",
      "order": 1,
      "submenus": [
        {
          "id": 1,
          "title": "Tarix",
          "slug": "tarix",
          "link": null,
          "type": "default",
          "image": null,
          "order": 1,
          "multimenus": [
            {
              "id": 1,
              "title": "Institut tarixi",
              "slug": "institut-tarixi",
              "link": null,
              "image": null,
              "order": 1
            }
          ]
        }
      ]
    }
  ],
  "meta": {"locale": "uz"}
}
```

### `GET /menus/{slug}`

Bitta menyu va uning submenyulari.

| Parameter | Tur | Tavsif |
|-----------|-----|--------|
| `slug` | string | Menyu slugi (joriy tilda yoki uz) |

### `GET /menus/{menu_slug}/{submenu_slug}`

Submenyu va uning multimenyulari.

---

## Pages (Sahifalar)

### `GET /pages`

Sahifalar ro'yxati (filtrlash va saralash bilan).

**Query parametrlari:**

| Parameter | Tur | Default | Tavsif |
|-----------|-----|---------|--------|
| `type` | string | — | Sahifa turi: `blog`, `faculty`, `department`, `center`, `section`, `default` |
| `menu_id` | integer | — | Menyu ID bo'yicha filtr |
| `submenu_id` | integer | — | Submenyu ID bo'yicha filtr |
| `multimenu_id` | integer | — | Multimenyu ID bo'yicha filtr |
| `sort` | string | `-date` | Saralash: `date`, `-date`, `views`, `-views`, `order`, `id`, `created_at`. `-` prefix = descending |
| `per_page` | integer | 15 | Sahifadagi elementlar (max 50) |
| `page` | integer | 1 | Sahifa raqami |

**Javob (PageList):**
```json
{
  "data": [
    {
      "id": 42,
      "title": "Yangi o'quv yili",
      "slug": "yangi-oquv-yili",
      "excerpt": "2026-yil uchun yangi o'quv yili...",
      "image": "https://sies.test/storage/pages/news.webp",
      "date": "2026-02-15",
      "views": 1250,
      "page_type": "blog",
      "tags": [
        {"id": 1, "name": "Yangiliklar", "slug": "yangiliklar", "image": null}
      ]
    }
  ],
  "meta": {"locale": "uz", "current_page": 1, "per_page": 15, "total": 87, "last_page": 6},
  "links": {"first": "...?page=1", "next": "...?page=2", "prev": null, "last": "...?page=6"}
}
```

### `GET /pages/{id}`

Sahifa to'liq ma'lumot (ID bo'yicha). View count +1 qilinadi.

**Javob (PageDetail):**
```json
{
  "data": {
    "id": 42,
    "title": "Yangi o'quv yili",
    "slug": "yangi-oquv-yili",
    "content": "<p>Sahifa kontenti HTML formatda...</p>",
    "image": "https://sies.test/storage/pages/news.webp",
    "images": [
      "https://sies.test/storage/pages/gallery/img1.webp",
      "https://sies.test/storage/pages/gallery/img2.webp"
    ],
    "date": "2026-02-15",
    "views": 1251,
    "page_type": "blog",
    "activity": false,
    "tags": [
      {"id": 1, "name": "Yangiliklar", "slug": "yangiliklar", "image": null}
    ],
    "files": [
      {"id": 1, "name": "Hujjat.pdf", "url": "https://sies.test/storage/files/hujjat.pdf"}
    ],
    "staff_categories": [
      {
        "id": 1,
        "title": "Rahbariyat",
        "staff_members": [
          {
            "id": 10,
            "name": "Aliyev Vali",
            "position": "Kafedra mudiri",
            "image": "https://sies.test/storage/staff_members/aliyev.webp"
          }
        ],
        "children": [
          {
            "id": 2,
            "title": "O'qituvchilar",
            "staff_members": [ ... ],
            "children": []
          }
        ]
      }
    ],
    "department_history": {
      "id": 1,
      "content": "<p>Kafedra 1995-yilda tashkil etilgan...</p>"
    },
    "menu": {"id": 1, "title": "Institut haqida", "slug": "institut-haqida"},
    "submenu": {"id": 1, "title": "Tarix", "slug": "tarix"},
    "multimenu": {"id": 1, "title": "Institut tarixi", "slug": "institut-tarixi"},
    "created_at": "2026-01-10T10:00:00+05:00",
    "updated_at": "2026-02-15T14:30:00+05:00"
  },
  "meta": {"locale": "uz"}
}
```

### `GET /pages/by-path/{menu_slug}/{submenu_slug}/{multimenu_slug}`

Slug yo'l bo'yicha sahifalar ro'yxati. Mobil appda navigatsiya uchun qulay.

| Parameter | Tur | Tavsif |
|-----------|-----|--------|
| `menu_slug` | string | Menyu slugi |
| `submenu_slug` | string | Submenyu slugi |
| `multimenu_slug` | string | Multimenyu slugi |
| `per_page` | integer | Sahifadagi elementlar (max 50) |

**Javob:** PageList (paginated) — yuqoridagi `GET /pages` bilan bir xil format.

### `GET /pages/by-path/{menu}/{submenu}/{multimenu}/{page_slug}`

Slug yo'l bo'yicha bitta sahifa detail. `page_slug` sifatida slug, id yoki `slug-id` formati qabul qilinadi.

**Javob:** PageDetail — yuqoridagi `GET /pages/{id}` bilan bir xil format.

---

## Search (Qidiruv)

### `GET /search?q={query}`

Sahifa sarlavhalari bo'yicha qidiruv (barcha tillarda).

| Parameter | Tur | Tavsif |
|-----------|-----|--------|
| `q` | string | Qidiruv so'zi (min 2 belgi) |
| `per_page` | integer | Sahifadagi elementlar (max 50) |

**Javob:** PageList (paginated)

**Xatolik (qisqa so'z):**
```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Search query must be at least 2 characters."
  },
  "meta": {"timestamp": "..."}
}
```

---

## Tags (Teglar)

### `GET /tags`

Barcha teglar ro'yxati (sahifalar soni bilan).

```json
{
  "data": [
    {
      "id": 1,
      "name": "Yangiliklar",
      "slug": "yangiliklar",
      "image": null,
      "pages_count": 42
    }
  ],
  "meta": {"locale": "uz"}
}
```

### `GET /tags/{slug}`

Teg bo'yicha sahifalar ro'yxati (paginated).

**Javob:** PageList (paginated)

---

## Staff (Xodimlar)

### `GET /staff/{id}`

Xodim to'liq ma'lumot.

```json
{
  "data": {
    "id": 10,
    "name": "Aliyev Vali Karimovich",
    "position": "Kafedra mudiri, iqtisod fanlari doktori",
    "content": "<p>Biografiya va ilmiy faoliyat...</p>",
    "image": "https://sies.test/storage/staff_members/aliyev.webp",
    "page_id": 15,
    "category": {
      "id": 1,
      "title": "Rahbariyat",
      "staff_members": [],
      "children": []
    }
  },
  "meta": {"locale": "uz"}
}
```

### `GET /pages/{id}/staff`

Sahifaning barcha xodimlari (kategoriyalar bilan, nested).

```json
{
  "data": [
    {
      "id": 1,
      "title": "Rahbariyat",
      "staff_members": [
        {
          "id": 10,
          "name": "Aliyev Vali",
          "position": "Kafedra mudiri",
          "image": "https://sies.test/storage/staff_members/aliyev.webp"
        }
      ],
      "children": [
        {
          "id": 2,
          "title": "O'qituvchilar",
          "staff_members": [ ... ],
          "children": []
        }
      ]
    }
  ],
  "meta": {"locale": "uz"}
}
```

---

## Symbols (Davlat ramzlari)

### `GET /symbols`

Barcha davlat ramzlari (bayroq, gerb, madhiya).

```json
{
  "data": [
    {
      "id": 1,
      "title": "O'zbekiston Respublikasi Davlat bayrog'i",
      "slug": "davlat-bayrogi",
      "content": "<p>Bayroq haqida ma'lumot...</p>",
      "image": "https://sies.test/storage/symbols/flag.webp"
    }
  ],
  "meta": {"locale": "uz"}
}
```

### `GET /symbols/{slug}`

Bitta ramz to'liq ma'lumot.

---

## Stats (Statistika)

### `GET /stats`

Institut statistikasi.

```json
{
  "data": {
    "campus_area": 25,
    "green_area": 15,
    "faculties": 5,
    "departments": 23,
    "centers": 8,
    "employees": 450,
    "leadership": 12,
    "scientific": 85,
    "technical": 120,
    "students": 8500,
    "male_students": 5000,
    "female_students": 3500,
    "teachers": 320,
    "dsi": 15,
    "phd_teachers": 45,
    "professors": 28,
    "books": 50000,
    "textbooks": 3000,
    "study": 1500,
    "methodological": 800,
    "monograph": 200
  },
  "meta": {"locale": "uz"}
}
```

---

# AUTH API

## Login

### `POST /auth/login`

**Rate limit:** 5 urinish/daqiqa

**Request body:**
```json
{
  "email": "admin@sies.uz",
  "password": "secret123",
  "device_name": "iPhone 15 Pro"
}
```

| Maydon | Tur | Majburiy | Tavsif |
|--------|-----|----------|--------|
| `email` | string | Ha | Email manzil |
| `password` | string | Ha | Parol (min 6 belgi) |
| `device_name` | string | Yo'q | Qurilma nomi (default: "mobile-app") |

**Muvaffaqiyatli javob (200):**
```json
{
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@sies.uz",
      "profile_photo_url": null,
      "roles": ["super-admin"],
      "permissions": ["view_any_page", "create_page", "..."],
      "last_seen_at": "2026-02-18T10:00:00+05:00",
      "created_at": "2025-09-01T00:00:00+05:00"
    },
    "token": "1|abc123def456ghi789...",
    "token_type": "Bearer"
  },
  "meta": {"locale": "uz"}
}
```

**Xatolik (401):**
```json
{
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "The provided credentials are incorrect."
  },
  "meta": {"timestamp": "2026-02-18T15:30:00+05:00"}
}
```

---

## Logout

### `POST /auth/logout`

**Header:** `Authorization: Bearer {token}`

**Javob (200):**
```json
{
  "data": {"message": "Successfully logged out."},
  "meta": {"locale": "uz"}
}
```

---

## Profile

### `GET /auth/profile`

Joriy foydalanuvchi ma'lumotlari.

**Header:** `Authorization: Bearer {token}`

**Javob (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@sies.uz",
    "profile_photo_url": null,
    "roles": ["super-admin"],
    "permissions": ["view_any_page", "create_page", "..."],
    "last_seen_at": "2026-02-18T10:00:00+05:00",
    "created_at": "2025-09-01T00:00:00+05:00"
  },
  "meta": {"locale": "uz"}
}
```

### `PUT /auth/profile`

Profil yangilash.

**Header:** `Authorization: Bearer {token}`

**Request body:**
```json
{
  "name": "Yangi Ism",
  "email": "yangi@sies.uz",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

| Maydon | Tur | Majburiy | Tavsif |
|--------|-----|----------|--------|
| `name` | string | Yo'q | Yangi ism (max 255) |
| `email` | string | Yo'q | Yangi email (unique) |
| `password` | string | Yo'q | Yangi parol (min 8, confirmed) |
| `password_confirmation` | string | Yo'q | Parol tasdiqi |

**Javob:** User obyekti (yuqoridagi profile bilan bir xil format)

---

# ADMIN API

Barcha admin endpointlar `Authorization: Bearer {token}` header talab qiladi.
Foydalanuvchining roli va ruxsatlari (permissions) tekshiriladi.

---

## Admin Pages (Sahifalar CRUD)

Admin API sahifa ma'lumotlarini **barcha tillarda** qaytaradi (`title_uz`, `title_ru`, `title_en`).

### `GET /admin/pages`

**Query parametrlari:**

| Parameter | Tur | Tavsif |
|-----------|-----|--------|
| `type` | string | Sahifa turi bo'yicha filtr |
| `menu_id` | integer | Menyu bo'yicha filtr |
| `per_page` | integer | Sahifadagi elementlar (max 50) |

**Javob (AdminPage paginated):**
```json
{
  "data": [
    {
      "id": 42,
      "title_uz": "Yangi o'quv yili",
      "title_ru": "Новый учебный год",
      "title_en": "New academic year",
      "slug_uz": "yangi-oquv-yili",
      "slug_ru": "novyj-uchebnyj-god",
      "slug_en": "new-academic-year",
      "content_uz": "<p>O'zbek kontenti...</p>",
      "content_ru": "<p>Русский контент...</p>",
      "content_en": "<p>English content...</p>",
      "image": "https://sies.test/storage/pages/news.webp",
      "images": [],
      "date": "2026-02-15",
      "status": "published",
      "page_type": "blog",
      "activity": false,
      "views": 1250,
      "order": 1,
      "menu_id": 1,
      "submenu_id": 1,
      "multimenu_id": 1,
      "tag": null,
      "tags": [1, 3],
      "created_by": {"id": 1, "name": "Admin"},
      "updated_by": {"id": 1, "name": "Admin"},
      "created_at": "2026-01-10T10:00:00+05:00",
      "updated_at": "2026-02-15T14:30:00+05:00"
    }
  ],
  "meta": {"locale": "uz", "current_page": 1, "per_page": 15, "total": 42, "last_page": 3},
  "links": { ... }
}
```

### `GET /admin/pages/{id}`

Sahifa detail (barcha tillar bilan).

### `POST /admin/pages`

Yangi sahifa yaratish.

**Content-Type:** `multipart/form-data` (rasm yuklash uchun) yoki `application/json`

**Request body:**

| Maydon | Tur | Majburiy | Tavsif |
|--------|-----|----------|--------|
| `title_uz` | string | Ha | Sarlavha (uz) - max 500 |
| `title_ru` | string | Yo'q | Sarlavha (ru) |
| `title_en` | string | Yo'q | Sarlavha (en) |
| `content_uz` | string | Yo'q | Kontent HTML (uz) |
| `content_ru` | string | Yo'q | Kontent HTML (ru) |
| `content_en` | string | Yo'q | Kontent HTML (en) |
| `slug_uz` | string | Yo'q | URL slug (uz) |
| `slug_ru` | string | Yo'q | URL slug (ru) |
| `slug_en` | string | Yo'q | URL slug (en) |
| `menu_id` | integer | Ha | Menyu ID |
| `submenu_id` | integer | Ha | Submenyu ID |
| `multimenu_id` | integer | Ha | Multimenyu ID |
| `page_type` | string | Ha | `blog`, `faculty`, `department`, `center`, `section`, `default` |
| `status` | boolean | Yo'q | Holat |
| `date` | string | Yo'q | Sana (YYYY-MM-DD) |
| `image` | file | Yo'q | Rasm fayli (max 2MB) |
| `activity` | boolean | Yo'q | Faollik holati |
| `tags` | array | Yo'q | Tag IDlar massivi: `[1, 3, 5]` |

**Javob (201):** AdminPage obyekti

### `PUT /admin/pages/{id}`

Sahifani yangilash. Faqat o'zgargan maydonlarni yuborish mumkin.

**Javob (200):** AdminPage obyekti

### `DELETE /admin/pages/{id}`

Sahifani o'chirish.

**Javob (200):**
```json
{
  "data": {"message": "Page deleted successfully."},
  "meta": {"locale": "uz"}
}
```

---

## Admin Staff (Xodimlar CRUD)

### `GET /admin/staff`

| Parameter | Tur | Tavsif |
|-----------|-----|--------|
| `page_id` | integer | Sahifa bo'yicha filtr |
| `staff_category_id` | integer | Kategoriya bo'yicha filtr |
| `per_page` | integer | Sahifadagi elementlar (max 50) |

**Javob (AdminStaffMember paginated):**
```json
{
  "data": [
    {
      "id": 10,
      "name_uz": "Aliyev Vali Karimovich",
      "name_ru": "Алиев Вали Каримович",
      "name_en": "Aliyev Vali Karimovich",
      "position_uz": "Kafedra mudiri",
      "position_ru": "Заведующий кафедрой",
      "position_en": "Head of Department",
      "content_uz": "<p>Biografiya...</p>",
      "content_ru": "<p>Биография...</p>",
      "content_en": "<p>Biography...</p>",
      "image": "https://sies.test/storage/staff_members/aliyev.webp",
      "page_id": 15,
      "staff_category_id": 1,
      "user_id": null,
      "created_by": {"id": 1, "name": "Admin"},
      "updated_by": {"id": 1, "name": "Admin"},
      "created_at": "2026-01-10T10:00:00+05:00",
      "updated_at": "2026-02-15T14:30:00+05:00"
    }
  ],
  "meta": { ... },
  "links": { ... }
}
```

### `GET /admin/staff/{id}`

Xodim detail (barcha tillar).

### `POST /admin/staff`

**Content-Type:** `multipart/form-data` (rasm yuklash uchun)

| Maydon | Tur | Majburiy | Tavsif |
|--------|-----|----------|--------|
| `name_uz` | string | Ha | Ism (uz) - max 255 |
| `name_ru` | string | Yo'q | Ism (ru) |
| `name_en` | string | Yo'q | Ism (en) |
| `position_uz` | string | Yo'q | Lavozim (uz) |
| `position_ru` | string | Yo'q | Lavozim (ru) |
| `position_en` | string | Yo'q | Lavozim (en) |
| `content_uz` | string | Yo'q | Biografiya HTML (uz) |
| `content_ru` | string | Yo'q | Biografiya HTML (ru) |
| `content_en` | string | Yo'q | Biografiya HTML (en) |
| `page_id` | integer | Ha | Bog'langan sahifa ID |
| `staff_category_id` | integer | Yo'q | Kategoriya ID |
| `user_id` | integer | Yo'q | Foydalanuvchi ID (ixtiyoriy) |
| `image` | file | Yo'q | Rasm (max 2MB) |

**Javob (201):** AdminStaffMember obyekti

### `PUT /admin/staff/{id}`

Xodimni yangilash.

### `DELETE /admin/staff/{id}`

Xodimni o'chirish.

---

## Admin Activities (Faoliyat jurnali)

### `GET /admin/activities`

Tizim faoliyat jurnali (read-only).

| Parameter | Tur | Tavsif |
|-----------|-----|--------|
| `log_name` | string | Log nomi bo'yicha filtr (masalan: `page`) |
| `subject_type` | string | Model turi bo'yicha filtr |
| `causer_id` | integer | Foydalanuvchi bo'yicha filtr |
| `per_page` | integer | Sahifadagi elementlar (max 50) |

**Javob:**
```json
{
  "data": [
    {
      "id": 150,
      "log_name": "page",
      "description": "updated",
      "subject_type": "App\\Models\\Page",
      "subject_id": 42,
      "event": "updated",
      "properties": {
        "attributes": {"title_uz": "Yangi sarlavha"},
        "old": {"title_uz": "Eski sarlavha"}
      },
      "causer": {"id": 1, "name": "Admin User"},
      "created_at": "2026-02-18T10:00:00+05:00"
    }
  ],
  "meta": { ... },
  "links": { ... }
}
```

---

# Ma'lumot modellari (Data Models)

## Sahifa turlari (`page_type`)

| Tur | Tavsif | Xususiyat |
|-----|--------|-----------|
| `blog` | Yangiliklar, e'lonlar | Ro'yxat + detail |
| `faculty` | Fakultetlar | Ro'yxat + detail + xodimlar |
| `department` | Kafedralar | Ro'yxat + detail + xodimlar + tarix |
| `center` | Markazlar | Single page + xodimlar |
| `section` | Bo'limlar | Single page + xodimlar |
| `default` | Oddiy sahifa | Single page |

## Menyu pozitsiyalari (`position`)

| Qiymat | Tavsif |
|--------|--------|
| `header` | Asosiy navigatsiya |
| `quick_links` | Tezkor havolalar |

## Rasmlar haqida

- Barcha rasm URL-lari **absolute** formatda qaytariladi
- `image` — bitta rasm (string yoki null)
- `images` — rasmlar massivi (array yoki bo'sh array)
- Agar rasm mavjud bo'lmasa `null` qaytariladi

## Content haqida

- `content` maydonlari **HTML** formatda saqlanadi
- Mobil appda WebView yoki HTML parser ishlatish kerak
- `excerpt` maydonida HTML teglar olib tashlangan qisqa matn (max 200 belgi) qaytariladi

---

# Mobile App uchun tavsiyalar

## Asosiy ekranlar va ularga mos endpointlar

| Ekran | Endpoint | Izoh |
|-------|----------|------|
| Splash / Loading | `GET /stats` | Tez javob, ulanishni tekshirish |
| Bosh sahifa | `GET /homepage` | Bitta so'rovda barcha ma'lumot |
| Menyu (drawer/tab) | `GET /menus` | Cache qilish tavsiya etiladi |
| Yangiliklar ro'yxati | `GET /pages?type=blog&sort=-date` | Pagination bilan |
| Yangilik detail | `GET /pages/{id}` | Yoki `by-path` orqali |
| Qidiruv | `GET /search?q=...` | Min 2 belgi |
| Teglar | `GET /tags` → `GET /tags/{slug}` | |
| Fakultet/Kafedra ro'yxati | `GET /pages?type=faculty` | |
| Xodimlar | `GET /pages/{id}/staff` | Kategoriyalar bilan |
| Xodim profili | `GET /staff/{id}` | |
| Davlat ramzlari | `GET /symbols` | |
| Statistika | `GET /stats` | |
| Login | `POST /auth/login` | Token saqlash |
| Profil | `GET /auth/profile` | |

## Cache strategiyasi (tavsiya)

| Ma'lumot | Cache muddati | Sabab |
|----------|---------------|-------|
| Menyu daraxti | 1 soat | Kamdan-kam o'zgaradi |
| Homepage | 30 daqiqa | Yangiliklar tez-tez qo'shiladi |
| Statistika | 2 soat | Kamdan-kam o'zgaradi |
| Ramzlar | 24 soat | Deyarli o'zgarmaydi |
| Teglar | 2 soat | Kamdan-kam o'zgaradi |
| Sahifa ro'yxati | 5 daqiqa | Tez-tez yangilanadi |
| Sahifa detail | 15 daqiqa | O'rtacha |

## Til boshqaruvi

- App ichida til tanlash ekrani qo'ying (uz/ru/en)
- Tanlangan tilni `UserDefaults` / `SharedPreferences` da saqlang
- Har bir API so'rovda `?lang=uz` query param yuboring
- Yoki global `Accept-Language` header o'rnating

## Token boshqaruvi

1. Login → token oling → secure storage da saqlang
2. Har bir admin so'rovda `Authorization: Bearer {token}` yuboring
3. 401 javob kelsa → login ekraniga yo'naltiring
4. Logout → token o'chiring

---

# Barcha endpointlar jadvali

| # | Method | URL | Auth | Tavsif |
|---|--------|-----|------|--------|
| 1 | GET | `/homepage` | Yo'q | Bosh sahifa |
| 2 | GET | `/menus` | Yo'q | Menyu daraxti |
| 3 | GET | `/menus/{slug}` | Yo'q | Bitta menyu |
| 4 | GET | `/menus/{menu}/{submenu}` | Yo'q | Submenyu |
| 5 | GET | `/pages` | Yo'q | Sahifalar ro'yxati |
| 6 | GET | `/pages/{id}` | Yo'q | Sahifa detail |
| 7 | GET | `/pages/by-path/{m}/{s}/{mm}` | Yo'q | Slug bo'yicha ro'yxat |
| 8 | GET | `/pages/by-path/{m}/{s}/{mm}/{p}` | Yo'q | Slug bo'yicha detail |
| 9 | GET | `/search?q=...` | Yo'q | Qidiruv |
| 10 | GET | `/tags` | Yo'q | Teglar |
| 11 | GET | `/tags/{slug}` | Yo'q | Teg sahifalari |
| 12 | GET | `/staff/{id}` | Yo'q | Xodim detail |
| 13 | GET | `/pages/{id}/staff` | Yo'q | Sahifa xodimlari |
| 14 | GET | `/symbols` | Yo'q | Ramzlar |
| 15 | GET | `/symbols/{slug}` | Yo'q | Ramz detail |
| 16 | GET | `/stats` | Yo'q | Statistika |
| 17 | POST | `/auth/login` | Yo'q | Login |
| 18 | POST | `/auth/logout` | Ha | Logout |
| 19 | GET | `/auth/profile` | Ha | Profil |
| 20 | PUT | `/auth/profile` | Ha | Profil yangilash |
| 21 | GET | `/admin/pages` | Ha | Admin sahifalar |
| 22 | GET | `/admin/pages/{id}` | Ha | Admin sahifa detail |
| 23 | POST | `/admin/pages` | Ha | Sahifa yaratish |
| 24 | PUT | `/admin/pages/{id}` | Ha | Sahifa tahrirlash |
| 25 | DELETE | `/admin/pages/{id}` | Ha | Sahifa o'chirish |
| 26 | GET | `/admin/staff` | Ha | Admin xodimlar |
| 27 | GET | `/admin/staff/{id}` | Ha | Admin xodim detail |
| 28 | POST | `/admin/staff` | Ha | Xodim yaratish |
| 29 | PUT | `/admin/staff/{id}` | Ha | Xodim tahrirlash |
| 30 | DELETE | `/admin/staff/{id}` | Ha | Xodim o'chirish |
| 31 | GET | `/admin/activities` | Ha | Faoliyat jurnali |

> Barcha URL-lar `https://sies.test/api/v1` prefixi bilan boshlanadi.
