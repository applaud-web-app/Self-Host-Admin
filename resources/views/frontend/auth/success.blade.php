@extends('frontend.auth.layout.app')

@section('title', 'Payment Successful | Aplu')

@section('content')
    <style>
        .success-container {
            max-width: 580px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(50, 205, 150, 0.15);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .success-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #38b2ac, #48bb78, #38b2ac);
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0fff4;
            border-radius: 50%;
            border: 4px solid #48bb78;
            color: #48bb78;
            font-size: 48px;
            animation: bounce 1s ease;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-20px);}
            60% {transform: translateY(-10px);}
        }
        
        .success-heading {
            font-size: 2rem;
            font-weight: 700;
            color: #2f855a;
            margin-bottom: 0.75rem;
        }
        
        .success-subtitle {
            font-size: 1.1rem;
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .user-name {
            color: #2f855a;
            font-weight: 600;
        }
        
        .payment-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #edf2f7;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 500;
            color: #4a5568;
        }
        
        .detail-value {
            font-weight: 600;
            color: #2d3748;
        }
        
        .amount-value {
            color: #2f855a;
            font-size: 1.1rem;
        }
        
        .btn-dashboard {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.85rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #38b2ac, #48bb78);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(56, 178, 172, 0.2);
        }
        
        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(56, 178, 172, 0.3);
        }
        
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #f0f;
            opacity: 0;
        }
        
        @media (max-width: 640px) {
            .success-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .success-heading {
                font-size: 1.6rem;
            }
        }
    </style>

    <section class="section-padding" style="padding-top: 3rem; padding-bottom: 3rem; background-color: #f7fafc;">
        <div class="container">
            <div class="success-container">
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                
                <h1 class="success-heading">Payment Successful!</h1>
                <p class="success-subtitle">
                    Thank you, <span class="user-name">{{ $user->name }}</span>!<br>
                    Your payment has been processed successfully and your account has been updated.
                </p>
                
                <div class="payment-details">
                    <div class="detail-item">
                        <span class="detail-label">Payment ID:</span>
                        <span class="detail-value">{{ $payment->id }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Order ID:</span>
                        <span class="detail-value">{{ $payment->order_id }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">{{ now()->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value amount-value">₹{{ number_format($payment->amount / 100, 2) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value" style="color: #38a169;">
                            {{ ucfirst($payment->status === "captured" ? 'Paid' : $payment->status) }}
                            <svg style="display: inline; margin-left: 4px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </span>
                    </div>
                </div>
                
                <p style="color: #718096; margin-bottom: 1.5rem;">
                    A receipt has been sent to your registered email address.
                </p>
                
                <a href="{{route('customer.dashboard')}}" class="btn-dashboard text-white">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </section>

    <script>
        // Simple confetti effect
        document.addEventListener('DOMContentLoaded', function() {
            const colors = ['#38b2ac', '#48bb78', '#4299e1', '#9f7aea', '#ed8936'];
            const container = document.querySelector('.success-container');
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = -10 + 'px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                container.appendChild(confetti);
                
                animateConfetti(confetti);
            }
            
            function animateConfetti(el) {
                const startX = parseFloat(el.style.left);
                const rotation = Math.random() * 360;
                const duration = 3 + Math.random() * 3;
                
                el.style.opacity = '1';
                el.style.transform = `rotate(${rotation}deg)`;
                
                const animation = el.animate([
                    { top: '-10px', opacity: 0 },
                    { top: '10%', opacity: 1 },
                    { top: '100%', opacity: 0 }
                ], {
                    duration: duration * 1000,
                    easing: 'cubic-bezier(0.1, 0.8, 0.9, 1)'
                });
                
                animation.onfinish = () => {
                    el.style.top = '-10px';
                    animateConfetti(el);
                };
            }
        });
    </script>
@endsection