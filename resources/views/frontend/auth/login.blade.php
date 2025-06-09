<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Aplu Push – Login</title>

    <meta name="keywords" content="push notifications, Aplu Push, push.apu.io" />
    <meta name="description" content="Aplu Push Login" />
    <meta property="og:image" content="{{ asset('images/aplu.png') }}" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}" />

    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/iziToast.css') }}">

    <!-- FontAwesome for eye/eye-slash -->
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

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--dark-gray);
        }

        .password-wrapper {
            position: relative;
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
    </style>
</head>

<body class="h-100">
    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-8 col-sm-10">
                <div class="card p-3 p-md-5">
                    <div class="text-center mb-4">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo-full.png') }}" alt="Aplu Push" class="img-fluid" style="height: 72px;" />
                        </a>
                        <h2 class="mt-3 font-weight-bold">Welcome Back</h2>
                        <p class="text-muted">Sign in to continue to your Aplu Push account</p>
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

                    <form action="{{ route('login.doLogin') }}" method="POST" id="loginForm">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="password-wrapper">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                    required
                                />
                                <span class="password-toggle" id="togglePassword">
                                    <i class="far fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    name="remember_me"
                                    id="remember_me"
                                    class="form-check-input"
                                    {{ old('remember_me') ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="remember_me">Remember me</label>
                            </div>
                            <a href="{{ route('forget-password') }}" class="text-primary small">Forgot password?</a>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" id="submitButton" class="btn btn-primary">
                                Sign In
                            </button>
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
                        src="https://aplu.io/assets/images/aplu-image-1.png"
                        alt="Login illustration"
                        class="auth-illustration"
                    />
                    <h3 class="mt-4">Powerful Push Notifications</h3>
                    <p class="text-muted">Engage your users with real-time notifications delivered through our reliable platform.</p>

                    <div class="mt-4">
                        <div class="d-flex justify-content-center">
                            <div class="mx-3 text-center">
                                <i class="fas fa-bolt text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5>Fast Delivery</h5>
                                <p class="text-muted small">Instant notifications to your users</p>
                            </div>
                            <div class="mx-3 text-center">
                                <i class="fas fa-chart-line text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5>Analytics</h5>
                                <p class="text-muted small">Track engagement and performance</p>
                            </div>
                            <div class="mx-3 text-center">
                                <i class="fas fa-shield-alt text-primary mb-2" style="font-size: 2rem;"></i>
                                <h5>Secure</h5>
                                <p class="text-muted small">Enterprise-grade security</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- jQuery (needed for validation and toggle) --}}
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
            // Toggle Password Visibility
            $('#togglePassword').on('click', function() {
                const pwdField = $('#password');
                const type = pwdField.attr('type') === 'password' ? 'text' : 'password';
                pwdField.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            // jQuery Validation Rules
            $('#loginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255
                    },
                    password: {
                        required: true,
                        minlength: 8
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address.",
                        maxlength: "Email cannot exceed 255 characters."
                    },
                    password: {
                        required: "Please enter your password.",
                        minlength: "Password must be at least 8 characters long."
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
                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.parent("label"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    // Show spinner and disable the button
                    const btn = $('#submitButton');
                    btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...');
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