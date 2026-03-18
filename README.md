# ⚡ Steven Games

> A feature-rich online gaming platform with user authentication, a player dashboard, and a full admin panel.

---

## 📋 Project Information

| Field               | Details                     |
|---------------------|-----------------------------|
| **Project Title**   | Steven Games                |
| **Technologies**    | PHP, MySQL, HTML5, CSS3, JavaScript |
| **Database**        | MySQL (steven_games)        |
| **PHP Version**     | 7.4+                        |

---

## 📁 Project Structure

```
steven_games/
├── index.php              # Homepage (hero, games, leaderboard)
├── login.php              # User login page
├── register.php           # New user registration
├── logout.php             # Session logout
├── dashboard.php          # Authenticated user dashboard
├── database.sql           # Full database schema + seed data
│
├── admin/
│   ├── index.php          # Admin dashboard (stats overview)
│   ├── users.php          # User management (ban/promote/delete)
│   ├── games.php          # Games management
│   ├── reports.php        # Reports page
│   └── settings.php       # Site settings
│
├── includes/
│   ├── db.php             # Database connection (MySQLi)
│   └── auth.php           # Auth functions (login, register, guards)
│
├── css/
│   └── style.css          # Main stylesheet (dark cyberpunk theme)
│
└── js/
    └── main.js            # Particles, scroll reveal, animations
```

---

## 🚀 Technologies Used

- **PHP** – Server-side scripting, session management, authentication
- **MySQL** – Relational database (users, games, scores, activity log)
- **HTML5** – Semantic page structure
- **CSS3** – Custom animations, CSS variables, responsive grid layout
- **JavaScript** – Particle effects, scroll reveal, navbar interactions
- **Google Fonts** – Orbitron (display) + Rajdhani (body)

---

## ⚙️ Steps to Run the Project

### Prerequisites
- XAMPP / WAMP / LAMP installed and running
- PHP 7.4 or higher
- MySQL 5.7 or higher

### 1. Clone / Copy Project
```bash
# If using Git:
git clone https://github.com/ntale-steven/25bsit0257_stevengames.git

# Or copy the folder into your htdocs:
# Windows: C:\xampp\htdocs\steven_games\
# Linux:   /opt/lampp/htdocs/steven_games/
```

### 2. Start Services
- Open **XAMPP Control Panel**
- Start **Apache** and **MySQL**

### 3. Import the Database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **"New"** on the left sidebar
3. Create a database named: `steven_games`
4. Click on the database, then go to the **Import** tab
5. Click **"Choose File"** and select `database.sql`
6. Click **"Go"** to import

### 4. Configure Database Connection
Open `includes/db.php` and update if needed:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // your MySQL username
define('DB_PASS', '');          // your MySQL password
define('DB_NAME', 'steven_games');
```

### 5. Launch the Site
Open your browser and go to:
```
http://localhost/steven_games/
```

---

## 🔐 Default Login Credentials

| Role  | Username   | Password   |
|-------|------------|------------|
| Admin | `admin`    | `admin123` |
| User  | `StevenX_Pro` | `admin123` |
| User  | `NightHawk99` | `admin123` |

> ⚠️ **Important:** Change the admin password immediately after first login in a production environment.

---

## 🛡️ Admin Panel

Access the admin panel at: `http://localhost/steven_games/admin/`

**Admin features include:**
- Dashboard overview (total users, active, banned, admins)
- User management: ban/unban, promote to admin, delete accounts
- Search and filter users
- Game management
- Activity logs

---

## 🗄️ Database Import Instructions

1. Ensure MySQL is running via XAMPP
2. Navigate to `http://localhost/phpmyadmin`
3. Create a new database called `steven_games`
4. Select the database and click the **Import** tab
5. Upload the `database.sql` file from this project root
6. Click **Go** – all tables and sample data will be imported automatically

The SQL file creates:
- `users` table – accounts, roles, scores, status
- `games` table – game catalogue
- `scores` table – player score records
- `activity_log` table – admin action audit trail

---

## 🎨 Design Features

- **Dark cyberpunk theme** with neon cyan and pink accents
- Animated floating particle background
- Smooth scroll reveal animations
- Responsive layout for mobile and desktop
- Orbitron display font for authentic gaming aesthetic

---



---

## 📜 License

This project was created for academic purposes.

© 2024 Steven Games. All rights reserved.
"# 25bsit0257_stevengames" 
