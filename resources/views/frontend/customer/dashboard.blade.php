@extends('frontend.customer.layout.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <style>
        .license-setup-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding: 30px;
            margin-top: 20px;
        }
        .license-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .license-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        .license-header p {
            color: #718096;
            margin-bottom: 0;
        }
        .license-key-container {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            position: relative;
            margin-bottom: 30px;
        }
        .license-key {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            word-break: break-all;
            padding-right: 100px;
        }
        .copy-license-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            background: #edf2f7;
            border: none;
            color: #4a5568;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .copy-license-btn:hover {
            background: #e2e8f0;
        }
        .copy-license-btn.copied {
            background: #48bb78;
            color: white;
        }
        .setup-steps {
            margin-top: 30px;
        }
        .step-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
        }
        .step-number {
            background: #f93a0b;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-content h4 {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .step-content p {
            color: #4a5568;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .code-snippet {
            background: #2d3748;
            color: #f7fafc;
            padding: 12px 15px;
            border-radius: 4px;
            font-size: 14px;
            margin: 10px 0;
            overflow-x: auto;
        }
        .download-btn {
            background: #f93a0b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        .download-btn:hover {
            background: #3182ce;
            color: white;
        }
        .download-btn i {
            margin-right: 8px;
        }
        .support-section {
            background: #ebf8ff;
            border-left: 4px solid #f93a0b;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
        }
        .support-section h4 {
            font-size: 18px;
            font-weight: 600;
            color: #2b6cb0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .support-section h4 i {
            margin-right: 10px;
        }
        .support-section p {
            color: #4a5568;
            margin-bottom: 10px;
        }
        .support-link {
            color: #f93a0b;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        .support-link:hover {
            color: #3182ce;
            text-decoration: underline;
        }
        .support-link i {
            margin-left: 5px;
            transition: transform 0.2s;
        }
        .support-link:hover i {
            transform: translateX(3px);
        }
    </style>
@endpush

@section('content')
    <section class="content-body">
        <div class="container-fluid position-relative">
            <div class="d-flex flex-wrap align-items-center justify-content-between text-head">
                <h2 class="mb-3 me-auto">Self-Hosted Setup</h2>
            </div>

            <div class="license-setup-container">
                <div class="license-header">
                    <h2><i class="fas fa-key text-primary me-2"></i> Your Aplu License Key</h2>
                    <p>This key is required to activate your self-hosted installation</p>
                </div>

                <div class="license-key-container">
                    <button class="copy-license-btn" id="copyLicenseBtn">
                        <i class="far fa-copy me-1"></i> Copy Key
                    </button>
                    <div class="license-key" id="licenseKey">
                        {{$licenseString}}
                    </div>
                </div>

                <div class="setup-steps">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Download the Installation Package</h4>
                            <p>Get the latest version of Aplu for self-hosting from your customer dashboard.</p>
                            <button class="download-btn">
                                <i class="fas fa-download" data-uuid="{{$product->uuid}}"></i> Download {{$product->name}}
                            </button>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Upload to Your Server</h4>
                            <p>Upload the downloaded package to your server's web directory using FTP/SFTP or your hosting control panel.</p>
                            <p>Common directories include:</p>
                            <ul>
                                <li><code>public_html</code></li>
                                <li><code>www</code></li>
                                <li><code>htdocs</code></li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Run the Installation Wizard</h4>
                            <p>Navigate to your domain in a web browser to start the installation process:</p>
                            <div class="code-snippet">
                                https://yourdomain.com/install
                            </div>
                            <p>The wizard will guide you through database setup and initial configuration.</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h4>Enter Your License Key</h4>
                            <p>When prompted during installation, enter the license key shown above.</p>
                            <p>This verifies your subscription and activates all features.</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">5</div>
                        <div class="step-content">
                            <h4>Complete Setup</h4>
                            <p>Create your admin account and configure basic settings to finish the installation.</p>
                            <p>You'll be automatically redirected to your new Aplu dashboard when complete.</p>
                        </div>
                    </div>
                </div>

                <div class="support-section">
                    <h4><i class="fas fa-life-ring"></i> Need Help With Installation?</h4>
                    <p>Our technical support team is available to assist you with any issues during setup.</p>
                    <a href="#" class="support-link">
                        Contact Support <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyBtn = document.getElementById('copyLicenseBtn');
            const licenseKey = document.getElementById('licenseKey').innerText;
            
            copyBtn.addEventListener('click', function() {
                navigator.clipboard.writeText(licenseKey).then(function() {
                    copyBtn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
                    copyBtn.classList.add('copied');
                    
                    setTimeout(function() {
                        copyBtn.innerHTML = '<i class="far fa-copy me-1"></i> Copy Key';
                        copyBtn.classList.remove('copied');
                    }, 2000);
                }).catch(function(err) {
                    console.error('Failed to copy license key: ', err);
                });
            });
        });
    </script>
@endpush