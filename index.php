<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Loyalty Rewards — QR Cashback &amp; Owner Dashboard</title>
  <script src="https://cdn.tailwindcss.com/3.4.17"></script>
  <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #f8fafc; color: #0f172a; }
    #qr-reader video { border-radius: 0.75rem; object-fit: cover; }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <!-- Top Navigation -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
          <i data-lucide="qr-code" class="w-5 h-5"></i>
        </div>
        <div class="min-w-0">
          <div class="font-bold text-slate-900 text-sm sm:text-base truncate">Customer Loyalty Rewards</div>
          <div class="text-xs text-slate-500 hidden sm:block">PHP + MySQL QR Cashback System</div>
        </div>
      </div>

      <nav class="flex items-center bg-slate-100 p-1 rounded-xl shrink-0" aria-label="Portal Switcher">
        <button id="tab-customer" type="button"
          class="px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-white text-emerald-700 shadow-sm flex items-center gap-1.5">
          <i data-lucide="gift" class="w-4 h-4"></i>
          <span>Customer</span>
        </button>
        <button id="tab-owner" type="button"
          class="px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5">
          <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
          <span>Factory Owner</span>
        </button>
      </nav>
    </div>
  </header>

  <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">

    <!-- ==================== CUSTOMER VIEW ==================== -->
    <section id="view-customer" class="space-y-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-6 text-white">
            <div class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium mb-3">
              <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
              <span>100 Scans = 2x Platinum VIP Cashback</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Scan QR or Enter Box Code</h1>
            <p class="text-emerald-50 text-sm mt-1">Earn more as you buy more: Silver (10+), Gold (50+), and Platinum VIP (100+ scans).</p>
          </div>

          <div class="p-6 space-y-5">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 space-y-2">
              <div class="text-xs font-semibold text-slate-600 flex items-center justify-between flex-wrap gap-2">
                <span class="flex items-center gap-1.5"><i data-lucide="flask-conical" class="w-3.5 h-3.5 text-emerald-600"></i> Quick Test Codes:</span>
                <div class="flex flex-wrap gap-1.5">
                  <button id="normal-demo-btn" type="button" class="px-2.5 py-1 rounded-lg bg-slate-200 text-slate-800 text-[11px] font-semibold hover:bg-slate-300 transition">
                    New (0 Scans)
                  </button>
                  <button id="silver-demo-btn" type="button" class="px-2.5 py-1 rounded-lg bg-sky-100 text-sky-900 border border-sky-300 text-[11px] font-semibold hover:bg-sky-200 transition">
                    Silver Ready (9→10)
                  </button>
                  <button id="gold-demo-btn" type="button" class="px-2.5 py-1 rounded-lg bg-yellow-100 text-yellow-900 border border-yellow-300 text-[11px] font-semibold hover:bg-yellow-200 transition">
                    Gold Ready (49→50)
                  </button>
                  <button id="vip-demo-btn" type="button" class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-semibold hover:bg-amber-200 transition">
                    VIP Ready (99→100)
                  </button>
                </div>
              </div>
            </div>

            <form id="claim-form" class="space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="cust-phone" class="block text-xs font-semibold text-slate-700 mb-1.5">Mobile Number</label>
                  <input id="cust-phone" type="tel" required placeholder="e.g. 9876543210" value="9876543210"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                </div>
                <div>
                  <label for="cust-upi" class="block text-xs font-semibold text-slate-700 mb-1.5">UPI ID (for Cashback)</label>
                  <input id="cust-upi" type="text" required placeholder="e.g. priya@okaxis" value="priya@okaxis"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                </div>
              </div>

              <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-4 space-y-2">
                <label for="box-code" class="block text-xs font-bold text-slate-800">Enter Box Code OR Scan QR</label>
                <div class="flex flex-col sm:flex-row gap-2">
                  <input id="box-code" type="text" required placeholder="Type code e.g. FB-9X4K-2M8P"
                    class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-sm font-mono uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                  <button id="open-scanner-btn" type="button"
                    class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition flex items-center justify-center gap-2 shrink-0">
                    <i data-lucide="camera" class="w-4 h-4"></i>
                    <span>Open QR Scanner</span>
                  </button>
                </div>
                <p class="text-[11px] text-slate-500">Type the code inside the box, use your camera, or upload a QR image.</p>
              </div>

              <button id="claim-submit-btn" type="submit"
                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-sm transition flex items-center justify-center gap-2">
                <i data-lucide="indian-rupee" class="w-4 h-4"></i>
                <span>Verify &amp; Claim Cashback</span>
              </button>
            </form>

            <div id="claim-feedback" class="hidden rounded-xl p-4 text-sm"></div>
          </div>
        </div>

        <div class="lg:col-span-5 space-y-5">
          <!-- Wallet + Loyalty Tier Card -->
          <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Wallet: <span id="wallet-phone-label" class="text-white font-mono">9876543210</span></span>
              <span id="loyalty-tier-badge" class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-semibold">Bronze</span>
            </div>
            <div>
              <div class="text-3xl sm:text-4xl font-extrabold tracking-tight" id="wallet-balance">₹0</div>
              <p class="text-xs text-slate-400 mt-1" id="wallet-summary">0 scans completed • 1x Standard</p>
            </div>
            <div class="space-y-1.5 pt-2 border-t border-slate-800">
              <div class="flex justify-between text-[11px] text-slate-300">
                <span id="loyalty-progress-label">Progress to Platinum VIP (100 scans)</span>
                <span id="loyalty-scan-count">0 / 100</span>
              </div>
              <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                <div id="loyalty-progress-bar" class="bg-gradient-to-r from-emerald-400 to-amber-400 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
              </div>
              <div class="text-[11px] text-emerald-300" id="loyalty-next-hint">10 more scans to reach Silver (+₹10/box)</div>
            </div>
          </div>

          <!-- Product Showcase Card (3 Distinct Colored Boxes) -->
          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Eligible Product Boxes</span>
              <span class="text-[11px] text-slate-400">3 Pack Sizes</span>
            </div>
            <div class="grid grid-cols-3 gap-2.5 text-center">
              <div class="p-2 rounded-xl bg-orange-50/60 border border-orange-200">
                <img src="data:image/jpeg;base64,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" alt="Small Pack" class="w-16 h-16 mx-auto rounded-lg object-cover border border-orange-200" />
                <div class="text-[11px] font-bold text-slate-900 mt-1.5">Small Pack</div>
                <div class="text-[11px] font-semibold text-orange-700">₹10 Base</div>
              </div>
              <div class="p-2 rounded-xl bg-emerald-50/60 border border-emerald-200">
                <img src="data:image/jpeg;base64,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" alt="Regular Chocolate Box" class="w-16 h-16 mx-auto rounded-lg object-cover border border-emerald-200" />
                <div class="text-[11px] font-bold text-slate-900 mt-1.5">Regular Box</div>
                <div class="text-[11px] font-semibold text-emerald-700">₹25 Base</div>
              </div>
              <div class="p-2 rounded-xl bg-blue-50/60 border border-blue-200">
                <img src="data:image/jpeg;base64,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" alt="Family Gift Box" class="w-16 h-16 mx-auto rounded-lg object-cover border border-blue-200" />
                <div class="text-[11px] font-bold text-slate-900 mt-1.5">Family Box</div>
                <div class="text-[11px] font-semibold text-blue-700">₹50 Base</div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-emerald-600"></i>
                <span>Your Recent Claims</span>
              </h2>
              <span class="text-xs text-slate-500" id="claim-count-badge">0 items</span>
            </div>
            <div id="customer-history-list" class="space-y-2.5"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ==================== FACTORY OWNER VIEW ==================== -->
    <section id="view-owner" class="hidden space-y-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
          <div class="text-xs font-medium text-slate-500 flex items-center justify-between">
            <span>Total Codes</span>
            <i data-lucide="package" class="w-4 h-4 text-slate-400"></i>
          </div>
          <div class="text-2xl font-bold text-slate-900 mt-2" id="kpi-total">0</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
          <div class="text-xs font-medium text-slate-500 flex items-center justify-between">
            <span>Redeemed</span>
            <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
          </div>
          <div class="text-2xl font-bold text-emerald-600 mt-2" id="kpi-redeemed">0</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
          <div class="text-xs font-medium text-slate-500 flex items-center justify-between">
            <span>Cashback Paid</span>
            <i data-lucide="indian-rupee" class="w-4 h-4 text-blue-500"></i>
          </div>
          <div class="text-2xl font-bold text-slate-900 mt-2" id="kpi-paid">₹0</div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
          <div class="text-xs font-medium text-slate-500 flex items-center justify-between">
            <span>Blocked Fraud</span>
            <i data-lucide="shield-alert" class="w-4 h-4 text-rose-500"></i>
          </div>
          <div class="text-2xl font-bold text-rose-600 mt-2" id="kpi-fraud">0</div>
        </div>
      </div>

      <!-- Customer Purchase Analytics (Highest / Average / Lowest) -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-5 h-5 text-emerald-600"></i>
            <span>Customer Purchase Analytics (High / Average / Low)</span>
          </h2>
          <span class="text-xs text-slate-500" id="analytics-cust-count">0 active buyers</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
            <div class="text-xs font-bold uppercase tracking-wider text-emerald-700 flex items-center justify-between">
              <span>Highest Buyer</span>
              <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="text-lg font-extrabold text-slate-900 mt-1 font-mono" id="insight-high-phone">—</div>
            <div class="text-xs text-emerald-800 mt-0.5" id="insight-high-meta">No scans yet</div>
          </div>

          <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
            <div class="text-xs font-bold uppercase tracking-wider text-blue-700 flex items-center justify-between">
              <span>Average per Customer</span>
              <i data-lucide="activity" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div class="text-lg font-extrabold text-slate-900 mt-1" id="insight-avg-scans">0 scans</div>
            <div class="text-xs text-blue-800 mt-0.5" id="insight-avg-earned">Avg ₹0 cashback</div>
          </div>

          <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
            <div class="text-xs font-bold uppercase tracking-wider text-amber-800 flex items-center justify-between">
              <span>Lowest Buyer</span>
              <i data-lucide="trending-down" class="w-4 h-4 text-amber-600"></i>
            </div>
            <div class="text-lg font-extrabold text-slate-900 mt-1 font-mono" id="insight-low-phone">—</div>
            <div class="text-xs text-amber-900 mt-0.5" id="insight-low-meta">No scans yet</div>
          </div>
        </div>

        <div id="leaderboard-strip" class="flex flex-wrap gap-2 pt-1"></div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
          <h2 class="font-bold text-slate-900 text-base flex items-center gap-2 mb-4">
            <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-600"></i>
            <span>Create QR Codes for New Boxes</span>
          </h2>
          <form id="batch-form" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label for="batch-name" class="block text-xs font-semibold text-slate-700 mb-1.5">Product / Lot Name</label>
              <input id="batch-name" type="text" required value="Regular-Chocolate-Box" placeholder="e.g. Chocolate-Box or Diwali-Pack"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
              <label for="batch-qty" class="block text-xs font-semibold text-slate-700 mb-1.5">How Many Boxes</label>
              <select id="batch-qty" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                <option value="5">5 Boxes</option>
                <option value="10" selected>10 Boxes</option>
                <option value="25">25 Boxes</option>
              </select>
            </div>
            <div>
              <label for="batch-amount" class="block text-xs font-semibold text-slate-700 mb-1.5">Base Cashback / Box</label>
              <select id="batch-amount" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white">
                <option value="10">₹10 / Box</option>
                <option value="25" selected>₹25 / Box</option>
                <option value="50">₹50 / Box</option>
              </select>
            </div>
            <div class="sm:col-span-3 flex flex-wrap items-center gap-3 pt-1">
              <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition flex items-center gap-2">
                <i data-lucide="wand-2" class="w-4 h-4"></i>
                <span>Generate Codes</span>
              </button>
              <a href="api.php?action=export_csv"
                class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export CSV</span>
              </a>
              <button id="open-reset-modal-btn" type="button"
                class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-sm font-semibold transition flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                <span>Reset Demo Data</span>
              </button>
            </div>
          </form>
        </div>

        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
          <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
              <i data-lucide="shield-check" class="w-5 h-5 text-rose-600"></i>
              <span>Fraud &amp; Reuse Log</span>
            </h2>
            <span class="text-xs text-slate-500">MySQL live log</span>
          </div>
          <div id="fraud-log-list" class="space-y-2 max-h-44 overflow-y-auto pr-1"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <h2 class="font-bold text-slate-900 text-base">Box QR Codes Inventory</h2>
          <div class="flex flex-wrap items-center gap-2">
            <input id="owner-search" type="text" placeholder="Search code or batch..."
              class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            <select id="owner-filter" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs sm:text-sm bg-white">
              <option value="ALL">All Status</option>
              <option value="UNUSED">Unused</option>
              <option value="REDEEMED">Redeemed</option>
            </select>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-sm">
            <thead>
              <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                <th class="py-3 px-4 font-semibold">Code</th>
                <th class="py-3 px-4 font-semibold">Product / Lot</th>
                <th class="py-3 px-4 font-semibold">Cashback</th>
                <th class="py-3 px-4 font-semibold">Status</th>
                <th class="py-3 px-4 font-semibold">Redeemed By</th>
                <th class="py-3 px-4 font-semibold text-right">QR Preview</th>
              </tr>
            </thead>
            <tbody id="codes-table-body" class="divide-y divide-slate-100"></tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <!-- QR Scanner Modal -->
  <div id="scanner-modal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-5 space-y-4 shadow-xl">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-900 flex items-center gap-2">
          <i data-lucide="scan-line" class="w-4 h-4 text-emerald-600"></i>
          <span>QR Code Scanner</span>
        </h3>
        <button id="close-scanner-btn" type="button" class="text-slate-400 hover:text-slate-700">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <div id="qr-reader" class="w-full rounded-xl overflow-hidden bg-slate-900 min-h-[190px] flex items-center justify-center text-xs text-slate-300 text-center p-4">
        Starting camera...
      </div>
      <div id="scanner-status" class="text-xs text-slate-500 text-center"></div>
      <div class="border-t border-slate-200 pt-3 space-y-2">
        <label for="qr-file-input"
          class="w-full py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs sm:text-sm font-semibold transition flex items-center justify-center gap-2 cursor-pointer">
          <i data-lucide="image-up" class="w-4 h-4 text-emerald-600"></i>
          <span>Upload QR Image from Device</span>
        </label>
        <input id="qr-file-input" type="file" accept="image/*" class="hidden" />
      </div>
    </div>
  </div>

  <!-- QR Code Preview Modal -->
  <div id="qr-preview-modal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xs w-full p-6 text-center space-y-4 shadow-xl">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Scannable Box QR</span>
        <button id="close-qr-modal-btn" type="button" class="text-slate-400 hover:text-slate-700">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <div id="qr-code-render" class="mx-auto w-44 h-44 bg-white border border-slate-200 rounded-xl p-3 flex items-center justify-center"></div>
      <div>
        <div id="qr-modal-code" class="font-mono font-bold text-lg text-slate-900"></div>
        <div id="qr-modal-meta" class="text-xs text-emerald-700 font-medium mt-0.5"></div>
      </div>
      <button id="download-qr-btn" type="button"
        class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition flex items-center justify-center gap-2">
        <i data-lucide="download" class="w-4 h-4"></i>
        <span>Download QR Image (PNG)</span>
      </button>
    </div>
  </div>

  <!-- Level-Up Congratulations Modal -->
  <div id="levelup-modal" class="hidden fixed inset-0 z-50 bg-slate-900/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl border border-amber-200">
      <div class="mx-auto w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
        <i data-lucide="trophy" class="w-8 h-8"></i>
      </div>
      <div>
        <div class="text-xs font-bold uppercase tracking-wider text-amber-600">Level Unlocked!</div>
        <h3 id="levelup-title" class="text-xl font-extrabold text-slate-900 mt-1">Congratulations!</h3>
        <p id="levelup-msg" class="text-sm text-slate-600 mt-2"></p>
      </div>
      <button id="close-levelup-btn" type="button"
        class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
        Awesome! Continue
      </button>
    </div>
  </div>

  <!-- Reset Demo Data Confirmation Modal -->
  <div id="reset-modal" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 space-y-4 shadow-xl">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
          <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold text-slate-900 text-base">Reset All Demo Data?</h3>
          <p class="text-xs text-slate-500 mt-0.5">This restores sample codes, clears test claims, and resets Silver/Gold/VIP demo accounts.</p>
        </div>
      </div>
      <div class="flex gap-2 pt-1">
        <button id="cancel-reset-btn" type="button" class="flex-1 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
        <button id="confirm-reset-btn" type="button" class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition">Yes, Reset Data</button>
      </div>
    </div>
  </div>

  <div id="toast" class="hidden fixed bottom-5 right-5 z-50 bg-slate-900 text-white text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-lg"></div>

  <script>
    const BOX_IMAGES = {
      small: "data:image/jpeg;base64,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",
      regular: "data:image/jpeg;base64,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",
      family: "data:image/jpeg;base64,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"
    };
    function getBoxImg(baseAmt) {
      const a = Number(baseAmt);
      if (a >= 50) return BOX_IMAGES.family;
      if (a >= 25) return BOX_IMAGES.regular;
      return BOX_IMAGES.small;
    }
    const state = {
      codes: [], fraudLogs: [], customerClaims: [], walletBalance: 0,
      loyalty: { scans: 0, tier: 'Bronze', multiplier: '1x Standard', nextTier: 'Silver (+₹10)', scansToNext: 10, progress: 0 },
      customerInsights: null
    };
    const el = (id) => document.getElementById(id);
    let html5QrInstance = null;
    let currentModalCode = '';

    function showToast(msg) {
      const t = el('toast');
      t.textContent = msg;
      t.classList.remove('hidden');
      clearTimeout(window._toastTimer);
      window._toastTimer = setTimeout(() => t.classList.add('hidden'), 2800);
    }

    async function loadState() {
      const phone = encodeURIComponent(el('cust-phone').value.trim());
      const res = await fetch(`api.php?action=state&phone=${phone}`);
      const data = await res.json();
      if (!data.ok) {
        showToast(data.error || 'Could not load database state');
        return;
      }
      state.codes = data.codes || [];
      state.fraudLogs = data.fraudLogs || [];
      state.customerClaims = data.customerClaims || [];
      state.walletBalance = data.walletBalance || 0;
      if (data.loyalty) state.loyalty = data.loyalty;
      if (data.customerInsights) state.customerInsights = data.customerInsights;
      renderAll();
    }

    function switchTab(tab) {
      const isCust = tab === 'customer';
      el('view-customer').classList.toggle('hidden', !isCust);
      el('view-owner').classList.toggle('hidden', isCust);
      el('tab-customer').className = isCust
        ? 'px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-white text-emerald-700 shadow-sm flex items-center gap-1.5'
        : 'px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5';
      el('tab-owner').className = !isCust
        ? 'px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all bg-white text-emerald-700 shadow-sm flex items-center gap-1.5'
        : 'px-3 sm:px-4 py-1.5 rounded-lg text-xs sm:text-sm font-semibold transition-all text-slate-600 hover:text-slate-900 flex items-center gap-1.5';
      el('open-reset-modal-btn').addEventListener('click', () => el('reset-modal').classList.remove('hidden'));
    el('cancel-reset-btn').addEventListener('click', () => el('reset-modal').classList.add('hidden'));
    el('confirm-reset-btn').addEventListener('click', async () => {
      const res = await fetch('api.php?action=reset_demo', { method: 'POST' });
      const data = await res.json();
      el('reset-modal').classList.add('hidden');
      el('claim-feedback').classList.add('hidden');
      showToast(data.ok ? data.message : (data.error || 'Reset failed'));
      await loadState();
    });

    loadState();
    }

    function showFeedback(type, title, message) {
      const box = el('claim-feedback');
      const styles = {
        success: 'bg-emerald-50 border border-emerald-200 text-emerald-900',
        error: 'bg-rose-50 border border-rose-200 text-rose-900',
        warn: 'bg-amber-50 border border-amber-200 text-amber-900'
      };
      box.className = 'rounded-xl p-4 text-sm ' + (styles[type] || styles.error);
      box.innerHTML = `<div class="font-bold mb-0.5">${title}</div><div class="text-xs sm:text-sm opacity-90">${message}</div>`;
      box.classList.remove('hidden');
    }

    async function handleClaimSubmit(e) {
      e.preventDefault();
      const phone = el('cust-phone').value.trim();
      const upi = el('cust-upi').value.trim();
      const code = el('box-code').value.trim().toUpperCase();

      const res = await fetch('api.php?action=claim', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone, upi, code })
      });
      const data = await res.json();
      showFeedback(data.type || (data.ok ? 'success' : 'error'), data.title || 'Status', data.message || '');
      if (data.ok) {
        el('box-code').value = '';
        if (data.leveledUp) {
          el('levelup-title').textContent = `Congratulations! You reached ${data.leveledUp}!`;
          el('levelup-msg').textContent = data.levelUpMessage || '';
          el('levelup-modal').classList.remove('hidden');
          if (typeof confetti === 'function') {
            confetti({ particleCount: 90, spread: 70, origin: { y: 0.6 } });
          }
        }
      }
      await loadState();
    }

    async function handleBatchGenerate(e) {
      e.preventDefault();
      const batchName = el('batch-name').value.trim();
      const qty = parseInt(el('batch-qty').value, 10);
      const amount = parseInt(el('batch-amount').value, 10);

      const res = await fetch('api.php?action=generate_batch', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ batchName, qty, amount })
      });
      const data = await res.json();
      showToast(data.ok ? data.message : (data.error || 'Error'));
      await loadState();
    }

    function openQrPreview(code, amount, batch) {
      currentModalCode = code;
      el('qr-modal-code').textContent = code;
      el('qr-modal-meta').textContent = `${batch} • ₹${amount} Cashback`;
      const container = el('qr-code-render');
      container.innerHTML = '';
      new QRCode(container, { text: code, width: 148, height: 148, colorDark: '#0f172a', colorLight: '#ffffff' });
      el('qr-preview-modal').classList.remove('hidden');
    }

    function downloadModalQr() {
      const container = el('qr-code-render');
      const canvas = container.querySelector('canvas');
      const img = container.querySelector('img');
      const dataUrl = canvas ? canvas.toDataURL('image/png') : (img ? img.src : '');
      if (!dataUrl) return;
      const a = document.createElement('a');
      a.href = dataUrl;
      a.download = `${currentModalCode || 'box-qr'}.png`;
      a.click();
    }

    async function stopScannerCamera() {
      if (html5QrInstance) {
        try {
          if (html5QrInstance.isScanning) await html5QrInstance.stop();
          html5QrInstance.clear();
        } catch (err) {}
      }
    }

    async function openScannerModal() {
      el('scanner-modal').classList.remove('hidden');
      el('scanner-status').textContent = 'Requesting camera permission...';
      try {
        html5QrInstance = new Html5Qrcode('qr-reader');
        await html5QrInstance.start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: { width: 200, height: 200 } },
          (decodedText) => {
            el('box-code').value = decodedText.trim().toUpperCase();
            closeScannerModal();
            showToast(`Scanned QR: ${decodedText.trim().toUpperCase()}`);
          },
          () => {}
        );
        el('scanner-status').textContent = 'Point your camera at the QR code.';
      } catch (err) {
        el('qr-reader').innerHTML = '<div class="text-amber-300">Camera unavailable on HTTP or blocked. Use "Upload QR Image" below.</div>';
        el('scanner-status').textContent = '';
      }
    }

    async function closeScannerModal() {
      await stopScannerCamera();
      el('scanner-modal').classList.add('hidden');
    }

    async function handleQrFileUpload(e) {
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      await stopScannerCamera();
      try {
        const fileScanner = new Html5Qrcode('qr-reader');
        const decodedText = await fileScanner.scanFile(file, true);
        el('box-code').value = decodedText.trim().toUpperCase();
        await closeScannerModal();
        showToast(`QR Image Read: ${decodedText.trim().toUpperCase()}`);
      } catch (err) {
        el('scanner-status').textContent = 'Could not detect a valid QR code in that image.';
      } finally {
        e.target.value = '';
      }
    }

    function renderCustomerView() {
      const l = state.loyalty;
      el('wallet-phone-label').textContent = el('cust-phone').value.trim() || '—';
      el('wallet-balance').textContent = `₹${state.walletBalance}`;
      el('wallet-summary').textContent = `${l.scans} scans completed • ${l.multiplier}`;
      el('loyalty-tier-badge').textContent = l.tier;
      el('loyalty-scan-count').textContent = `${Math.min(l.scans, 100)} / 100`;
      el('loyalty-progress-bar').style.width = `${Math.min(l.progress, 100)}%`;
      el('loyalty-next-hint').textContent = l.nextTier
        ? `${l.scansToNext} more scan(s) to reach ${l.nextTier}`
        : 'Maximum Platinum VIP (2x Highest Cashback) Unlocked!';

      el('claim-count-badge').textContent = `${state.customerClaims.length} items`;

      const list = el('customer-history-list');
      if (state.customerClaims.length === 0) {
        list.innerHTML = `<div class="text-center py-6 text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">No new claims in this table yet for this number.</div>`;
        return;
      }
      list.innerHTML = state.customerClaims.map((item) => `
        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
          <div>
            <div class="font-mono font-bold text-xs text-slate-900">${item.code}</div>
            <div class="text-[11px] text-slate-500">${item.upi} • ${item.time}${Number(item.bonus) > 0 ? ` • +₹${item.bonus} VIP Bonus` : ''}</div>
          </div>
          <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-xs">+₹${item.amount}</span>
        </div>
      `).join('');
    }

    function renderOwnerDashboard() {
      const total = state.codes.length;
      const redeemed = state.codes.filter((c) => c.status === 'REDEEMED');
      const paid = redeemed.reduce((sum, c) => sum + Number(c.amount), 0);

      el('kpi-total').textContent = total;
      el('kpi-redeemed').textContent = redeemed.length;
      el('kpi-paid').textContent = `₹${paid}`;
      el('kpi-fraud').textContent = state.fraudLogs.length;

      const ins = state.customerInsights;
      if (ins) {
        el('analytics-cust-count').textContent = `${ins.totalCustomers} active buyer(s)`;
        if (ins.highest) {
          el('insight-high-phone').textContent = ins.highest.phone;
          el('insight-high-meta').textContent = `${ins.highest.total_scans} scans • ₹${ins.highest.total_earned} (${ins.highest.tier})`;
        } else {
          el('insight-high-phone').textContent = '—';
          el('insight-high-meta').textContent = 'No scans yet';
        }
        el('insight-avg-scans').textContent = `${ins.avgScans} scans`;
        el('insight-avg-earned').textContent = `Avg ₹${ins.avgEarned} cashback`;
        if (ins.lowest) {
          el('insight-low-phone').textContent = ins.lowest.phone;
          el('insight-low-meta').textContent = `${ins.lowest.total_scans} scans • ₹${ins.lowest.total_earned} (${ins.lowest.tier})`;
        } else {
          el('insight-low-phone').textContent = '—';
          el('insight-low-meta').textContent = 'No scans yet';
        }
        el('leaderboard-strip').innerHTML = (ins.leaderboard || []).map((b, i) => `
          <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-[11px] text-slate-700 font-medium">
            #${i + 1} <span class="font-mono font-bold">${b.phone}</span> • ${b.scans} scans (${b.tier})
          </span>
        `).join('');
      }

      el('fraud-log-list').innerHTML = state.fraudLogs.map((f) => `
        <div class="p-2.5 rounded-xl bg-rose-50/70 border border-rose-100 text-xs flex items-start justify-between gap-2">
          <div>
            <span class="font-mono font-bold text-rose-800">${f.code}</span>
            <span class="text-rose-700"> — ${f.reason}</span>
            <div class="text-[11px] text-rose-500 mt-0.5">Phone: ${f.phone || '-'}</div>
          </div>
          <span class="text-[11px] text-rose-400 shrink-0">${f.time}</span>
        </div>
      `).join('');

      const query = el('owner-search').value.trim().toLowerCase();
      const filter = el('owner-filter').value;
      const filtered = state.codes.filter((c) => {
        const matchesStatus = filter === 'ALL' || c.status === filter;
        const matchesText = c.code.toLowerCase().includes(query) || c.batch.toLowerCase().includes(query);
        return matchesStatus && matchesText;
      });

      el('codes-table-body').innerHTML = filtered.map((c) => {
        const badge = c.status === 'UNUSED'
          ? '<span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">Unused</span>'
          : '<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">Redeemed</span>';
        const by = c.redeemed_phone ? `${c.redeemed_phone} (${c.redeemed_upi})` : '—';
        return `
          <tr class="hover:bg-slate-50">
            <td class="py-3 px-4 font-mono font-semibold text-xs text-slate-900">${c.code}</td>
            <td class="py-3 px-4 text-xs text-slate-700">
              <div class="flex items-center gap-2.5">
                <img src="${getBoxImg(c.baseAmount || c.amount)}" alt="Product Box" class="w-7 h-7 rounded-lg object-cover border border-slate-200 shrink-0" />
                <span class="font-medium">${c.batch}</span>
              </div>
            </td>
            <td class="py-3 px-4 text-xs font-semibold text-slate-900">₹${c.amount}</td>
            <td class="py-3 px-4">${badge}</td>
            <td class="py-3 px-4 text-xs text-slate-500">${by}</td>
            <td class="py-3 px-4 text-right">
              <button type="button" data-qr-code="${c.code}" data-qr-amount="${c.amount}" data-qr-batch="${c.batch}"
                class="qr-view-btn px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-xs font-medium text-slate-700">
                View QR
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    function renderAll() {
      renderCustomerView();
      renderOwnerDashboard();
    }

    el('tab-customer').addEventListener('click', () => switchTab('customer'));
    el('tab-owner').addEventListener('click', () => switchTab('owner'));
    el('claim-form').addEventListener('submit', handleClaimSubmit);
    const DEFAULT_LOT_NAMES = { '10': 'Small-Snack-Box', '25': 'Regular-Chocolate-Box', '50': 'Family-Gift-Box' };
    el('batch-amount').addEventListener('change', (e) => {
      const nameInput = el('batch-name');
      const cur = nameInput.value.trim();
      if (!cur || Object.values(DEFAULT_LOT_NAMES).includes(cur)) {
        nameInput.value = DEFAULT_LOT_NAMES[e.target.value] || 'Regular-Chocolate-Box';
      }
    });
    el('batch-form').addEventListener('submit', handleBatchGenerate);
    let _phoneTimer = null;
    el('cust-phone').addEventListener('input', () => {
      clearTimeout(_phoneTimer);
      _phoneTimer = setTimeout(loadState, 300);
    });
    el('cust-phone').addEventListener('change', loadState);
    el('normal-demo-btn').addEventListener('click', () => {
      el('cust-phone').value = '9876543210';
      el('cust-upi').value = 'priya@okaxis';
      loadState();
      showToast('Switched to normal customer (9876543210)');
    });
    el('owner-search').addEventListener('input', renderOwnerDashboard);
    el('owner-filter').addEventListener('change', renderOwnerDashboard);

    async function activateDemoLevel(phone, upi, scans, label) {
      el('cust-phone').value = phone;
      el('cust-upi').value = upi;
      await fetch('api.php?action=set_demo_level', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ phone, scans })
      });
      await loadState();
      showToast(`${label} ready (${scans} scans)! Scan 1 valid code to level up.`);
    }

    el('silver-demo-btn').addEventListener('click', () => activateDemoLevel('9888888888', 'silver@okaxis', 9, 'Silver Demo'));
    el('gold-demo-btn').addEventListener('click', () => activateDemoLevel('9777777777', 'gold@okaxis', 49, 'Gold Demo'));
    el('vip-demo-btn').addEventListener('click', () => activateDemoLevel('9999999999', 'vip@okaxis', 99, 'Platinum VIP Demo'));

    el('codes-table-body').addEventListener('click', (e) => {
      const btn = e.target.closest('.qr-view-btn');
      if (btn) openQrPreview(btn.dataset.qrCode, btn.dataset.qrAmount, btn.dataset.qrBatch);
    });

    el('open-scanner-btn').addEventListener('click', openScannerModal);
    el('close-scanner-btn').addEventListener('click', closeScannerModal);
    el('qr-file-input').addEventListener('change', handleQrFileUpload);
    el('close-qr-modal-btn').addEventListener('click', () => el('qr-preview-modal').classList.add('hidden'));
    el('close-levelup-btn').addEventListener('click', () => el('levelup-modal').classList.add('hidden'));
    el('download-qr-btn').addEventListener('click', downloadModalQr);

    loadState();
    if (window.lucide) window.lucide.createIcons();
  </script>
</body>
</html>
