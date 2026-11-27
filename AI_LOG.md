# AI Collaboration Log (`AI_LOG.md`)

This log documents key prompts used to design and build **Customer Loyalty Rewards**, including **2 specific cases where the AI produced flawed or incomplete output**, how I caught each issue, and how I fixed it.

---

## Key Prompts Used

1. **Prompt 1 (Requirement Breakdown):** *"Explain the Customer Loyalty Rewards System assignment with a real-world factory example so I can design the customer and owner flows."*
   - **Outcome:** Defined the 3 core flows: box QR generation, customer cashback claim, and fraud monitoring.
2. **Prompt 2 (UI Prototype):** *"Build a responsive Customer & Factory Owner UI with Tailwind CSS where customers can type a box code or scan a QR code."*
   - **Outcome:** Created the initial two-tab interface with wallet summary and owner KPI cards.
3. **Prompt 3 (Real QR Scanner & Generator):** *"Replace the simulated scanner with a real camera/image QR scanner and real downloadable QR codes."*
   - **Outcome:** Integrated `html5-qrcode` (camera + image upload) and `qrcodejs` (downloadable PNGs).
4. **Prompt 4 (Database Schema):** *"Design a MySQL InnoDB schema for batches, unique product codes, fraud logs, and rate limiting."*
   - **Outcome:** Generated `database.sql` with foreign keys, unique constraints, and seed data.
5. **Prompt 5 (PHP API):** *"Write api.php using PDO to handle claim verification, batch generation, and CSV export."*
   - **Outcome:** Built the REST endpoints in `api.php` and connected `index.php` via `fetch()`.
6. **Prompt 6 (Tests & Documentation):** *"Write an automated PHP CLI test script and a manual test plan covering valid claims, duplicate reuse, and rate limiting."*
   - **Outcome:** Created `tests/api_test.php` and `tests/test_plan.md`.

---

## Cases Where the AI Was Wrong (Caught & Fixed)

### Case 1: Race Condition in Code Redemption (`check-then-update` bug)
- **What the AI generated initially:** In the first version of `api.php`, the AI wrote a plain `SELECT status FROM product_codes WHERE code = ?`, checked `if ($row['status'] === 'UNUSED')` in PHP, and then ran a separate `UPDATE product_codes SET status = 'REDEEMED'` query without a transaction or row lock.
- **How I noticed:** I reviewed the concurrency risk: if a user double-clicks "Claim" or sends two simultaneous `POST` requests (e.g., from two devices), both `SELECT` queries could read `UNUSED` before either `UPDATE` ran, crediting double cashback.
- **How I fixed it:** I instructed the AI to wrap the redemption in `$pdo->beginTransaction()`, use `SELECT ... FOR UPDATE` to lock the row during verification, and add `AND status = 'UNUSED'` to the `UPDATE` statement before `$pdo->commit()`.

### Case 2: Fake/Static QR Scanner & Weak Code Generation
- **What the AI generated initially:**
  1. In the first UI draft, the "Scan QR" button only opened an animated box with a "Capture Sample QR" button rather than a real QR reader, and the Owner modal drew a decorative SVG grid instead of a scannable QR code.
  2. For PHP code generation, an earlier snippet used `rand()` instead of a cryptographically secure generator.
- **How I noticed:** When I tried testing with my phone and laptop, there was no way to actually scan a QR code from the Owner screen or upload a QR image. Also, `rand()` is predictable for security tokens.
- **How I fixed it:**
  1. Replaced the fake scanner with `html5-qrcode` (supporting both live camera and QR image upload) and replaced the decorative SVG with `qrcodejs` + a PNG download button.
  2. Updated `api.php` to use `random_int()` (CSPRNG) for generating `FB-XXXX-XXXX` codes.
