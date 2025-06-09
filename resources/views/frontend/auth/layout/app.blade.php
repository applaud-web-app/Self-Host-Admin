<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Aplu Push')</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images//favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images//favicon_io/favicon-16x16.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/iziToast.css') }}">
    
    <style>
        /* Layout Structure */
         html {
            height: 100%;
           
        }
         body {
            height: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
            background-image: url('https://img.freepik.com/premium-photo/free-seamless-pattern-abstract-texture-geometric-vector-illustration-design-wallpaper-background_1226483-21619.jpg?semt=ais_hybrid&w=740')
        }
        
        /* Header */
        .custom-header {
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 20px;
            background-color: #fff;
            border-bottom: 1px solid #e3e6ea;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            z-index: 1000;
        }

        .custom-header .logo img {
            height: 52px;
            transition: all 0.3s ease;
        }

        .custom-header .logo img:hover {
            transform: scale(1.05);
        }

        /* Main Content */
        #page-content {
            flex: 1 0 auto;
            width: 100%;
        }

        /* Footer */
        .custom-footer {
            flex-shrink: 0;
            text-align: center;
            padding: 15px 0;
            background-color: #fff;
            border-top: 1px solid #e3e6ea;
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .custom-header .logo img {
                height: 42px;
            }
            
         
        }

        @media (max-width: 576px) {
            .custom-header {
                padding: 8px 15px;
            }
            
            .custom-header .logo img {
                height: 36px;
            }
            
            .custom-footer {
                padding: 10px 0;
                font-size: 0.75rem;
            }
        }

        /* FOR PASSOWRD FEILD */
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
        
    </style>
    @stack('styles')
</head>

<body>
    <!-- Fixed Header -->
    <header class="custom-header">
        <div class="logo">
            <a href="{{url('/')}}"><img src="{{ asset('images/logo-main.png') }}" alt="Aplu Logo" class="img-fluid"></a>
        </div>
    </header>

    <!-- Main Content -->
    <main id="page-content">
        @yield('content')
    </main>

    <!-- Fixed Footer -->
    <footer class="custom-footer">
        &copy; {{date('Y')}} Aplu Push Notification Service. All Rights Reserved.
    </footer>

    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('js/iziToast.js') }}"></script>

    <script>
        ///////////////////////////////
        // 1) Eye-toggle for passwords
        ///////////////////////////////
        $('.password-toggle').on('click', function() {
            let targetId = $(this).data('target');
            let $input = $('#' + targetId);
            const newType = $input.attr('type') === 'password' ? 'text' : 'password';
            $input.attr('type', newType);
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });
    </script>
    @stack('scripts')
</body>

</html>