<?php
// HealthPaws - Pricing
$page_title = "Pricing — HealthPaws";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/base.css">
    <link rel="stylesheet" href="styles/landing.css">
    <style>
        .pricing-hero{text-align:center; padding:60px 0 40px}
        .pricing-hero h1{font-size:clamp(32px, 4vw, 48px); margin-bottom:16px}
        .pricing-hero p{font-size:18px; color:var(--ink-600); max-width:600px; margin:0 auto}
        .pricing-toggle{display:flex; align-items:center; justify-content:center; gap:16px; margin:20px 0 40px}
        .toggle-option{position:relative}
        .toggle-option input{position:absolute; opacity:0}
        .toggle-option label{display:block; padding:8px 16px; border-radius:999px; cursor:pointer; transition:all .2s}
        .toggle-option input:checked + label{background:var(--brand-500); color:#fff}
        .pricing-grid{display:grid; grid-template-columns:repeat(3, 1fr); gap:24px; margin:40px 0}
        .pricing-card{background:#fff; border:1px solid rgba(42,140,130,.14); border-radius:18px; padding:24px; text-align:center; position:relative}
        .pricing-card.featured{border-color:var(--brand-500); box-shadow:0 20px 40px rgba(42,140,130,.15)}
        .pricing-card .plan-name{font-size:20px; font-weight:700; margin-bottom:8px}
        .pricing-card .price{font-size:36px; font-weight:700; color:var(--brand-600); margin-bottom:16px}
        .pricing-card .price .period{font-size:16px; color:var(--ink-500); font-weight:400}
        .pricing-card .features{text-align:left; margin:20px 0}
        .pricing-card .features li{margin:8px 0; padding-left:20px; position:relative}
        .pricing-card .features li::before{content:"✓"; position:absolute; left:0; color:var(--brand-600); font-weight:700}
        .pricing-card .cta{margin-top:20px}
        .pricing-card .badge{position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:var(--brand-500); color:#fff; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:600}
        @media(max-width: 980px){.pricing-grid{grid-template-columns:1fr; max-width:400px; margin:40px auto}}
    </style>
</head>
<body>
    <header class="site-header" data-header>
        <div class="header-inner container">
            <a href="index.php" class="brand">
                <span class="brand-logo" aria-hidden="true">🐾</span>
                <span class="brand-name">HealthPaws</span>
            </a>
            <nav class="nav" aria-label="Primary">
                <button class="nav-toggle" aria-expanded="false" aria-controls="nav-list">☰</button>
                <ul id="nav-list" class="nav-list">
                    <li><a href="index.php#features">Features</a></li>
                    <li><a href="index.php#how">How it works</a></li>
                    <li><a href="pricing.php">Pricing</a></li>
                    <li><a href="index.php#faq">FAQ</a></li>
                </ul>
            </nav>
            <div class="header-ctas">
                <div class="dropdown" data-dropdown>
                    <button class="btn btn-ghost" data-dropdown-toggle aria-expanded="false">Login ▾</button>
                    <div class="dropdown-menu" role="menu" aria-hidden="true">
                        <a role="menuitem" href="login.php">Log in</a>
                        <a role="menuitem" href="register.php">Register</a>
                    </div>
                </div>
                <button class="btn btn-ghost" data-open-modal="demo">Book demo</button>
            </div>
        </div>
    </header>

    <main>
        <section class="pricing-hero">
            <div class="container">
                <h1>Simple, transparent pricing</h1>
                <p>Start free, scale as you grow. No hidden fees, no surprises.</p>
                
                <div class="pricing-toggle">
                    <div class="toggle-option">
                        <input type="radio" name="billing" id="monthly" checked>
                        <label for="monthly">Monthly</label>
                    </div>
                    <div class="toggle-option">
                        <input type="radio" name="billing" id="annual">
                        <label for="annual">Annual (Save 20%)</label>
                    </div>
                </div>
            </div>
        </section>

        <section class="pricing-section">
            <div class="container">
                <div class="pricing-grid">
                    <div class="pricing-card">
                        <div class="plan-name">Starter</div>
                        <div class="price">$49<span class="period">/month</span></div>
                        <p>Perfect for small clinics getting started</p>
                        <ul class="features">
                            <li>1 location</li>
                            <li>Up to 3 users</li>
                            <li>Appointments & EMR</li>
                            <li>Email reminders</li>
                            <li>Basic reporting</li>
                            <li>Email support</li>
                        </ul>
                        <div class="cta">
                            <a href="register.php" class="btn btn-ghost">Start free trial</a>
                        </div>
                    </div>

                    <div class="pricing-card featured">
                        <div class="badge">Most Popular</div>
                        <div class="plan-name">Pro</div>
                        <div class="price">$129<span class="period">/month</span></div>
                        <p>For growing clinics with multiple locations</p>
                        <ul class="features">
                            <li>Up to 3 locations</li>
                            <li>Up to 10 users</li>
                            <li>Everything in Starter</li>
                            <li>Billing & payments</li>
                            <li>SMS reminders</li>
                            <li>Advanced analytics</li>
                            <li>Priority support</li>
                        </ul>
                        <div class="cta">
                            <a href="register.php" class="btn btn-primary">Start free trial</a>
                        </div>
                    </div>

                    <div class="pricing-card">
                        <div class="plan-name">Enterprise</div>
                        <div class="price">Custom</div>
                        <p>For large clinics with complex needs</p>
                        <ul class="features">
                            <li>Unlimited locations</li>
                            <li>Unlimited users</li>
                            <li>Everything in Pro</li>
                            <li>SLA & SSO</li>
                            <li>Custom integrations</li>
                            <li>Dedicated success manager</li>
                            <li>Phone support</li>
                        </ul>
                        <div class="cta">
                            <a href="register.php" class="btn btn-ghost">Contact sales</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section cta">
            <div class="container">
                <h2>Ready to get started?</h2>
                <p>Join thousands of clinics using HealthPaws to manage their practice.</p>
                <a href="register.php" class="btn btn-primary">Start free trial</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <p>© <span data-year></span> HealthPaws Veterinary Clinic</p>
            <nav aria-label="Legal">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </nav>
        </div>
    </footer>

    <!-- Demo Modal -->
    <div class="modal" id="modal-demo" aria-hidden="true" role="dialog" aria-labelledby="demo-title">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-dialog" role="document">
            <button class="modal-close" aria-label="Close" data-close-modal>×</button>
            <div class="modal-header">
                <h3 id="demo-title">Book a 15‑minute demo</h3>
                <p>Tell us about your clinic and we'll reach out shortly.</p>
            </div>
            <form class="modal-form" novalidate>
                <div class="form-row">
                    <label>Clinic name
                        <input type="text" name="clinic" required>
                    </label>
                    <label>Contact name
                        <input type="text" name="name" required>
                    </label>
                </div>
                <div class="form-row">
                    <label>Email
                        <input type="email" name="email" required>
                    </label>
                    <label>Phone
                        <input type="tel" name="phone">
                    </label>
                </div>
                <label>Message
                    <textarea name="message" rows="3" placeholder="Your goals or questions"></textarea>
                </label>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Request demo</button>
                </div>
            </form>
        </div>
    </div>

    <script src="scripts/main.js"></script>
    <script>
        // Billing toggle functionality
        document.querySelectorAll('input[name="billing"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                const isAnnual = e.target.id === 'annual';
                const prices = document.querySelectorAll('.price');
                prices.forEach(price => {
                    const amount = price.textContent.match(/\$(\d+)/)?.[1];
                    if(amount && isAnnual) {
                        const annualPrice = Math.round(amount * 0.8);
                        price.innerHTML = `$${annualPrice}<span class="period">/month</span>`;
                    } else if(amount && !isAnnual) {
                        price.innerHTML = `$${amount}<span class="period">/month</span>`;
                    }
                });
            });
        });
    </script>
</body>
</html>
