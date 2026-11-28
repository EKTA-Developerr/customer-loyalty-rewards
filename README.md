# Customer Loyalty Rewards System (QR Cashback)

A web application built with **PHP 8+ and MySQL** for a factory producing physical boxed products. Customers scan a unique QR code (or enter the secret code) found inside each box to claim instant cashback to their UPI ID, while the factory owner generates QR batches, exports printable codes, and monitors redemptions and fraud attempts.

## Tech Stack
- **Backend:** PHP 8+ (PDO with prepared statements & MySQL transactions)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, Tailwind CSS (CDN), Vanilla JavaScript (`fetch` API)
- **QR Tools:** `html5-qrcode` (camera + image upload scanner), `qrcodejs` (downloadable QR PNGs)

## Setup & Run Instructions (XAMPP / Local PHP)
1. **Start Apache & MySQL** in XAMPP Control Panel (or your local PHP/MySQL server).
2. **Import the Database:**
   - Open `http://localhost/phpmyadmin`
   - Create a database named `freshbite_loyalty` (or run `database.sql` directly via **Import**).
   - `database.sql` creates all 5 tables (`batches`, `customers`, `product_codes`, `fraud_logs`, `rate_limits`) and seeds sample codes, demo loyalty accounts, and fraud logs.
3. **Configure Environment:**
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update `DB_USER` and `DB_PASS` in `.env` (for default XAMPP: `DB_USER=root`, `DB_PASS=`).
4. **Place Project in Web Root:**
   - Copy the `customer-loyalty-rewards` folder into `C:\xampp\htdocs\customer-loyalty-rewards\` (or `/Applications/XAMPP/htdocs/customer-loyalty-rewards/`).
5. **Open in Browser:**
   - Visit [http://localhost/customer-loyalty-rewards/](http://localhost/customer-loyalty-rewards/)
   - Use **Customer** tab to scan/enter codes (`FB-9X4K-2M8P`, `FB-7L2Q-9R1A`, `FB-USED-0001`).
   - Use **Factory Owner** tab to generate new batches, download QR PNGs, export CSV, and inspect fraud logs.

## Running Tests
- **Automated CLI Test:**
  ```bash
  php tests/api_test.php
