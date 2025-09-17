<?php
// HealthPaws - Homepage
$page_title = "HealthPaws Vet Clinic";
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
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header" data-header>
        <div class="header-inner container">
            <a href="#hero" class="brand">
                <span class="brand-logo" aria-hidden="true">🐾</span>
                <span class="brand-name">HealthPaws</span>
            </a>
            <nav class="nav" aria-label="Primary">
                <button class="nav-toggle" aria-expanded="false" aria-controls="nav-list">☰</button>
                <ul id="nav-list" class="nav-list">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how">How it works</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="#security">Security</a></li>
                    <li><a href="#faq">FAQ</a></li>
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

    <main id="main">
        <section id="hero" class="section hero">
            <div class="container grid-2">
                <div class="hero-copy">
                    <h1>All‑in‑one Practice Management for Modern Vet Clinics</h1>
                    <p>Streamline appointments, records, billing, and client communications—secure, fast, and easy to use.</p>
                    <div class="actions">
                        <a href="#pricing" class="btn btn-primary">Start free trial</a>
                        <button class="btn btn-ghost" data-open-modal="demo">Book demo</button>
                    </div>
                    <ul class="trust-badges">
                        <li>HIPAA-style safeguards</li>
                        <li>Role-based access</li>
                        <li>99.9% uptime</li>
                    </ul>
                </div>
                <div class="hero-media">
                    <div class="media-card device-frame">
                        <div class="device-status"></div>
                        <div class="device-screen">
                            <div class="ui-top"></div>
                            <div class="ui-grid"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="section services">
            <div class="container">
                <h2>Product Highlights</h2>
                <div class="cards">
                    <article class="card">
                        <h3>Appointments</h3>
                        <p>Smart scheduling, reminders, and waitlist to keep your day on track.</p>
                    </article>
                    <article class="card">
                        <h3>EMR & Records</h3>
                        <p>Fast SOAP notes, templates, and attachments—all searchable and secure.</p>
                    </article>
                    <article class="card">
                        <h3>Billing & Invoices</h3>
                        <p>Packages, discounts, and integrated payments with automatic receipts.</p>
                    </article>
                    <article class="card">
                        <h3>Client Portal</h3>
                        <p>Online booking, reminders, and two-way SMS to delight pet parents.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="how" class="section about">
            <div class="container grid-2">
                <div>
                    <h2>How it works</h2>
                    <p>Get started in minutes and run your clinic with confidence. No heavy training required.</p>
                    <ul class="list-check">
                        <li>Onboard: import data and configure roles</li>
                        <li>Operate: book, chart, and bill from one place</li>
                        <li>Grow: insights, reminders, and client portal</li>
                    </ul>
                </div>
                <div class="about-media">
                    <div class="media-card small device-frame"></div>
                </div>
            </div>
        </section>

        <section id="integrations" class="section integrations">
            <div class="container">
                <h2>Integrations</h2>
                <div class="logo-row">
                    <div class="logo-chip">Stripe</div>
                    <div class="logo-chip">Twilio</div>
                    <div class="logo-chip">SendGrid</div>
                    <div class="logo-chip">QuickBooks</div>
                    <div class="logo-chip">LabConnect</div>
                </div>
            </div>
        </section>  




        <section id="testimonials" class="section testimonials">
            <div class="container">
                <h2>What Pet Parents Say</h2>
                <div class="quotes">
                    <blockquote>
                        "They treated Bella like family. The vets explained everything clearly."
                        <footer>— Maria R.</footer>
                    </blockquote>
                    <blockquote>
                        "Fast diagnostics and kind staff. Our go-to clinic."
                        <footer>— Jamal K.</footer>
                    </blockquote>
                </div>
            </div>
        </section>

        <section id="security" class="section security">
            <div class="container">
                <h2>Security & Compliance</h2>
                <ul class="list-check">
                    <li>Data encryption in transit and at rest</li>
                    <li>Role-based access controls and audit logs</li>
                    <li>Daily backups and disaster recovery</li>
                </ul>
            </div>
        </section>

        <section id="pricing" class="section pricing">
            <div class="container">
                <h2>Simple, transparent pricing</h2>
                <div class="pricing-grid">
                    <div class="price-card">
                        <h3>Starter</h3>
                        <p class="price"><span>$49</span>/month</p>
                        <ul>
                            <li>1 location, 3 users</li>
                            <li>Appointments & EMR</li>
                            <li>Email reminders</li>
                        </ul>
                        <a href="register.php" class="btn btn-ghost">Start free</a>
                    </div>
                    <div class="price-card featured">
                        <h3>Pro</h3>
                        <p class="price"><span>$129</span>/month</p>
                        <ul>
                            <li>3 locations, 10 users</li>
                            <li>Billing & payments</li>
                            <li>SMS reminders</li>
                        </ul>
                        <a href="pricing.php" class="btn btn-primary">View pricing</a>
                    </div>
                    <div class="price-card">
                        <h3>Enterprise</h3>
                        <p class="price">Custom</p>
                        <ul>
                            <li>Unlimited users</li>
                            <li>SLA & SSO</li>
                            <li>Dedicated success</li>
                        </ul>
                        <a href="register.php" class="btn btn-ghost">Talk to sales</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="section faq">
            <div class="container">
                <h2>Frequently Asked Questions</h2>
                <details>
                    <summary>Is there a free trial?</summary>
                    <p>Yes, try all Pro features free for 14 days. No credit card required.</p>
                </details>
                <details>
                    <summary>Can I import my data?</summary>
                    <p>We provide guided imports from CSV and popular legacy systems.</p>
                </details>
            </div>
        </section>

        <section id="cta" class="section cta">
            <div class="container">
                <h2>Run your clinic on HealthPaws</h2>
                <p>Start your free trial today or schedule a 15‑minute demo.</p>
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
                    <a href="dashboard.php?subdomain=demo&clinic=Demo%20Veterinary%20Clinic" class="btn btn-ghost">View dashboard mock</a>
                </div>
            </form>
        </div>
    </div>

    <script src="scripts/main.js"></script>
</body>
</html>
