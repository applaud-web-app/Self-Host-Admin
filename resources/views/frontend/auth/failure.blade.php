@extends('frontend.auth.layout.app')

@section('title', 'Payment Failed | Aplu')

@section('content')
    <style>
        .result-card {
            padding: 40px;
            background: #ffe6e6;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            margin: auto;
            text-align: center;
        }
        .result-heading {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #c53030;
        }
        .result-subtitle {
            font-size: 1.1rem;
            color: #9b2c2c;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .error-detail {
            font-size: 1rem;
            color: #742a2a;
            margin-bottom: 1rem;
        }
        .btn-retry {
            display: inline-block;
            background: #c53030;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s ease;
        }
        .btn-retry:hover {
            background: #a32828;
        }
    </style>

    <section class="section-padding" style="padding-top: 4rem; padding-bottom: 4rem;">
        <div class="container">
            <div class="result-card">
                <h1 class="result-heading">❌ Payment Failed</h1>
                <p class="result-subtitle">
                    Oops! Something went wrong while processing your payment.
                </p>

                @if(isset($error))
                    <p class="error-detail"><strong>Error:</strong> {{ $error }}</p>
                @endif

                <a href="{{ route('checkout') }}" class="btn-retry">
                    Try Again
                </a>
            </div>
        </div>
    </section>
@endsection