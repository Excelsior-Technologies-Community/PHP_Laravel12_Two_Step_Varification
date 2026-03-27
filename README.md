# 🔐 PHP Laravel 12 - Two Step Verification (2FA)

This project demonstrates an **advanced Two-Step Verification (2FA) system** built using **Laravel 12** and **Laravel Breeze**.

When a user logs in, a **6-digit OTP (One Time Password)** is sent to their email address. The user must verify the OTP before accessing the dashboard.

This ensures an **extra layer of security** beyond the normal email and password login.

---

# 🚀 Features

* **Laravel 12 Framework** – Latest performance and security improvements
* **Laravel Breeze Authentication** – Simple login and registration system
* **Two-Step Verification (2FA)** – Email-based OTP verification
* **Custom Middleware Protection** – Prevents dashboard access without OTP verification
* **OTP Expiration Logic** – OTP is valid for **10 minutes only**
* **Resend OTP Functionality** – Users can request a new OTP if needed
* **Modern UI** – Clean verification interface built with **Tailwind CSS**

---

# 🛠️ Installation Guide

## 1️⃣ Clone Project & Install Dependencies

```bash
composer create-project laravel/laravel PHP_Laravel12_Two_Step_Varification

cd PHP_Laravel12_Two_Step_Varification

composer install
npm install
```

---

# 2️⃣ Environment Setup

Create your `.env` file and generate the application key.

```bash
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`.

```env
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

---

# 3️⃣ Install Laravel Breeze

Laravel Breeze is used for authentication (Login / Register).

```bash
composer require laravel/breeze --dev
php artisan breeze:install

npm install
npm run dev
```

---

# 4️⃣ Run Database Migration

Run migrations to create the necessary tables including OTP fields.

```bash
php artisan migrate
```

---

# 5️⃣ Email Configuration

Configure your mail service in `.env` to send OTP emails.

Example using **Mailtrap**:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="manavsanchela76@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

# 📖 How Two-Step Verification Works

### 1️⃣ User Login

The user logs in using their **email and password**.

### 2️⃣ OTP Generation

After successful login, the system generates a **6-digit OTP** and stores it in the database.

### 3️⃣ Email Notification

Laravel **Notification system** sends the OTP to the user's email.

### 4️⃣ Middleware Protection

A custom middleware ensures the user **cannot access the dashboard without verifying the OTP**.

### 5️⃣ OTP Verification

The user enters the OTP on the verification page.

If the OTP is correct:

* OTP will be cleared from the database
* The user is redirected to the **dashboard**

---

# 📂 Project Structure (Important Files)

| File Path                                      | Description                               |
| ---------------------------------------------- | ----------------------------------------- |
| `app/Models/User.php`                          | Contains OTP generation and reset methods |
| `app/Http/Middleware/TwoFactorVerify.php`      | Protects dashboard routes                 |
| `app/Http/Controllers/TwoFactorController.php` | Handles OTP verification and resend       |
| `app/Notifications/SendTwoFactorCode.php`      | Sends OTP email notification              |
| `resources/views/auth/verify.blade.php`        | Tailwind UI for OTP verification          |

---

# ⚡ Clear Cache Commands

If you encounter any issues after making changes, run:

```bash
php artisan optimize:clear
```

---

# ▶️ Run the Project

Start the development server:

```bash
php artisan serve
```

Open your browser and visit:

```
http://localhost:8000
```

---
#Output
<img width="852" height="492" alt="image" src="https://github.com/user-attachments/assets/64169743-c1c5-4074-898d-96cd7013189c" />
<img width="496" height="413" alt="image" src="https://github.com/user-attachments/assets/a0d279ef-59a7-4c61-bdd8-7603d4ed6ab3" />
<img width="423" height="530" alt="image" src="https://github.com/user-attachments/assets/24bb2a34-2c6e-4963-86dd-eba26822dfe7" />
<img width="1042" height="240" alt="image" src="https://github.com/user-attachments/assets/ee6d2eff-c3af-4284-ac0b-868073669a0b" />




# 👨‍💻 Developed By

**Manav Sanchela**

---

