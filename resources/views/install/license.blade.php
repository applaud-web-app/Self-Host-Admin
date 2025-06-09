@extends('layouts.single-master')
@section('title', 'License Verification | Aplu')

@section('content')
<style>
    .license-card {
        padding: 40px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .license-heading {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .license-subtitle {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
    }
</style>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="license-card card">
                    <h1 class="license-heading">🔐 License Verification</h1>
                    <p class="license-subtitle">
                        Enter your license details to verify and activate Aplu.
                    </p>

                    <form id="licenseForm" action="{{ route('install.database') }}" >
                        @csrf

                        <div class="form-group">
                            <label for="license_code" class="form-label">License Code <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="license_code" 
                                id="license_code" 
                                class="form-control" 
                                placeholder="Enter your license code" 
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter your license code.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="domain_name" class="form-label">Domain Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="domain_name" 
                                id="domain_name" 
                                class="form-control" 
                                value="{{ request()->getHost() }}" 
                                placeholder="e.g. yourdomain.com" 
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter your domain name.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="domain_ip" class="form-label">Domain IP <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="domain_ip" 
                                id="domain_ip" 
                                class="form-control" 
                                value="{{ request()->ip() }}" 
                                placeholder="e.g. 192.168.1.1" 
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter your domain IP.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="registered_email" class="form-label">Registered Email <span class="text-danger">*</span></label>
                            <input 
                                type="email" 
                                name="registered_email" 
                                id="registered_email" 
                                class="form-control" 
                                placeholder="Enter your registered email" 
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter your registered email.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="secret_key" class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                name="secret_key" 
                                id="secret_key" 
                                class="form-control" 
                                placeholder="Enter your secret key" 
                                required
                            >
                            <div class="invalid-feedback">
                                Please enter your secret key.
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            id="submitBtn" 
                            class="btn btn-primary text-white w-100"
                        >
                            Verify License
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Bootstrap form validation + button processing state
(function () {
    'use strict';
    const form = document.getElementById('licenseForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Verifying...
            `;
        }
        form.classList.add('was-validated');
    }, false);
})();
</script>
@endsection
