**User Issue Management Backend (PHP)** 
It covers setup, structure, API testing, and dependencies:

---

```markdown
# 🛠️ User Issue Management System (PHP Backend)

This is a backend system for managing issue calls submitted by users. It supports 3 roles:

- **Public Users**: Can submit new issue calls
- **Manager**: Can view, assign, and close calls
- **Support Engineer**: Can view and resolve assigned calls

JWT-based authentication is used for manager and engineer routes. Built using plain PHP + MySQL (via PDO).

---

## 📁 Project Structure

```

project-root/
│
│
├── vendor/                     # Composer dependencies
│
├── api/
|   ├── config.php               # DB connection & JWT secret
|   |
│   ├── calls/
│   │   └── create.php          # Public: Create call
│   │
│   ├── manager/
│   │   ├── profile_get.php
│   │   ├── profile_put.php
│   │   ├── calls_list.php
│   │   ├── call_details.php
│   │   ├── assign_engineer.php
│   │   ├── close_call.php
│   │   └── support_engineers.php
│   │
│   ├── engineer/
│   │   ├── profile_get.php
│   │   ├── profile_put.php
│   │   ├── calls_list.php
│   │   └── resolve_call.php
│   │
│   ├── auth/
│   │   ├── manager_login.php
│   │   └── engineer_login.php
│
├── middleware/
│   └── auth.php                # JWT validation middleware
│
├── composer.json               # Composer dependencies
└── README.md                   # You're here!

````

---

## ⚙️ Setup Instructions

### 1. Install PHP & Composer

Make sure you have PHP 8+ and Composer installed. On WSL Ubuntu:
```bash
sudo apt install php composer
````

### 2. Install JWT Library

Inside your project directory:

```bash
composer require firebase/php-jwt
```

### 3. Configure Database

* Create a MySQL database called `issue`
* Import the schema and demo data:

```sql
-- Run this inside MySQL CLI or phpMyAdmin
SOURCE ./schema-and-demo-data.sql
```

Or copy SQL from the setup instructions provided earlier.

### 4. Update `./API/config.php`

Edit your DB credentials and set a strong `$jwt_secret` (32+ chars):

```php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "issue";
$jwt_secret = "your-strong-secret-key-here";
```

---

## 🚀 API Testing

### 🔓 Public: Submit a Call
one-liner
```bash
curl -X POST http://localhost/users-issue/api/calls/create.php -H "Content-Type: application/json" -d '{"problem_type": "Login Problem", "user_name": "Jane Doe", "user_email": "jane@example.com", "user_phone": "123-456-7890", "description": "Unable to log in since yesterday."}'
```
or full
```bash
curl -X POST http://localhost/users-issue/api/calls/create.php \
-H "Content-Type: application/json" \
-d '{"problem_type": "Login", "user_name": "Jane", "user_email": "jane@example.com", "user_phone": "1234567890", "description": "Cannot login"}'
```

### 🔐 Login to Get JWT

#### Manager:

```bash
curl -X POST http://localhost/users-issue/api/auth/manager_login.php \
-H "Content-Type: application/json" \
-d '{"email":"manager@example.com","password":"yourpass"}'
```

#### Engineer:

```bash
curl -X POST http://localhost/users-issue/api/auth/engineer_login.php \
-H "Content-Type: application/json" \
-d '{"email":"engineer@example.com","password":"yourpass"}'
```

You’ll get a token like:

```json
{ "jwt": "eyJhbGciOi..." }
```

Use it in `Authorization: Bearer` header for protected requests.

---

## 🔐 Protected API Examples

### 🔸 Manager: View Pending Calls

```bash
curl -X GET "http://localhost/users-issue/api/manager/calls_list.php?status=PENDING" \
-H "Authorization: Bearer YOUR_JWT" \
-H "Content-Type: application/json"
```

### 🔸 Engineer: Resolve a Call

```bash
curl -X POST http://localhost/users-issue/api/engineer/resolve_call.php \
-H "Authorization: Bearer YOUR_JWT" \
-H "Content-Type: application/json" \
-d '{"call_id": 5, "resolution_status": "RESOLVED", "issue_type": "Router Issue", "remarks": "Replaced router"}'
```

---

## ✅ Status Codes

| Code | Meaning                    |
| ---- | -------------------------- |
| 200  | OK / Success               |
| 201  | Created (for new call)     |
| 400  | Bad Request                |
| 401  | Unauthorized (invalid JWT) |
| 403  | Forbidden (wrong role)     |
| 404  | Not Found (call/user)      |
| 500  | Internal Server Error      |

---

## 📌 Notes

* Use **Postman** or `curl` for testing
* All input/output is in **JSON**
* Manager and engineer data is stored in a shared `users` table
* Use strong secrets for JWT
* Each role has separate login + dashboard logic

---

## 🔧 To-Do (Optional Improvements)

* Password hashing (bcrypt)
* Refresh tokens
* Logging (who closed/resolved)
* Email notifications to users
* Frontend UI (HTML + JS)

---

## 👨‍💻 Author

Koushik – 2026

```

---

Let me know if you want this saved as a real `README.md` file in your project, or need a version with Markdown preview!
```
