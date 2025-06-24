<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex, nofollow">

    <title>Aplu Push – Forgot Password</title>

    <meta name="keywords" content="push notifications, Aplu Push, push.apu.io" />
    <meta name="description" content="Aplu Push Password Recovery" />
    <meta property="og:image" content="{{ asset('images/aplu.png') }}" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}" />

    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/iziToast.css') }}">

    <!-- FontAwesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .auth-illustration {
            max-width: 100%;
            height: auto;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        .footer-links a {
            color: var(--dark-gray);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .reset-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .reset-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .feature-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="h-100">
    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-8 col-sm-10">
                <div class="card reset-card p-3 p-md-5">
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo-full.png') }}" alt="Aplu Push" class="img-fluid" style="height: 72px;" />
                        </a>
                        <h2 class="mt-2 font-weight-bold">Forgot Password?</h2>
                        <p class="text-muted">Enter your email and we'll send you a link to reset your password</p>
                    </div>

                    {{-- Display Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('forget-password.post') }}" method="POST" id="forgotPasswordForm">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    class="form-control border-start-0"
                                    value="{{ old('email') }}"
                                    placeholder="Enter your registered email"
                                    required
                                />
                            </div>
                            <small class="text-muted">We'll send a password reset link to this email</small>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" id="submitButton" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>

                <div class="text-center mt-3">
                    <p class="text-muted mb-0 footer-links">
                        &copy; {{ now()->year }} Aplu Push.
                        <a href="https://aplu.io/terms-conditions/" target="_blank">Terms</a> |
                        <a href="https://aplu.io/privacy-policy/" target="_blank">Privacy</a>
                    </p>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6 d-none d-lg-block">
                <div class="p-5 text-center">
                    <img
                        src="{{asset('images/forgot-password.png')}}"
                        alt="Password recovery illustration"
                        class="auth-illustration"
                        style="max-height: 350px;"
                    />
                    <h3 class="mt-4">Secure Password Recovery</h3>
                    <p class="text-muted">Our secure process ensures only you can reset your password.</p>

                    <div class="row mt-5">
                        <div class="col-md-4 mb-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h5>Secure Process</h5>
                                <p class="text-muted small">Encrypted link sent to your registered email</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h5>Time Limited</h5>
                                <p class="text-muted small">Reset link expires after 24 hours</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-user-lock"></i>
                                </div>
                                <h5>Account Protection</h5>
                                <p class="text-muted small">Prevents unauthorized access</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- jQuery (needed for validation) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>

    {{-- jQuery Validate plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            crossorigin="anonymous"
            referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/iziToast.js') }}"></script>

    <script>
        $(document).ready(function() {
            // jQuery Validation Rules
            $('#forgotPasswordForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address.",
                        maxlength: "Email cannot exceed 255 characters."
                    }
                },
                errorElement: "div",
                errorClass: "invalid-feedback",
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                submitHandler: function(form) {
                    // Show spinner and disable the button
                    const btn = $('#submitButton');
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
                    btn.prop('disabled', true);

                    form.submit();
                }
            });
        });
    </script>
    
    {{-- MESSAGE ALERT --}}
    @if (Session::has('error'))
        <script>
            iziToast.error({
                title: 'Error',
                message: '{{ Session::get('error') }}',
                position: 'topRight'
            });
        </script>
    @endif
    @if (Session::has('success'))
        <script>
            iziToast.success({
                title: 'Success',
                message: '{{ Session::get('success') }}',
                position: 'topRight'
            });
        </script>
    @endif
    @if (Session::has('warning'))
        <script>
            iziToast.warning({
                title: 'Warning',
                message: '{{ Session::get('success') }}',
                position: 'topRight'
            });
        </script>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <script>
                iziToast.error({
                    title: 'Error',
                    message: '{{ $error }}',
                    position: 'topRight'
                });
            </script>
        @endforeach
    @endif
</body>
</html>