# Assumptions & Design Decisions

The assignment leaves several business and technical details open. Below is every assumption made for **Customer Loyalty Rewards** and the reasoning behind it.

1. **Reward Format — Tiered Instant UPI Cashback (Bronze → Silver → Gold → Platinum VIP)**
   - **Gap:** The prompt states "a reward system to increase customer loyalty" without specifying points, discounts, or cash.
   - **Choice:** Direct ₹ cashback credited to a customer's UPI ID, with **repeat-purchase loyalty tiers** tracked per mobile number:
     - **Bronze (1–9 scans):** 1x base box cashback (₹10 / ₹25 / ₹50)
     - **Silver (10–49 scans):** +₹10 extra bonus per scan
     - **Gold (50–99 scans):** 1.5x cashback per scan
     - **Platinum VIP (100+ scans):** **2x Highest Cashback** per scan
   - **Why:** Rewarding repeat buyers with higher cashback directly incentivizes long-term customer loyalty rather than one-off scans. A demo account (`9999999999`) is seeded at 99 scans in `database.sql` so evaluators can test the 100th-scan Platinum VIP unlock in one click.

2. **Proof of Purchase — Unique Secret Code + QR Printed Inside the Box**
   - **Gap:** How a customer proves they bought a product.
   - **Choice:** Each box contains an internal card/sticker with a unique 11-character alphanumeric code (`FB-XXXX-XXXX`) encoded as a QR code.
   - **Why:** Printing *inside* the sealed box prevents shoppers from scanning codes in the retail aisle without buying the product.

3. **Customer Identity — Mobile Number + UPI ID (Passwordless)**
   - **Gap:** Whether customers must register with email/password.
   - **Choice:** Customers enter their 10-digit mobile number and UPI ID directly on the claim form; scan counts are tracked per mobile number in the `customers` table.
   - **Why:** Lowering friction increases redemption rates; in production, a 4-digit SMS OTP would be added before payout.

4. **Fraud & Code Reuse Prevention**
   - **Single-use enforcement:** Every code has `status ENUM('UNUSED', 'REDEEMED')` and is redeemed inside a MySQL `InnoDB` transaction using `SELECT ... FOR UPDATE` so two simultaneous requests cannot claim the same code.
   - **Brute-force protection:** Codes use `random_int()` across a 32-character alphabet (32^8 = ~1.1 trillion combinations), and 3 consecutive invalid attempts lock that IP+phone pair for 15 minutes via `rate_limits`.

5. **Factory Owner Access — Dedicated Dashboard Tab (Demo Mode)**
   - **Gap:** Authentication mechanism for the factory owner.
   - **Choice:** Separated into a **Factory Owner** tab in the UI for frictionless live evaluation/demo. In production, this view would sit behind session-based admin login.

6. **Payout Execution — Simulated Settlement**
   - **Gap:** Integration with a live payment gateway (RazorpayX / Cashfree Payouts).
   - **Choice:** Successful verification records the payout in MySQL and updates the customer's verified wallet balance.
