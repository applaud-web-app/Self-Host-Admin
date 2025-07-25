<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <!-- Responsive viewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Allow indexing -->
 <meta name="robots" content="noindex, nofollow">


  <title>Aplu Self Host ― Web Push Notification Service</title>
  <meta name="description"
    content="Aplu Self Host is a versatile, open-source web push notification platform you can deploy on your own servers. Empower your team with unlimited, privacy-first push messaging and advanced analytics—no third-party vendor required.">
  <meta name="keywords" content="Aplu, self-host, web push notifications, open-source, privacy, analytics">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

  <!-- CSS Libraries -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS -->
  <link href="{{ asset('css/home.css') }}" rel="stylesheet">

  <style>
    #particles-js {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 0;
    }
    #hero .hero-content {
      position: relative;
      z-index: 1;
    }
  </style>

  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js" defer></script>
</head>

<body class="index-page">

  <!-- Header -->
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="/" class="logo d-flex align-items-center me-auto">
        <img src="https://push.aplu.io/images/logo-main.png" alt="Aplu Self Host Logo">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#services">Installation</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#faq">FAQ</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none fas fa-bars" role="button" aria-label="Toggle navigation"></i>
      </nav>

      <a class="btn-getstarted" href="{{ route('login') }}">Get Started</a>
    </div>
  </header>
  <!-- End Header -->

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section" style="position: relative; padding: 160px 0 0 0; overflow: hidden;">
      <div id="particles-js"></div>
      <div class="container text-center">
        <div class="row justify-content-center">
          <div class="col-xl-7 col-lg-9">
            <div class="hero-content">
              <div class="d-flex flex-column justify-content-center align-items-center">
                <h1 data-aos="fade-up"><span id="typed"></span></h1>
                <p data-aos="fade-up" data-aos-delay="100">
                  High-Performance, Self-Hosted Push Notifications allow you to run your own messaging infrastructure end-to-end, ensuring lightning-fast delivery, unlimited throughput, and full data ownership.
                </p>
                <div class="mb-3" data-aos="fade-up" data-aos-delay="200">
                  <a href="{{ route('login') }}" class="btn-get-started">Discover More</a>
                </div>
                <img
                  src="https://img.freepik.com/free-vector/design-stats-concept-illustration_114360-4496.jpg"
                  class="img-fluid hero-img"
                  alt="Aplu dashboard preview"
                  data-aos="zoom-out"
                  data-aos-delay="300"
                  loading="lazy"
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Hero Section -->

    <!-- Featured Services Section -->
    <section id="featured-services" class="featured-services py-5 section">
      <div class="container">
        <div class="row g-3">
          <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
            <div class="service-item h-100 d-flex">
              <div class="icon flex-shrink-0"><i class="fas fa-shield-alt"></i></div>
              <div>
                <h4 class="title"><a href="#about" class="stretched-link">Privacy-First</a></h4>
                <p class="description">Keep every subscriber record and message data entirely on your infrastructure. No third-party tracking.</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item h-100 d-flex">
              <div class="icon flex-shrink-0"><i class="fas fa-infinity"></i></div>
              <div>
                <h4 class="title"><a href="#features" class="stretched-link">Unlimited Scale</a></h4>
                <p class="description">Send limitless web push messages to unlimited subscribers—no usage caps or surprise fees.</p>
              </div>
            </div>
          </div>
          <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
            <div class="service-item h-100 d-flex">
              <div class="icon flex-shrink-0"><i class="fas fa-cogs"></i></div>
              <div>
                <h4 class="title"><a href="#services" class="stretched-link">Easy Deployment</a></h4>
                <p class="description">Get a full Aplu instance running in under 10 minutes with Docker Compose or Helm Chart.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Featured Services Section -->

    <!-- About Section -->
    <section id="about" class="about section">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <p class="who-we-are">About Aplu Self Host</p>
            <h3>A Push Platform You Own, Control, and Customize</h3>
            <p class="fst-italic">
              Aplu Self Host is an open-source push notification system designed to reside entirely within your own infrastructure.
              Enjoy full data ownership, advanced customization, and enterprise-grade reliability without vendor lock-in.
            </p>
            <ul>
              <li><i class="fas fa-check-circle"></i> <span>100% Open-Source (MIT License)</span></li>
              <li><i class="fas fa-check-circle"></i> <span>Lightning-Fast Setup in Minutes</span></li>
              <li><i class="fas fa-check-circle"></i> <span>Full REST API & SDKs for Every Stack</span></li>
            </ul>
            <a href="{{ route('login') }}" class="read-more"><span>Get Started Today</span><i class="fas fa-arrow-right ms-1"></i></a>
          </div>
          <div class="col-lg-6 about-images" data-aos="fade-up" data-aos-delay="200">
            <img
              src="https://img.freepik.com/free-vector/gradient-website-hosting-illustration_23-2149247164.jpg"
              class="img-fluid"
              alt="Server setup example"
              loading="lazy"
            >
          </div>
        </div>
      </div>
    </section>
    <!-- End About Section -->

    <!-- Features Section -->
    <section id="features" class="features section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Features</h2>
        <p>Everything You Need for In-House Web Push</p>
      </div>
      <div class="container">
        <div class="row justify-content-between">
          <div class="col-lg-5 d-flex align-items-center">
            <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">
              <li class="nav-item">
                <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                  <i class="fas fa-robot"></i>
                  <div>
                    <h4 class="d-none d-lg-block">Automated Workflows</h4>
                    <p>Schedule drip campaigns, triggers, and notifications based on user behavior.</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                  <i class="fas fa-chart-pie"></i>
                  <div>
                    <h4 class="d-none d-lg-block">Advanced Analytics</h4>
                    <p>Track delivery rates, engagement, and subscriber growth in real time.</p>
                  </div>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                  <i class="fas fa-user-cog"></i>
                  <div>
                    <h4 class="d-none d-lg-block">Granular Segmentation</h4>
                    <p>Create dynamic segments and target users with laser-focused content.</p>
                  </div>
                </a>
              </li>
            </ul>
          </div>
          <div class="col-lg-6">
            <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
              <div class="tab-pane fade active show" id="features-tab-1">
                <img
                  src="https://img.freepik.com/free-photo/3d-cartoon-scene-depicting-variety-people-multitasking_23-2151294580.jpg"
                  alt="Automated workflows illustration"
                  class="img-fluid"
                  loading="lazy"
                >
              </div>
              <div class="tab-pane fade" id="features-tab-2">
                <img
                  src="https://img.freepik.com/free-photo/blockchain-technology-cartoon-illustration_23-2151572152.jpg"
                  alt="Analytics dashboard example"
                  class="img-fluid"
                  loading="lazy"
                >
              </div>
              <div class="tab-pane fade" id="features-tab-3">
                <img
                  src="https://img.freepik.com/free-photo/digital-art-style-illustration-graphic-designer_23-2151536947.jpg"
                  alt="Segmentation interface"
                  class="img-fluid"
                  loading="lazy"
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Features Section -->

    <!-- Installation Section -->
 <!-- Installation Section -->
<section id="services" class="services section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Installation</h2>
    <p>Get Aplu up and running quickly with your preferred deployment method.</p>
  </div>
  <div class="container">
    <div class="row g-3">
      <!-- Installation without Docker -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <div class="service-item item-cyan position-relative">
          <i class="fas fa-terminal icon"></i>
          <div>
            <h3>Manual Installation</h3>
            <p>Clone the repository, configure your web server (NGINX/Apache), set up a PostgreSQL database, and start the service with systemd or a similar process manager.</p>
            <a href="https://github.com/aplu-self-host/aplu#manual-installation" class="read-more stretched-link" target="_blank">View Guide <i class="fas fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
      <!-- Kubernetes Installation -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
        <div class="service-item item-orange position-relative">
          <i class="fas fa-cloud-upload-alt icon"></i>
          <div>
            <h3>Kubernetes (Helm Chart)</h3>
            <p>Install Aplu on any Kubernetes cluster using our Helm chart. This method supports scaling, high availability, and efficient management of resources.</p>
            <a href="https://github.com/aplu-self-host/aplu-helm-chart" class="read-more stretched-link" target="_blank">Get the Chart <i class="fas fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
      <!-- Security Setup -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
        <div class="service-item item-teal position-relative">
          <i class="fas fa-shield-alt icon"></i>
          <div>
            <h3>Security Best Practices</h3>
            <p>Follow our security guide to configure SSL/TLS, firewalls, environment variables, and system updates to lock down your instance.</p>
            <a href="https://github.com/aplu-self-host/aplu#security" class="read-more stretched-link" target="_blank">Security Guide <i class="fas fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
      <!-- Cloud Deployment -->
      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
        <div class="service-item item-indigo position-relative">
          <i class="fas fa-cloud icon"></i>
          <div>
            <h3>Cloud Deployment</h3>
            <p>Deploy Aplu on your preferred cloud platform (AWS, GCP, Azure) for high availability and scalability. Follow our cloud deployment guide for specific steps on setting up your environment.</p>
            <a href="https://github.com/aplu-self-host/aplu#cloud-deployment" class="read-more stretched-link" target="_blank">View Cloud Guide <i class="fas fa-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

    <!-- End Installation Section -->

    <!-- Pricing Section -->
    <section id="pricing" class="pricing section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Pricing</h2>
        <p>Select Your Ideal Plan and Scale Your Notifications</p>
      </div>
      <div class="container">
        <div class="row gy-4 justify-content-center">
          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="pricing-item">
              <h3>Self-Host Standard</h3>
              <p class="description">One-time license. Deploy Aplu on your servers and own your push infrastructure.</p>
              <h4><sup>₹</sup>80,000<span> / one-time</span></h4>
              <a href="mailto:sales@aplu-self-host.com" class="cta-btn">Get Started</a>
              <ul>
                <li><i class="fas fa-check"></i> Unlimited Subscribers</li>
                <li><i class="fas fa-check"></i> Core Push Engine & API</li>
                <li><i class="fas fa-check"></i> Docker & Helm Support</li>
                <li><i class="fas fa-check"></i> Community-Driven Updates</li>
                <li class="na"><i class="fas fa-times"></i> Official SLA</li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="pricing-item featured">
              <p class="popular">Modular Add-Ons</p>
              <h3>Customize Your Stack</h3>
              <p class="description">Pick and pay only for the modules you need.</p>
              <h4>Starting at<span> ₹10,000</span></h4>
              <a href="mailto:modules@aplu-self-host.com" class="cta-btn">Contact Sales</a>
              <ul>
                <li><i class="fas fa-check"></i> <strong>Analytics Module</strong>: delivery & engagement stats</li>
                <li><i class="fas fa-check"></i> <strong>Segmentation Module</strong>: dynamic audiences</li>
                <li><i class="fas fa-check"></i> <strong>Automation Module</strong>: drip & event triggers</li>
                <li><i class="fas fa-check"></i> <strong>Priority Support</strong>: dedicated email & chat</li>
                <li><i class="fas fa-check"></i> <strong>Security Audit</strong>: vuln scans & hardening</li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="pricing-item">
              <h3>Enterprise & Custom</h3>
              <p class="description">White-glove onboarding, SLAs, and custom integrations.</p>
              <h4>Contact<span> for Pricing</span></h4>
              <a href="mailto:enterprise@aplu-self-host.com" class="cta-btn">Get a Quote</a>
              <ul>
                <li><i class="fas fa-check"></i> All Core + Add-Ons</li>
                <li><i class="fas fa-check"></i> 24×7 Phone & Email Support</li>
                <li><i class="fas fa-check"></i> Dedicated Account Manager</li>
                <li><i class="fas fa-check"></i> On-Prem Consulting & Training</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Pricing Section -->

    <!-- FAQ Section -->
    <section id="faq" class="faq section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Frequently Asked Questions</h2>
      </div>
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">
            <div class="faq-container">
              <div class="faq-item faq-active">
                <h3>Which browsers are supported?</h3>
                <div class="faq-content">
                  <p>Supports all modern browsers with Push API & Service Workers (Chrome, Firefox, Edge, Safari ≥ 16, mobile).</p>
                </div>
                <i class="faq-toggle fas fa-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>How do I integrate Aplu?</h3>
                <div class="faq-content">
                  <p>Use our REST API or lightweight JS SDK in React, Vue, Angular, or plain JS.</p>
                </div>
                <i class="faq-toggle fas fa-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>Do I need an SSL certificate?</h3>
                <div class="faq-content">
                  <p>Yes—HTTPS is required. Use Let’s Encrypt in production, self-signed locally.</p>
                </div>
                <i class="faq-toggle fas fa-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>Is there a hosted version?</h3>
                <div class="faq-content">
                  <p>Not yet—self-host only. Sign up for updates on a managed cloud offering.</p>
                </div>
                <i class="faq-toggle fas fa-chevron-right"></i>
              </div>
              <div class="faq-item">
                <h3>Where’s the documentation?</h3>
                <div class="faq-content">
                  <p>See our full docs and API reference on GitHub: <a href="https://github.com/aplu-self-host/aplu" target="_blank" rel="noopener">github.com/aplu-self-host/aplu</a></p>
                </div>
                <i class="faq-toggle fas fa-chevron-right"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End FAQ Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>Trusted by Developers & CTOs Worldwide</p>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": { "delay": 5000 },
              "slidesPerView": "auto",
              "pagination": { "el": ".swiper-pagination", "type": "bullets", "clickable": true },
              "breakpoints": { "320": { "slidesPerView": 1, "spaceBetween": 40 }, "1200": { "slidesPerView": 3, "spaceBetween": 1 } }
            }
          </script>
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="stars mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p>"Aplu Self Host transformed how we reach our users. Deployment was a breeze, and analytics gave instant insights."</p>
                <div class="profile mt-auto">
                  <img src="https://img.freepik.com/free-photo/3d-cartoon-style-character_23-2151034122.jpg" class="testimonial-img rounded-circle" alt="Alex Johnson">
                  <h3>Alex Johnson</h3><h4 class="text-muted">CTO, NextGen Apps</h4>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="stars mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p>"Running our own push server with Aplu was a game-changer. No vendor lock-in and infinite scaling."</p>
                <div class="profile mt-auto">
                  <img src="https://img.freepik.com/free-photo/3d-cartoon-style-character_23-2151034122.jpg" class="testimonial-img rounded-circle" alt="Maria Chen">
                  <h3>Maria Chen</h3><h4 class="text-muted">Lead Developer, BlogSphere</h4>
                </div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="testimonial-item">
                <div class="stars mb-3"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                <p>"Segmentation and automation modules helped us personalize notifications. Engagement shot through the roof."</p>
                <div class="profile mt-auto">
                  <img src="https://img.freepik.com/free-photo/3d-cartoon-style-character_23-2151034122.jpg" class="testimonial-img rounded-circle" alt="Samuel Lee">
                  <h3>Samuel Lee</h3><h4 class="text-muted">Product Manager, EduConnect</h4>
                </div>
              </div>
            </div>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </section>
    <!-- End Testimonials Section -->

  </main>
  <!-- End #main -->

  <!-- Footer -->
  <footer id="footer" class="footer">
    <div class="container footer-top text-center">
      <a href="/" class="logo mb-3 d-inline-block">
        <img src="https://push.aplu.io/images/logo-main.png" alt="Aplu Self Host Logo">
      </a>
      <div class="social-links d-flex justify-content-center mb-3">
        <a href="https://github.com/aplu-self-host" aria-label="GitHub"><i class="fab fa-github"></i></a>
        <a href="#" aria-label="Twitter" class="ms-3"><i class="fab fa-twitter"></i></a>
        <a href="#" aria-label="LinkedIn" class="ms-3"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>
    <div class="container copyright text-center py-3">
      <p>© <strong class="sitename">Aplu Self Host</strong> — All Rights Reserved</p>
      <p class="credits">Designed by Aplu Community</p>
    </div>
  </footer>
  <!-- End Footer -->

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center" aria-label="Scroll to top">
    <i class="fas fa-arrow-up"></i>
  </a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js" defer></script>

  <!-- Typing Animation -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const text = "Aplu Self Host";
      let index = 0;
      const target = document.getElementById("typed");
      function typeCharacter() {
        if (index < text.length) {
          target.textContent += text.charAt(index);
          index++;
          setTimeout(typeCharacter, 100);
        }
      }
      typeCharacter();
    });
  </script>

  <!-- Particles.js Init -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      particlesJS("particles-js", {
        particles: {
          number: { value: 60, density: { enable: true, value_area: 800 } },
          color: { value: "#fd683e" },
          shape: { type: "circle", stroke: { width: 0, color: "#000" } },
          opacity: { value: 0.5 },
          size: { value: 3, random: true },
          line_linked: { enable: true, distance: 150, color: "#fd683e", opacity: 0.4, width: 1 },
          move: { enable: true, speed: 2 }
        },
        interactivity: {
          events: {
            onhover: { enable: true, mode: "grab" },
            onclick: { enable: true, mode: "push" },
            resize: true
          },
          modes: { grab: { distance: 140, line_linked: { opacity: 1 } }, push: { particles_nb: 4 } }
        },
        retina_detect: true
      });
    });
  </script>

  <script src="{{ asset('js/home.js') }}"></script>
</body>

</html>
```
