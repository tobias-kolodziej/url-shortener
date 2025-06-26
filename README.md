# URL Shortener

A modern, lightweight URL shortening service built with Symfony and Vue.js.  
The backend provides a clean API, while the frontend is a Single Page Application (SPA) for managing and creating short URLs.

---

## Features

- Shorten any valid URL into a short code
- Automatic redirection to original URLs
- Track click counts for each short link
- Idempotent: same URL always returns same code
- Clean SPA frontend with Vue Router and Tailwind CSS
- No build-step dependencies for backend routing

---

## Tech Stack

- **Backend:** Symfony 6.1, Doctrine ORM, PHP 8.3
- **Frontend:** Vue 3 + Vite + Tailwind CSS 4
- **Database:** Doctrine ORM with MariaDB 10.11 (SQLite/MySQL possible)
- **Tooling:** Docker, DDEV

---

## Getting Started

### Prerequisites

- Docker
- DDEV

### Clone and run

```bash
git clone https://github.com/your-user/url-shortener.git
cd url-shortener
ddev start
ddev ssh
composer install

cd frontend
npm install
npm run dev
```

---

## Frontend Access

### Development mode (with HMR)

```bash
npm run dev
```

Open:

- http://localhost:5173  
  or  
- https://url-shortener.ddev.site:5173 (if using DDEV TLS proxy)

This uses Vite’s dev server and hot module replacement. Backend requests to `/api/...` are proxied.

### Production mode

```bash
npm run build
```

This builds the frontend into `public/spa`.  
No DDEV restart is required.

Open in browser:

- https://url-shortener.ddev.site

Vue Router handles navigation client-side. Symfony forwards unknown routes to `spa/index.html`.

---

## Routing Overview

- All requests go through `public/index.php` (Symfony front controller)
- `/api/...` routes are handled by Symfony controllers
- `/abc123` routes are checked as short codes and resolved or redirected
- All other non-API routes fall back to `public/spa/index.html`

---

## API Usage

### Create short URL

**POST** `/api/urls`

```json
{
  "url": "https://example.com"
}
```

**Response**

```json
{
  "shortUrl": "https://url-shortener.ddev.site/abc123"
}
```

### Redirect short code

Visit:  
`https://url-shortener.ddev.site/abc123`

### List all URLs

**GET** `/api/urls`

```json
[
  {
    "shortCode": "abc123",
    "originalUrl": "https://example.com",
    "clickCount": 42,
    "createdAt": "2025-06-26T12:34:56+00:00"
  }
]
```

### Get details for one code

**GET** `/api/urls/{shortCode}`

Returns entry or 404.

---

## Tests

```bash
ddev ssh
php bin/phpunit
```