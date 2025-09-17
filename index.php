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
    <link rel="stylesheet" href="styles/base.css?v=1.0">
    <link rel="stylesheet" href="styles/landing.css?v=1.0">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header" data-header>
        <div class="header-inner container">
            <a href="#hero" class="brand">
                <span class="brand-text-logo">HealthPaws</span>
            </a>
            <nav class="nav" aria-label="Primary">
                <button class="nav-toggle" aria-expanded="false" aria-controls="nav-list">☰</button>
                <ul id="nav-list" class="nav-list">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#owners-trust">Pet Owners</a></li>
                    <li><a href="#pricing">Pricing</a></li>
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
            </div>
        </div>
    </header>

    <main id="main">
        <section id="hero" class="section hero">
            <div class="container grid-2">
                <div class="hero-copy">
                    <h1>Keeping Your Pet’s Care Connected, Anywhere</h1>
                    <p>All‑in‑one, cloud‑based software that keeps schedules tight, records organized, and clients delighted.</p>
                    <div class="actions">
                        <a href="#pricing" class="btn btn-primary">Get started today</a>
                        <a href="#features" class="btn btn-ghost">Explore features</a>
                    </div>
                    <ul class="trust-badges">
                        <li>Secure by design</li>
                        <li>Owner‑controlled sharing</li>
                        <li>Global access</li>
                    </ul>
                </div>
                <div class="hero-media">
                    <div class="hero-placeholder">
                        <div class="placeholder-content">
                            <h3>HealthPaws Dashboard</h3>
                            <p>Modern veterinary management system</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="stats" class="section stats">
            <div class="container stats-row">
                <div class="stat">
                    <div class="stat-value">10<span>+</span></div>
                    <div class="stat-label">Regions</div>
                </div>
                <div class="stat">
                    <div class="stat-value">250<span>+</span></div>
                    <div class="stat-label">Clinics</div>
                </div>
                <div class="stat">
                    <div class="stat-value">1,800<span>+</span></div>
                    <div class="stat-label">Professionals</div>
                </div>
                <div class="stat">
                    <div class="stat-value">120k<span>+</span></div>
                    <div class="stat-label">Pets</div>
                </div>
            </div>
        </section>

        <section id="spotlights" class="section spotlights">
            <div class="container">
                <div class="section-header">
                    <h2>Built for Everyone in Pet Care</h2>
                    <p class="section-subtitle">Whether you're a veterinary professional or a loving pet parent, HealthPaws adapts to your needs.</p>
                </div>
                <div class="spotlights-grid">
                    <article class="spotlight-card is-clinics">
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 5L8 21l4-7 4 7-4-16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="card-content">
                            <h3>For Veterinary Clinics</h3>
                            <p>Streamline your practice with appointments, EMR, billing, and client messaging in one powerful workspace. Save time and focus on what matters most - caring for pets.</p>
                            <ul class="feature-highlights">
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Smart scheduling & booking</span>
                                </li>
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Digital medical records</span>
                                </li>
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Integrated billing system</span>
                                </li>
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Client communication hub</span>
                                </li>
                            </ul>
                        </div>
                        <div class="actions">
                            <a href="#pricing" class="btn btn-primary">Start Free Trial</a>
                            <a href="#features" class="btn btn-ghost">View Features</a>
                        </div>
                    </article>
                    <article class="spotlight-card is-owners">
                        <div class="card-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1" stroke="currentColor" stroke-width="2"/>
                                <path d="M20.2 20.2 18 18M6 6l-2.2-2.2M18 6l2.2-2.2M6 18l-2.2 2.2" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <div class="card-content">
                            <h3>For Pet Parents</h3>
                            <p>Keep your furry family member's health data secure and accessible. Share information with any vet, anywhere, while maintaining complete control over your pet's privacy.</p>
                            <ul class="feature-highlights">
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Bank-level security</span>
                                </li>
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Global clinic access</span>
                                </li>
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Mobile-friendly design</span>
                                </li>
                                <li>
                                    <div class="feature-dot"></div>
                                    <span>Instant health updates</span>
                                </li>
                            </ul>
                        </div>
                        <div class="actions">
                            <a href="#owners-trust" class="btn btn-primary">Learn More</a>
                            <a href="#digital-pet-card" class="btn btn-ghost">See Digital Card</a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="digital-pet-card" class="section digital-pet-card">
            <div class="container grid-2">
                <div>
                    <h2>Your Pet's Digital Health Card</h2>
                    <p class="section-subtitle">Carry your pet's complete health history in your pocket. Our QR-coded digital pet card gives you instant access to medical records, vaccination history, and emergency contacts anywhere in the world.</p>
                    <ul class="list-check">
                        <li>Instant access to complete medical history</li>
                        <li>QR code for emergency situations</li>
                        <li>Works with any HealthPaws-registered clinic</li>
                        <li>Secure, encrypted data storage</li>
                        <li>Share selectively with veterinarians</li>
                    </ul>
                    <div class="actions">
                        <a href="#pricing" class="btn btn-primary">Get Your Digital Card</a>
                        <a href="#features" class="btn btn-ghost">Learn More</a>
                    </div>
                </div>
                <div class="digital-card-media">
                    <div class="pet-card-mockup">
                        <div class="pet-card-modern">
                            <div class="card-background">
                                <div class="card-pattern"></div>
                                <div class="card-glow"></div>
                            </div>
                            <div class="card-top">
                                <div class="card-brand">
                                    <div class="brand-icon-container">
                                        <!-- You can replace this with your SVG icon -->
                                        <svg class="brand-svg-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" fill="currentColor"/>
                                            <path d="M19 15L19.74 18.26L23 19L19.74 19.74L19 23L18.26 19.74L15 19L18.26 18.26L19 15Z" fill="currentColor"/>
                                            <path d="M5 15L5.74 18.26L9 19L5.74 19.74L5 23L4.26 19.74L1 19L4.26 18.26L5 15Z" fill="currentColor"/>
                                        </svg>
                                    </div>
                                    <span class="brand-text">HealthPaws</span>
                                </div>
                                <div class="qr-code-modern">
                                    <div class="qr-pattern"></div>
                                </div>
                            </div>
                            <div class="card-middle">
                                <div class="pet-photo">
                                    <div class="photo-container">
                                        <!-- You can replace this src with your pet photo -->
                                        <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=400&h=400&fit=crop&crop=face" alt="Pet Photo" class="pet-image">
                                    </div>
                                </div>
                                <div class="pet-details">
                                    <h3 class="pet-name">Bella Rodriguez</h3>
                                    <div class="pet-breed">Golden Retriever</div>
                                    <div class="pet-age">3 years old</div>
                                </div>
                            </div>
                            <div class="card-bottom">
                                <div class="health-indicators">
                                    <div class="indicator active">
                                        <div class="indicator-dot"></div>
                                        <span>Vaccinated</span>
                                    </div>
                                    <div class="indicator">
                                        <div class="indicator-dot"></div>
                                        <span>Checkup Due</span>
                                    </div>
                                    <div class="indicator active">
                                        <div class="indicator-dot"></div>
                                        <span>Insured</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="emergency-info">
                                        <div class="emergency-label">Emergency Contact</div>
                                        <div class="emergency-contact">Dr. Sarah Chen</div>
                                    </div>
                                    <div class="card-id">#HP-2024-001</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-reflection"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="owners-trust" class="section owners-trust">
            <div class="container grid-2">
                <div>
                    <h2>Pet parents, your companion's data is safe here</h2>
                    <ul class="list-check">
                        <li>You decide what to share and with whom</li>
                        <li>Bank‑grade security and encrypted storage</li>
                        <li>Access vaccines, prescriptions, and visit summaries anywhere</li>
                        <li>Works across clinics and on the go</li>
                    </ul>
                </div>
                <div class="owners-trust-media">
                    <div class="media-card small device-frame"></div>
                </div>
            </div>
        </section>

        <section id="features" class="section key-features">
            <div class="container">
                <h2>Everything You Need for Complete Pet Care</h2>
                <p class="section-subtitle">Discover the powerful features that make HealthPaws the trusted choice for pet owners and veterinarians worldwide.</p>
                <div class="features-grid">
                    <article class="feature-item featured">
                        <div class="feature-icon digital-card-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M7 8h4M7 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <rect x="15" y="8" width="4" height="4" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3>Digital Pet Card</h3>
                        <p>Carry your pet's health history with a QR-coded card, accessible anywhere.</p>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon vaccination-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 21l4-7 4 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Vaccination Tracking</h3>
                        <p>Never miss a shot with color-coded due date reminders.</p>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon profile-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 14v7" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3>Pet Profile Management</h3>
                        <p>Store and update your pet's details, from breed to medical history.</p>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon appointment-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 14h8M8 18h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3>Appointment Scheduling</h3>
                        <p>Book and manage vet visits with ease.</p>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon global-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <h3>Global Accessibility</h3>
                        <p>Access your pet's records across any HealthPaws-registered clinic.</p>
                    </article>
                    <article class="feature-item">
                        <div class="feature-icon security-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h3>Secure Access</h3>
                        <p>Role-based logins keep your data safe and private.</p>
                    </article>
                </div>
            </div>
        </section>





        <section id="testimonials" class="section testimonials">
            <div class="container">
                <div class="testimonials-header">
                    <h2>Trusted by Pet Parents Everywhere</h2>
                    <p class="section-subtitle">Real stories from families who trust HealthPaws with their beloved companions.</p>
                </div>
                <div class="testimonials-grid">
                    <article class="testimonial-card">
                        <div class="quote-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-10zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z" fill="currentColor"/>
                            </svg>
                        </div>
                        <blockquote>
                            "HealthPaws has been a game-changer for our family. Having all of Bella's medical records in one place gives us peace of mind, especially when traveling. The digital card saved us during an emergency visit!"
                        </blockquote>
                        <div class="testimonial-author">
                            <div class="author-avatar">MR</div>
                            <div class="author-info">
                                <div class="author-name">Maria Rodriguez</div>
                                <div class="author-details">Golden Retriever Owner • Los Angeles</div>
                            </div>
                            <div class="rating">
                                <span class="stars">★★★★★</span>
                            </div>
                        </div>
                    </article>
                    <article class="testimonial-card">
                        <div class="quote-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-10zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z" fill="currentColor"/>
                            </svg>
                        </div>
                        <blockquote>
                            "The convenience is incredible. No more worrying about lost vaccination records or trying to remember when Max's last checkup was. Everything is right there on my phone, and vets love how organized it is."
                        </blockquote>
                        <div class="testimonial-author">
                            <div class="author-avatar">JK</div>
                            <div class="author-info">
                                <div class="author-name">Jamal Kim</div>
                                <div class="author-details">German Shepherd Owner • Chicago</div>
                            </div>
                            <div class="rating">
                                <span class="stars">★★★★★</span>
                            </div>
                        </div>
                    </article>
                    <article class="testimonial-card">
                        <div class="quote-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-10zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h4v10h-10z" fill="currentColor"/>
                            </svg>
                        </div>
                        <blockquote>
                            "As someone who moves frequently for work, HealthPaws has been a lifesaver. Luna's health history follows us wherever we go, and new vets can access everything they need instantly."
                        </blockquote>
                        <div class="testimonial-author">
                            <div class="author-avatar">SC</div>
                            <div class="author-info">
                                <div class="author-name">Sarah Chen</div>
                                <div class="author-details">Cat Owner • New York</div>
                            </div>
                            <div class="rating">
                                <span class="stars">★★★★★</span>
                            </div>
                        </div>
                    </article>
                </div>
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
