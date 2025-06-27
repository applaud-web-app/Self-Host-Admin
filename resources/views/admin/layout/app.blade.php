<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="robots" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>Aplu Push</title>

    <meta name="description" content="Fastest Push Notification Service in India" />
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large" />
    <link rel="canonical" href="https://aplu.io/" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Push Notification Service" />
    <meta property="og:description" content="Fastest Push Notification Service in India" />
    <meta property="og:url" content="https://aplu.io/" />
    <meta property="og:site_name" content="APLU" />
    <meta property="og:updated_time" content="2024-07-08T12:59:03+05:30" />
    <meta property="og:image" content="{{ asset('images/aplu.png') }}" />
    <meta property="og:image:secure_url" content="{{ asset('images/aplu.png') }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="675" />
    <meta property="og:image:alt" content="Aplu Login Page" />
    <meta property="og:image:type" content="image/jpeg" />

    <!-- FAVICONS ICON -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_io/favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/favicon.ico') }}">
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/jqueryui/css/jquery-ui.min.css') }}">
    <link href="{{ asset('css/multiselect.css') }}" rel="stylesheet">
    <!-- Style css -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/iziToast.css') }}">

    @stack('styles')


</head>

<body>
    <div id="preloader">
        <div class="gooey">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <div id="main-wrapper">
        <div class="nav-header">
            <a href="#" class="brand-logo main-logo">
                <img src="{{ asset('images/logo-main.png') }}" alt="main-logo" class="img-fluid header-desklogo" />
                <img src="{{ asset('images/logo.png') }}" alt="main-logo" class="img-fluid header-moblogo" />
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="nav-item">
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button"
                                    data-bs-toggle="dropdown">
                                    @php
                                        $user = auth()->user();
                                        $profileImage = $user->avatar ? asset('storage/' . $user->avatar) : asset('images/user.png');
                                    @endphp
                                    <img src="{{ $profileImage }}" width="56" alt="" />
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{route('admin.profile.show')}}" class="dropdown-item ai-icon">
                                        <i class="far fa-user text-primary"></i>
                                        <span class="ms-1">Profile</span>
                                    </a>
                                    <a href="{{route('admin.logout')}}" class="dropdown-item ai-icon text-danger">
                                        <i class="far fa-sign-out text-danger"></i>
                                        <span class="ms-1">Logout</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">
                    <!-- Dashboard -->
                    <li>
                        <a class="ai-icon" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                            <i class="far fa-tachometer-alt-slowest"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    <!-- Profile -->
                    {{-- <li>
                        <a class="ai-icon" href="{{ route('admin.profile.show') }}" aria-expanded="false">
                            <i class="fal fa-user"></i>
                            <span class="nav-text">Profile</span>
                        </a>
                    </li> --}}

                    <!-- Users -->
                    <li>
                        <a class="ai-icon" href="{{ route('admin.users.show') }}" aria-expanded="false">
                            <i class="fal fa-users"></i>
                            <span class="nav-text">Users</span>
                        </a>
                    </li>

                    <!-- Payment -->
                    <li>
                        <a class="ai-icon" href="{{ route('admin.payment.show') }}" aria-expanded="false">
                            <i class="fal fa-credit-card"></i>
                            <span class="nav-text">Payment</span>
                        </a>
                    </li>

                    <li>
                        <a class="ai-icon" href="{{ route('admin.addons.show') }}" aria-expanded="false">
                            <i class="fal fa-plus"></i>
                            <span class="nav-text">Addons</span>
                        </a>
                    </li>

                    <li>
                        <a class="ai-icon" href="{{ route('admin.coupons.show') }}" aria-expanded="false">
                            <i class="fal fa-receipt"></i>
                            <span class="nav-text">Coupons</span>
                        </a>
                    </li>

                    <!-- Support -->
                    <li>
                        <a href="https://aplu.io/contact" class="ai-icon" aria-expanded="false">
                            <i class="fal fa-user-headset"></i>
                            <span class="nav-text">Support</span>
                        </a>
                    </li>
                    
                    <!-- Logout -->
                    <li>
                        <a class="ai-icon" href="{{ route('admin.logout') }}" aria-expanded="false">
                            <i class="fal fa-sign-out"></i>
                            <span class="nav-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        @yield('content')
        <div class="footer sticky-bottom">
            <div class="copyright">
                <p>Copyright © {{ date('Y') }} <a href="https://applaudwebmedia.com/" target="_blank">Applaud Web Media PVT. LTD.</a></p>
            </div>
        </div>
        <div class="whatsapp-icon">
            <a href="https://api.whatsapp.com/send/?phone=919997526894&text=Hi%2C+I+need+help+with+Aplu+Push.&type=phone_number&app_absent=0"
                target="_blank">
                <img src="{{ asset('images/whatsapp.gif') }}" alt="" class="img-fluid">
            </a>
        </div>
    </div>

    <!-- Required vendors -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('vendor/jqueryui/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
    <!-- Datatable -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins-init/datatables.init.js') }}"></script>
    <!-- multiselect -->
    <script src="{{ asset('js/multiselect.js') }}"></script>
    <!-- uipluploaded -->
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/deznav-init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="{{ asset('js/iziToast.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
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
    @stack('scripts')
</body>

</html>