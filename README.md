# ⚓ Sri Lanka Ports Authority (SLPA) - Port Entry Permit System

[![Laravel Version](https://img.shields.io/badge/Laravel-12.0-red.svg?logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Enhanced%20%2F%20Stable-brightgreen.svg)]()

A comprehensive, secure, and modern web-based entry permit management system developed for the **Sri Lanka Ports Authority (SLPA)**. This system streamlines and automates the process of issuing, tracking, and verifying entry permits for both personnel and vehicles accessing port facilities (such as the Galle Port).

---

## 📖 Table of Contents
1. [Key Features](#-key-features)
2. [Technology Stack](#%EF%B8%8F-technology-stack)
3. [System Roles & Access](#-system-roles--access)
4. [Installation & Setup](#-installation--setup)
5. [Usage & Seeded Accounts](#-usage--seeded-accounts)
6. [Galle Port Enhancements (Phase A)](#%EF%B8%8F-galle-port-enhancements-phase-a)
7. [Developers & Contributors](#-developers--contributors)

---

## 🌟 Key Features

### 🎫 Multi-Type Permit Lifecycle
* **Temporary (Daily) Permits:** Instant single-day access.
* **Monthly Permits:** Recurrent security clearance.
* **Vehicle Permits:** Specific vehicle authorization linked to transport rates.

### 💳 Financial & Invoicing
* Automatic fee calculations based on permit type and configuration.
* Professional PDF invoice generation.
* Detailed transaction records and financial reports.

### 🚫 Blacklist Management
* Global registry for blacklisted individuals or vehicles.
* Auto-validation blocks permit creation instantly if a user matches the blacklist database.
* Full change log tracking.

### 🛡️ Security Verification
* Live check dashboards for gate personnel.
* Concurrent operations protected with MySQL advisory locks (concurrency-safe ID generation).
* Automatic session timeout controls.

---

## 🛠️ Technology Stack

| Component | Technology Used |
| :--- | :--- |
| **Backend Framework** | Laravel 12.0 (PHP 8.2+) |
| **Database** | MySQL / MariaDB (Supports advisory locks) |
| **Frontend Utilities** | Tailwind CSS 3.x, Alpine.js 3.x, Vite 6.x |
| **PDF Generation** | DomPDF |
| **Excel Exports** | Maatwebsite Excel |
| **Authentication** | Laravel Breeze |

---

## 👥 System Roles & Access

The application operates on a strict Role-Based Access Control (RBAC) mechanism:

1. **Super Admin (`super-admin`):** Full control over system configuration, user accounts, and master databases.
2. **Admin (`admin`):** Oversees permit operations, payments, and blacklist lists.
3. **Clerk (`clerk`):** Issues permits, processes batch checkouts, and handles client billing.
4. **Staff (`staff`):** Views dashboard statistics, creates draft requests, and prints permits.

---

## 🚀 Installation & Setup

Follow these simple steps to configure and run the project locally.

### Prerequisites
Ensure you have the following installed:
* PHP `>= 8.2`
* Composer `>= 2.0`
* Node.js & NPM
* MySQL Server (e.g. XAMPP)

### Step-by-Step Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/jprsavindya1/Galle-Port-Entry-Permit-System.git
   cd port-entry-permit
   ```

2. **Install PHP and Node dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   Copy the template environment file:
   ```bash
   copy .env.example .env
   ```
   Open the `.env` file and set up your MySQL database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=port_entry_permit
   DB_USERNAME=root
   DB_PASSWORD=your_mysql_password
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seed Database:**
   Creates tables and populates master rates, designations, and administrative accounts.
   ```bash
   php artisan migrate --seed
   ```

6. **Build Frontend Assets:**
   ```bash
   npm run build
   ```

7. **Start Server:**
   ```bash
   php artisan serve
   ```
   Open [http://localhost:8000](http://localhost:8000) in your web browser.

---

## 🔑 Usage & Local Testing

For testing and local demonstration, you can run the database seeders. The default seeded development accounts (with their roles and permissions) are defined in [UserSeeder.php](file:///d:/New-GllePermitSystem/port-entry-permit/port-entry-permit/database/seeders/UserSeeder.php).

> [!WARNING]
> Do not use seeded development credentials in production. Ensure you configure unique, secure passwords for all administrative users immediately upon deployment.

---

## ⚓ Galle Port Enhancements (Phase A)

The codebase has been extended with Galle Port-specific implementations and analytical roadmaps (see [GALLE_PORT_REPORT.md](file:///d:/New-GllePermitSystem/port-entry-permit/port-entry-permit/docs/GALLE_PORT_REPORT.md) for details):
* **Phase A Database Extensions:** Support for applicant photos (`photo_path`), scanned credentials (`scanned_nic`), and Yacht crew entry details.
* **Modernized Gate UI:** Enhanced dashboards designed for quick validation and multilingual support.
* **Batch Printing:** Continuous dot-matrix print receipt adjustments.

---

## 👨‍💻 Developers & Contributors

This system is built and maintained by:
* **sahanSS98** (Original Implementation)
* **Rashini Savindya** (Galle Port UI Modernization, Database Schema Extensions, and Layout Optimization)

---
*Developed for the Sri Lanka Ports Authority (SLPA).*
