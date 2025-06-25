@extends('frontend.customer.layout.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <style>
        .product-details-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 20px;
        }
        .product-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .product-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        .product-header p {
            color: #718096;
            margin-bottom: 0;
        }
        .details-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .details-list li {
            margin-bottom: 10px;
            color: #4a5568;
        }
        .integration-section {
            background: #ebf8ff;
            border-left: 4px solid #f93a0b;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }
        .integration-section h4 {
            font-size: 18px;
            font-weight: 600;
            color: #2b6cb0;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .integration-section p {
            color: #4a5568;
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .contact-details {
            margin-top: 15px;
            list-style: none;
            padding: 0;
        }
        .contact-details li {
            margin-bottom: 8px;
            font-size: 14px;
            color: #2d3748;
        }
        .contact-details li i {
            margin-right: 8px;
            color: #f93a0b;
        }
        .contact-link {
            color: #f93a0b;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        .contact-link:hover {
            color: #3182ce;
            text-decoration: underline;
        }
        .contact-link i {
            margin-left: 5px;
            transition: transform 0.2s;
        }
        .contact-link:hover i {
            transform: translateX(3px);
        }
    </style>
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head">
                <h2 class="mb-3 me-auto">Product Details</h2>
            </div>

            <div class="product-details-container">
                <div class="product-header">
                    <h2><i class="fas fa-box text-primary me-2"></i> {{ $product->name }}</h2>
                    <p>{{ $product->description }}</p>
                </div>
                <div class="integration-section">
                    <h4><i class="fas fa-tools me-2"></i> Setup & Integration</h4>
                    <p>For complete installation and integration on your dedicated server, our expert team will handle the entire process end-to-end. Reach out using the details below or via our support portal.</p>
                    <ul class="contact-details">
                        <li><i class="fab fa-whatsapp"></i><b>WhatsApp</b>: <a href="https://wa.me/919876543210" class="contact-link">+91 98765 43210</a></li>
                        <li><i class="fas fa-phone"></i><b>Mobile</b>: <a href="tel:+919823456789" class="contact-link">+91 98234 56789</a></li>
                        <li><i class="fas fa-envelope"></i><b>Email</b>: <a href="mailto:support@aplu.com" class="contact-link">support@aplu.com</a></li>
                    </ul>
                    <a href="https://aplu.io/contact" class="contact-link mt-3">
                        Contact Our Team <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <p class="mb-3 mt-4 text-center"><span class="bg-primary rounded-circle p-2 text-white light">OR</span></p>
                <div class="integration-section">
                    <h4><i class="fas fa-tools me-2"></i> सेटअप और एकीकरण</h4>
                    <p>पूर्ण स्थापना और एकीकरण के लिए, हमारी विशेषज्ञ टीम आपके समर्पित सर्वर पर पूरी प्रक्रिया को आरंभ से अंत तक संभालेगी। नीचे दिए गए विवरणों के माध्यम से या हमारे सपोर्ट पोर्टल के जरिए संपर्क करें।</p>
                    <ul class="contact-details">
                        <li><i class="fab fa-whatsapp"></i><b>व्हाट्सएप</b>: <a href="https://wa.me/919876543210" class="contact-link">+91 98765 43210</a></li>
                        <li><i class="fas fa-phone"></i><b>मोबाइल</b>: <a href="tel:+919823456789" class="contact-link">+91 98234 56789</a></li>
                        <li><i class="fas fa-envelope"></i><b>ईमेल</b>: <a href="mailto:support@aplu.com" class="contact-link">support@aplu.com</a></li>
                    </ul>
                    <a href="https://aplu.io/contact" class="contact-link mt-3">
                        हमारी टीम से संपर्क करें <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection