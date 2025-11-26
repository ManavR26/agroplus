
# 🌱 AgroPlus - Agricultural Marketplace & Farmer Support System

AgroPlus is a comprehensive web-based platform designed to bridge the gap between farmers and consumers. It empowers farmers to sell their produce directly to customers, cutting out middlemen, while also providing valuable resources on sustainable farming, government schemes, and waste management.

## 🚀 Project Overview

* **Role:** Quality Assurance & System Logic Validation
* **Focus:** Security Patching, Database Optimization, and Workflow Logic.

This project was not just about building features but ensuring they work securely and logically. Significant effort was put into patching vulnerabilities (like payment bypass) and ensuring robust role-based access control.

---

## 🛡️ Key Security & Logic Enhancements

This project implements several critical security and logic fixes identified during the testing phase:

### 1. 🔒 Payment Security Patch (Server-Side Verification)
* **The Vulnerability:** The original payment flow relied on a client-side redirect after payment, allowing malicious users to potentially bypass payment and generate orders via tools like Postman.
* **The Fix:** Implemented **Server-Side Signature Verification** using **HMAC-SHA256**.
    * Created a dedicated verification handler (`verify_razorpay_payment.php`).
    * The system now hashes the `order_id` and `payment_id` with the secret key and compares it against the signature returned by Razorpay.
    * **Result:** Database order creation only triggers *after* the signature is mathematically verified, making payment spoofing impossible.

### 2. 🚦 Role-Based Redirection Logic
* **The Issue:** Users were often redirected to incorrect pages (e.g., Farmers to the "Home" page instead of their Dashboard) after login.
* **The Fix:** Refactored `login.php` to implement a strict `switch/case` logic based on `user_type`.
    * **Farmers** -> `categories.php` (Farmer Dashboard)
    * **Customers** -> `customer_dashboard.php`
    * **Admins** -> `admin_dashboard.php`

### 3. 🗄️ Database Integrity & Schema Optimization
* **The Improvement:** The original schema lacked granularity for product quantities.
* **The Fix:** Modified the `products` table schema to include a `unit` column (`VARCHAR`). This allows farmers to specify stock in **Kg, Tonnes, Dozen**, etc., preventing inventory mismatches and improving the user experience.

### 4. 🚫 Session Security (Back-Button Protection)
* **The Vulnerability:** Users could navigate back to protected pages after logging out using the browser's back button.
* **The Fix:** Implemented PHP **Cache-Control Headers** (`no-store, no-cache, must-revalidate`) across all dashboard pages. This forces the browser to re-verify the session with the server every time a page is loaded, effectively blocking unauthorized access after logout.

---

## 🌟 Features

### 👨‍🌾 For Farmers
* **Farmer Dashboard:** A centralized hub to manage agricultural activities.
* **Product Management:** Upload and manage produce listings with images, prices, and specific stock units.
* **Sales Analytics:** Track earnings, sold items, and total revenue via the "My Sales" module.
* **Resource Access:** View information on **Organic Farming Methods**, **Waste Management**, and **Government Schemes**.

### 🛒 For Customers
* **Marketplace:** Browse and search for fresh agricultural products.
* **Shopping Cart:** Add items to cart with dynamic quantity selection.
* **Secure Checkout:** Integrated **Razorpay** payment gateway.
* **Order History:** View past orders and generate/print professional **PDF-style invoices**.

### 🛡️ Admin Panel
* **User Management:** Manage Farmer and Customer accounts.
* **Content Management:** Add, edit, or delete Government Schemes, Organic Methods, and Waste Management tips.
* **System Oversight:** View all platform orders and products.

---

## 🛠️ Technologies Used

* **Backend:** PHP (Native)
* **Database:** MySQL
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
* **Payment Gateway:** Razorpay (API Integration)
* **Server:** Apache (XAMPP/WAMP/LAMP recommended)

---

## ⚙️ Installation & Setup

Follow these steps to set up the project locally.

### 1. Prerequisites
* A local server environment (XAMPP, WAMP, or InfinityFree for live hosting).
* PHP 7.4 or higher.
* MySQL Database.

### 2. Clone the Repository
```bash
git clone [https://github.com/yourusername/agroplus.git](https://github.com/yourusername/agroplus.git)
cd agroplus
````

### 3\. Database Setup

1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named `agroplus_db`.
3.  Import the `database.sql` file provided in the root directory.
      * *This creates all necessary tables including `users`, `products`, `orders`, and `team_members`.*

### 4\. Configuration

Configure your database connection and API keys.

**Database Connection (`includes/config.php`):**

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'agroplus_db');
```

**Razorpay Setup (`includes/razorpay_config.php`):**

1.  Sign up at [Razorpay](https://razorpay.com/) and get **Test Keys**.
2.  Update the config file:

<!-- end list -->

```php
define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_KEY_ID');
define('RAZORPAY_KEY_SECRET', 'YOUR_KEY_SECRET');
```

### 5\. File Permissions

Ensure the following folders are writable so images can be uploaded:

  * `assets/images/products/`
  * `uploads/`

-----

## 🖥️ Usage Credentials (For Testing)

**Admin Account:**

  * **Email:** `admin@admin.com`
  * **Password:** `admin123` *(Change this in `admin_login.php` for production\!)*

**Farmers/Customers:**

  * Register new accounts via the **Sign Up** page to test the specific workflows.

-----

## 📂 Project Structure

```text
agroplus/
├── assets/              # CSS, JS, and Images
├── includes/            # Config files (DB, Razorpay, Session)
├── uploads/             # Uploaded content (Schemes, Methods)
├── admin_*.php          # Admin panel pages
├── farmer_*.php         # Farmer dashboard pages
├── customer_*.php       # Customer dashboard pages
├── verify_*.php         # Security verification scripts
├── database.sql         # DB Import file
└── index.php            # Landing page
```

## 📄 License

This project is open-source and available under the [MIT License](https://www.google.com/search?q=LICENSE).

```
```
