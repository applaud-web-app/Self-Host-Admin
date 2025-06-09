@extends('layouts.single-master')
@section('title', 'Environment Check | Aplu')

@section('content')
<style>
    .env-card {
        padding: 40px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .env-heading {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2d3748;
    }

    .env-subtitle {
        font-size: 1.1rem;
        color: #4a5568;
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    .checklist-item {
        background-color: #e6fffa;
        border-left: 5px solid #38b2ac;
        padding: 10px 15px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .checklist-item i {
        color: #38b2ac;
        margin-right: 10px;
    }

    .checklist-item span {
        color: #4a5568;
    }

    .folder-requirements {
        margin-top: 2rem;
    }

    .folder-requirements h4 {
        margin-bottom: 1rem;
        text-align: left;
        color: #2d3748;
        font-weight: 600;
    }

    .btn-continue {
        margin-top: 2rem;
    }
</style>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="env-card card">
                    <h1 class="env-heading">🔍 Environment Check</h1>
                    <p class="env-subtitle">
                        Let's make sure your environment meets all requirements.
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Installation directory is valid.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Fileinfo PHP extension enabled.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>JSON PHP extension enabled.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Tokenizer PHP extension enabled.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Zip archive PHP extension enabled.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>CURL is installed.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>OpenSSL PHP extension enabled.</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Min PHP version 7.2.0 (Current Version 7.4.16).</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Ctype PHP extension enabled.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Mbstring PHP extension enabled.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>PDO is installed.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>allow_url_fopen is on.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Redis is enabled with 4GB max memory.</span></div>
                            <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Supervisor is running.</span></div>
                        </div>
                    </div>

                    <div class="folder-requirements">
                        <h4>Folder Requirements</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="checklist-item"><i class="fas fa-check-circle"></i><span>File .env is writable.</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="checklist-item"><i class="fas fa-check-circle"></i><span>Folder /storage/framework is writable.</span></div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('install.license') }}" id="continueBtn" class="btn btn-primary w-100 inline-block text-white btn-continue">
                        Continue Setup
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Button processing state and redirect
document.getElementById('continueBtn').addEventListener('click', function(event) {
    event.preventDefault();
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Processing...
    `;
    // Small delay to allow spinner to show
    setTimeout(() => {
        window.location.href = "{{ route('install.license') }}";
    }, 300);
});
</script>
@endsection
