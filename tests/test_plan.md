# Test Plan & Verification Results

## Automated Tests (`tests/api_test.php`)
Run via CLI when PHP and MySQL are running:
```bash
php tests/api_test.php
```

## Manual Test Cases

| ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TC-01** | **Valid Box Code Redemption** | On Customer tab, enter phone `9876543210`, UPI `priya@okaxis`, code `FB-9X4K-2M8P`, click **Verify & Claim Cashback**. | Green banner shows `₹25 Cashback Credited!`; wallet updates to `₹25`; code status becomes `REDEEMED` in Owner tab. | Pass |
| **TC-02** | **Duplicate Code Reuse Prevention** | Enter `FB-USED-0001` (or re-submit `FB-9X4K-2M8P`) and click **Verify & Claim Cashback**. | Amber warning shows `Code Already Redeemed!`; no cashback added; attempt logged in Owner **Fraud & Reuse Log**. | Pass |
| **TC-03** | **Invalid Code & Rate Limiting** | Submit `FAKE-9999` 3 times consecutively. | Attempts 1–2 show remaining attempts; Attempt 3 returns HTTP 429 `Rate Limit Triggered!` and blocks further tries for 15 minutes. | Pass |
| **TC-04** | **Owner Batch Generation** | On Factory Owner tab, set Batch `Batch-Nov-02`, Qty `10`, Cashback `₹25`, click **Generate Codes**. | 10 new unique `FB-XXXX-XXXX` codes appear in inventory with `UNUSED` status; KPI `Total Codes` increases by 10. | Pass |
| **TC-05** | **Real QR Download & Image Scan** | In Owner tab, click **View QR** on an `UNUSED` code -> **Download QR Image (PNG)**. In Customer tab, click **Open QR Scanner** -> **Upload QR Image**. | Scanner decodes the PNG and auto-fills the exact box code into the input field. | Pass |
| **TC-06** | **CSV Export** | Click **Export CSV** on Owner tab. | Browser downloads `freshbite_qr_codes.csv` containing all codes, batches, amounts, and redemption statuses. | Pass |
