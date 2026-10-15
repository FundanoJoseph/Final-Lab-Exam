# Student Information System (SIS)

A complete **PHP + MySQL** Student Information System built for the Final Lab Exam.
The application follows an **MVC / OOP** architecture, uses **PDO prepared statements**
everywhere, hashes passwords with `password_hash()` (bcrypt), and is ready to deploy on
**InfinityFree** or any PHP hosting.

---

## Features

- **Secure Login and Logout**
  - Passwords hashed with `password_hash()` / verified with `password_verify()`
  - Session hardening (HttpOnly + SameSite cookies, session-id rotation, fixation prevention)
  - Login throttling (brute-force protection) + role-based access control (RBAC)
  - CSRF tokens on every state-changing form
- **Dashboard**
  - Displays the summary of lists (departments, courses, students, active students, academic records)
  - Recently added students
- **Manage Department**
  - Add New Department · List All Departments · View Department · Update Department · Delete Department
- **Course Management**
  - Add New Course · List All Courses · View Course Details · Update Course Details · Delete Course Details
- **Student Information and Records Management**
  - Add New Student · List All Students · View Student Details
  - List Student's Academic History
  - Update Student Details
  - Add New / Edit / Delete Student Academic records
  - Update Student's Status
  - Delete Student Information and Records (cascades the academic history)
- **Settings** – change account password

### Default Admin Access

| Username | Password  |
|----------|-----------|
| `admin`  | `admin123` |

> Change this password right after deploying (Settings → Change Password).

---

## Requirements

- PHP **7.4+** (PHP 8.x recommended) with PDO (`pdo_mysql`)
- MySQL / MariaDB (provided by InfinityFree)
- Apache with `mod_rewrite` (enabled on InfinityFree)

---

## Project Structure

```
Final-Lab-Exam/
├── index.php              # Front controller (all requests enter here)
├── .htaccess              # Clean URLs + protects sensitive files
├── config/
│   └── config.php         # Externalized app + database configuration
├── app/
│   ├── Core/              # Framework: Router, Database, Auth, CSRF, Logger, View...
│   ├── Models/            # Data access (prepared statements only)
│   ├── Controllers/       # Request handlers (thin controllers)
│   └── Views/             # Presentation templates (no business logic)
├── assets/                # CSS + JS (no external dependencies)
├── database/
│   ├── schema.sql         # MySQL schema + seed data  ← import this on InfinityFree
│   └── schema-sqlite.sql  # SQLite variant for local demos without MySQL
└── logs/
    └── app.log            # Structured application log (created automatically)
```

**Separation of concerns:** `index.php` routes each request to a controller; controllers
validate input and call models; models talk to the database through the central
`App\Core\Database` (PDO) class; views only render data. Nothing is mixed together.

---

## Setup (local development)

1. **Create the database and import the schema**

   ```bash
   mysql -u root -p -e "CREATE DATABASE sis CHARACTER SET utf8mb4;"
   mysql -u root -p sis < database/schema.sql
   ```

2. **Configure the connection** – edit `config/config.php`:

   ```php
   'mysql' => [
       'host'     => 'localhost',
       'database' => 'sis',
       'username' => 'root',
       'password' => 'your_password',
       ...
   ],
   ```

3. **Run the app**

   ```bash
   php -S localhost:8000 router.php
   ```

   Open <http://localhost:8000> and log in with `admin` / `admin123`.
   (With XAMPP/WAMP just place the project in `htdocs` — `.htaccess` handles the routing.)

### Quick demo without MySQL (SQLite)

```bash
sqlite3 database/demo.sqlite < database/schema-sqlite.sql
SIS_DB_DRIVER=sqlite php -S localhost:8000 router.php
```

---

## Deployment on InfinityFree

1. **Create the hosting account** on [infinityfree.com](https://infinityfree.com)
   (remember your domain, e.g. `yoursite.rf.gd`).
2. **Create a MySQL database** in the InfinityFree control panel
   (note the *DB host*, *DB name*, *DB username*, *DB password*).
3. **Import the schema** – open **phpMyAdmin** from the control panel, select your
   database, choose **Import**, upload `database/schema.sql`, and press **Go**.
4. **Upload the project** – via the File Manager (or FTP) upload the *contents* of this
   project into the `htdocs` folder (so `index.php` sits directly inside `htdocs`).
5. **Set the credentials** – edit `htdocs/config/config.php` and fill in the values from
   step 2:

   ```php
   'mysql' => [
       'host'     => 'sql###.infinityfree.com',   // DB host from the control panel
       'database' => 'if0_xxxxxxxx',              // DB name
       'username' => 'if0_xxxxxxxx',              // DB user
       'password' => 'your_db_password',
       'charset'  => 'utf8mb4',
   ],
   ```

6. **Visit your site**, log in as `admin` / `admin123`, then change the password.

> Any other PHP host works the same way: import `database/schema.sql`,
> upload the files, update `config/config.php`.

---

## Security Notes (Category 2)

| Measure | Implementation |
|---|---|
| SQL-injection prevention | 100% PDO **prepared statements** (`App\Core\Database`) |
| Password storage | `password_hash()` (bcrypt) + `password_verify()` |
| Session security | HttpOnly + SameSite cookies, `session_regenerate_id()` on login & periodically |
| CSRF protection | Per-session tokens (`App\Core\Csrf`) validated on every POST |
| RBAC | `role` column + `Auth::requireRole('admin')` on all management actions |
| Input validation | `App\Core\Validator` server-side rules + `htmlspecialchars` output escaping (`e()`) |
| Login throttling | Session-based attempt counter with lockout window |
| Error handling | User-friendly messages; details go to `logs/app.log` only |

---

## Observability (Category 5)

Key events (login success/failure, throttling, CRUD operations, CSRF failures,
unhandled exceptions) are written to `logs/app.log` in a structured line format:

```
[2026-10-15 12:00:00] [INFO] [auth.login_success] {"user":"admin","role":"admin"}
[2026-10-15 12:01:02] [INFO] [student.created] {"actor":"admin","id":7,"student_no":"22104..."}
```

---

## License

Educational project – Final Lab Exam.
