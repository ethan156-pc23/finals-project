# Cebu Emergency Hotlines

A public directory of emergency hotlines for every city/municipality in
Cebu Province, plus an admin panel to manage them. Built from the Figma
prototype (fileKey `Sq2ZU3rgJBs2izpQvhJ42G`) with a PHP + MySQL backend.

## 1. Setup (XAMPP / WAMP / any Apache+PHP+MySQL stack)

1. **Copy the project** into your server's document root, e.g.
   `C:\xampp\htdocs\cebu-emergency-hotlines\` (or `htdocs/` directly if you
   want it at the root URL).
2. **Import the database.** In phpMyAdmin (or the `mysql` CLI), run
   `backend/schema.sql`. It creates the `cebu_emergency` database, all
   three tables, and seeds the categories + every hotline you supplied.
3. **Set your DB credentials** in `backend/core/db.php` (defaults match a
   stock XAMPP install: host `localhost`, user `root`, empty password).
4. Visit `http://localhost/frontend/` (or
   `http://localhost/cebu-emergency-hotlines/frontend/` for a subfolder
   install) — you should see the
   hotline directory.

## 2. Logging in as admin

- URL: `/frontend/auth/login.php`
- Mobile Number: `09123456789`
- Password: `admin123`

That seed account's password hash was regenerated with PHP's own
`password_hash()` — the hash originally pasted in the SQL didn't actually
match `admin123`, so this build fixes it. From the dashboard you can add,
edit, and delete hotlines; the category badge is derived automatically
from whichever category you pick.

## 3. What's included

```
frontend/                  User-facing pages and browser assets
  auth/                    Login, signup, and password reset pages
  includes/                Shared page headers and footer
  assets/
    css/                   Public and admin stylesheets
    images/                Project logos
    js/                    Browser-side behavior
backend/
  api/                     JSON endpoints and form/logout handlers
  core/                    Database connection, helpers, and SMS service
  schema.sql               Tables + seed data (with the fixed admin hash)
```

## 4. Decisions worth knowing about

- **One users table, one login form, role-based redirect.** Citizens can
  request a one-time code by mobile number or email, while the seeded admin
  can still log in with the existing password;
  `role` in the `users` table decides whether you land on the public
  directory or `/frontend/dashboard.php`.
- **The "Others" category.** Your `categories` table stores
  `name = 'Disaster'` / `slug = 'others'`. The Figma chip label for that
  bucket reads "Others," so `category_display_name()` in
  `backend/core/functions.php` is the one place that mapping lives — the
  hotline card badges still show `tag_label` (e.g. `DISASTER`) exactly as
  seeded.
- **A city/municipality filter was added.** The mobile mockup only shows
  category chips, but your real dataset spans ~80 cities/municipalities, so a
  dropdown filter was necessary to make the directory usable.
- **Sign-up uses OTP verification.** Mobile and email signup request a
  six-digit code that expires after 10 minutes. Local XAMPP testing displays
  the generated code on the page; email delivery uses PHP `mail()` when
  configured. Production SMS delivery requires an SMS provider such as
  Semaphore or Twilio and its API credentials.
- **Forgot-password has no verification step**, matching the two-field
  form in the prototype (mobile number + new password, no code). Worth
  adding a verification step before this goes anywhere near production.
- **The admin Users page** shows registered mobile/email accounts, roles,
  registration dates, and whether each user was active within the last five
  minutes. Existing databases are upgraded automatically with `last_seen_at`.
- **Icons are hand-drawn inline SVGs**, not exports from Figma — the
  Figma asset URLs aren't reachable from this environment's network, so
  common glyphs (search, phone, person, envelope, checkmark) were
  recreated as clean generic icons rather than guessed-at custom ones.
  Swap in your own exports from Figma any time.
- **Security basics included:** PDO prepared statements everywhere,
  `password_hash`/`password_verify`, session regeneration on login, CSRF
  tokens on every admin form/POST, and output escaping via the `e()`
  helper.

## 5. Extending it

- Category management UI (currently categories are fixed — add rows to
  the `categories` table directly if you need a 5th).
- Real SMS/email OTP verification for sign-up and password reset.
- A "favorites" or "recently called" feature for logged-in citizens,
  since the `users` table already supports accounts beyond the admin.
