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
```
---

## Prerequisit
```bash
sudo apt update
sudo apt install php composer
php -v
composer -V
composer require firebase/php-jwt
```
---

## ⚙️ Setup Instructions

### 1. Install PHP & Composer (ignore if already done)

Make sure you have PHP 8+ and Composer installed. On WSL / Ubuntu:
```bash
sudo apt install php composer
```

### 2. Install JWT Library

Inside your project directory:

```bash
composer require firebase/php-jwt
```

### 3. Configure Database

* Create a MySQL database called `issue`
* Import the schema and demo data:
* Run the db.sql file content on your DB query

```sql
-- Run this inside MySQL CLI or phpMyAdmin
SOURCE ./db.sql
```

Or copy SQL from the setup instructions provided earlier.

### 4. Update `./API/config.php`

Edit your DB credentials and set a strong `$jwt_secret` (32+ chars):

```php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "issue";
$jwt_secret = "";
```

---

## 🚀 API Testing
### 🔓 Public: Submit a Call
one-liner
```bash
curl -X POST http://localhost/users-issue/api/calls/create.php -H "Content-Type: application/json" -d '{"problem_type": "Login Problem", "user_name": "Jane Doe", "user_email": "jane@example.com", "user_phone": "123-456-7890", "description": "Unable to log in since yesterday."}'
```
---

### 🔐 Login to Get JWT
#### Manager:

```bash
curl -X POST http://localhost/users-issue/api/auth/manager_login.php -H "Content-Type: application/json" -d '{"email":"alice.johnson@example.com","password":"managerPass123"}'
```

#### Engineer:

```bash
curl -X POST http://localhost/users-issue/api/auth/engineer_login.php -H "Content-Type: application/json" -d '{"email":"bob.smith@example.com","password":"password123"}'
```

You’ll get a token like:

```json
{ "jwt": "eyJhbGciOi..." }
```

Use it in `Authorization: Bearer` header for protected requests.

---

## 🔐 Protected API Examples
* Remember to change the JWT code of the following or any JWT mention here to the freashly generated JWT of your own, this step is vital, else it will throw authentication error.

### 🔸 Manager: View Pending Calls

```bash
curl -X GET "http://localhost/users-issue/api/manager/calls_list.php?status=PENDING" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" -H "Content-Type: application/json"
```

### 🔸 Engineer: Resolve a Call

```bash
curl -X POST http://localhost/users-issue/api/engineer/resolve_call.php -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNjY3MjAsImV4cCI6MTc2ODA3MDMyMCwiZGF0YSI6eyJ1c2VyX2lkIjoiMiIsInJvbGUiOiJTVVBQT1JUX0VOR0lORUVSIn19.RSiLtL3p_WOOGO8vsqiGtjfvjWpc_Lf4fEp4GCx6jzE" -H "Content-Type: application/json" -d '{"call_id": 4,  "resolution_status": "RESOLVED", "issue_type": "Router Issue", "remarks": "Replaced router"}'
```

## Manager Endpoints (Protected)
```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" http://localhost/users-issue/api/manager/profile_get.php
```

```bash
curl -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" -d '{"name":"Jane Manager","email":"jane.manager@example.com","phone":"123-456-7890", "status": "ACTIVE"}' http://localhost/users-issue/api/manager/profile_put.php
```

```bash
curl -X GET "http://localhost/users-issue/api/manager/calls_list.php?status=PENDING" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" -H "Content-Type: application/json"
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" "http://localhost/users-issue/api/manager/calls_list.php?status=ONGOING"
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" "http://localhost/users-issue/api/manager/calls_list.php?status=CLOSED"
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" "http://localhost/users-issue/api/manager/call_details.php?id=4"
```

```bash
curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" -d '{"call_id":1,"engineer_id":2}' http://localhost/users-issue/api/manager/assign_engineer.php
```

```bash
curl -X POST -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" -d '{"call_id":4,"resolution":"Issue fixed","status":"CLOSED", "final_remark": "fixed by engineer id 2"}' http://localhost/users-issue/api/manager/close_call.php
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" http://localhost/users-issue/api/manager/support_engineers.php
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" "http://localhost/users-issue/api/reports/calls_by_interval.php?days=30"
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNzM2MzQsImV4cCI6MTc2ODA3NzIzNCwiZGF0YSI6eyJ1c2VyX2lkIjoiMSIsInJvbGUiOiJNQU5BR0VSIn19.sVM60CeNTvQKOtb88b0tZ9CrIqhidnduUxQCWcbqrYc" http://localhost/users-issue/api/reports/engineer_performance.php

```

## Engineer Endpoints (Protected)
```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNjY3MjAsImV4cCI6MTc2ODA3MDMyMCwiZGF0YSI6eyJ1c2VyX2lkIjoiMiIsInJvbGUiOiJTVVBQT1JUX0VOR0lORUVSIn19.RSiLtL3p_WOOGO8vsqiGtjfvjWpc_Lf4fEp4GCx6jzE" http://localhost/users-issue/api/engineer/profile_get.php
```

```bash
curl -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNjY3MjAsImV4cCI6MTc2ODA3MDMyMCwiZGF0YSI6eyJ1c2VyX2lkIjoiMiIsInJvbGUiOiJTVVBQT1JUX0VOR0lORUVSIn19.RSiLtL3p_WOOGO8vsqiGtjfvjWpc_Lf4fEp4GCx6jzE" -d '{"name":"Eve Engineer","email":"eve.engineer@example.com","phone":"987-654-3210", "status": "ACTIVE"}' http://localhost/users-issue/api/engineer/profile_put.php
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNjY3MjAsImV4cCI6MTc2ODA3MDMyMCwiZGF0YSI6eyJ1c2VyX2lkIjoiMiIsInJvbGUiOiJTVVBQT1JUX0VOR0lORUVSIn19.RSiLtL3p_WOOGO8vsqiGtjfvjWpc_Lf4fEp4GCx6jzE" "http://localhost/users-issue/api/engineer/calls_list.php?status=ONGOING"
```

```bash
curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNjY3MjAsImV4cCI6MTc2ODA3MDMyMCwiZGF0YSI6eyJ1c2VyX2lkIjoiMiIsInJvbGUiOiJTVVBQT1JUX0VOR0lORUVSIn19.RSiLtL3p_WOOGO8vsqiGtjfvjWpc_Lf4fEp4GCx6jzE" "http://localhost/users-issue/api/engineer/calls_list.php?status=CLOSED"
```

```bash
curl -X POST http://localhost/users-issue/api/engineer/resolve_call.php -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NjgwNjY3MjAsImV4cCI6MTc2ODA3MDMyMCwiZGF0YSI6eyJ1c2VyX2lkIjoiMiIsInJvbGUiOiJTVVBQT1JUX0VOR0lORUVSIn19.RSiLtL3p_WOOGO8vsqiGtjfvjWpc_Lf4fEp4GCx6jzE" -H "Content-Type: application/json" -d '{"call_id": 4,  "resolution_status": "RESOLVED", "issue_type": "Router Issue", "remarks": "Replaced router"}'
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
* Use your own strong secrets for JWT
* Each role has separate login + dashboard logic

---

## 🔧 To-Do (Future Improvements)

* Refresh tokens
* Email notifications to users

---

### 📫 Reach Me At:
- 📧 Email: koushikbardhan2000@gmail.com
- 💼 [LinkedIn](https://www.linkedin.com/in/koushik-bardhan-459895225/)
- 💼 [Vidwan](https://vidwan.inflibnet.ac.in/profile/563932)
- 🔬 [ORCID](https://orcid.org/0009-0002-8846-8347)
- 🐍 [GitHub](https://github.com/koushikbardhan2000)

---