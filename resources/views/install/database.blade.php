@extends('layouts.single-master')
@section('title', 'Database Configuration | Aplu')

@section('content')
<style>
    .database-card {
        padding: 40px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .database-heading {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .database-subtitle {
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
                <div class="database-card card">
                    <h1 class="database-heading">🗄️ Database Configuration</h1>
                    <p class="database-subtitle">
                        Enter your database details to establish a connection for Aplu.
                    </p>

                    <form id="dbForm" action="{{ route('install.cron') }}" >
                        @csrf

                        <div class="form-group">
                            <label for="db_host" class="form-label">Database Host <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="db_host" 
                                id="db_host" 
                                class="form-control" 
                                placeholder="e.g. 127.0.0.1" 
                                required
                            >
                            <div class="invalid-feedback">Please enter your database host.</div>
                        </div>

                        <div class="form-group">
                            <label for="db_port" class="form-label">Database Port <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="db_port" 
                                id="db_port" 
                                class="form-control" 
                                placeholder="e.g. 3306" 
                                required
                            >
                            <div class="invalid-feedback">Please enter your database port.</div>
                        </div>

                        <div class="form-group">
                            <label for="db_name" class="form-label">Database Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="db_name" 
                                id="db_name" 
                                class="form-control" 
                                placeholder="e.g. aplu_db" 
                                required
                            >
                            <div class="invalid-feedback">Please enter your database name.</div>
                        </div>

                        <div class="form-group">
                            <label for="db_username" class="form-label">Database Username <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                name="db_username" 
                                id="db_username" 
                                class="form-control" 
                                placeholder="e.g. root" 
                                required
                            >
                            <div class="invalid-feedback">Please enter your database username.</div>
                        </div>

                        <div class="form-group">
                            <label for="db_password" class="form-label">Database Password <span class="text-danger">*</span></label>
                            <input 
                                type="password" 
                                name="db_password" 
                                id="db_password" 
                                class="form-control" 
                                placeholder="Enter your database password" 
                                required
                            >
                            <div class="invalid-feedback">Please enter your database password.</div>
                        </div>

                        <button 
                            type="submit" 
                            id="submitBtn" 
                            class="btn btn-primary text-white w-100"
                        >
                            Setup Database
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Bootstrap form validation
(function () {
    'use strict'
    const forms = document.querySelectorAll('#dbForm')
    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            } else {
                const btn = document.getElementById('submitBtn')
                btn.disabled = true
                btn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Processing...
                `
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>
@endsection
