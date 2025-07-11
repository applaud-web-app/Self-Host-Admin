<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} Purchase Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* General Styles */
        body { 
            margin: 0; 
            padding: 0; 
            background-color: #f4f6f8; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            color: #333333; 
            font-size: 14px;
        }
        .email-wrapper { 
            width: 100%; 
            background-color: #f4f6f8; 
            padding: 20px 0; 
        }
        .email-content { 
            max-width: 700px; 
            margin: 0 auto; 
            background-color: #ffffff; 
            border-radius: 8px; 
            border: 1px solid #ffc7b8; 
            overflow: hidden; 
        }
        .email-header { 
            background: #ffede8; 
            padding: 15px; 
            text-align: center; 
            border-bottom: 1px solid #ffc7b8; 
        }
        .email-header img { 
            max-width: 150px; 
            height: auto; 
        }
        .email-body { 
            padding: 15px; 
            border-bottom: 1px solid #ffc7b8; 
        }
        .email-body h1 { 
            color: #FD683E; 
            margin: 10px 0; 
            font-size: 20px;
            font-weight: 700;
            text-align: center;
        }
        .email-body h2 { 
            color: #000; 
            margin: 10px 0; 
            font-size: 20px;
            font-weight: 600;
        }
        .email-body p { 
            line-height: 1.6; 
            margin: 10px 0; 
            font-size: 14px; 
        }
        .purchase-details { 
            background-color: #f9f9f9; 
            border: 1px solid #e0e0e0; 
            border-radius: 8px; 
            padding: 15px; 
            margin: 20px 0;
        }
        .purchase-details strong { 
            color: #FD683E; 
            font-weight: 600;
        }
          .purchase-details h3 { 
               margin: 0 0 10px 0; 
            font-weight: 600;
        }
        .email-body ul li, .email-body ol li { 
            margin: 10px 0; 
            font-size: 14px; 
        }
        .email-body a { 
            color: #FD683E; 
            text-decoration: none; 
        }
        .cta-button { 
            display: inline-block; 
            padding: 10px 20px; 
            background-color: #FD683E; 
            color: #ffffff !important; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: 500; 
            font-size: 14px; 
            margin: 20px 0;
            transition: background-color 0.3s ease;
        }
        .cta-button:hover { 
            background-color: #C7522A; 
        }
        .email-footer { 
            background-color: #ffede8; 
            padding: 10px; 
            text-align: center; 
            font-size: 12px; 
            color: #000; 
            border-top: 1px solid #ffc7b8; 
        }
        .email-footer a { 
            color: #000; 
            text-decoration: underline; 
            margin: 0 5px; 
        }
        .section-divider {
            width: 100%;
            height: 2px;
            background-color: #fd683e;
            margin: 20px 0;
        }
        @media only screen and (max-width: 600px) { 
            .email-content { 
                width: 95% !important; 
                border-radius: 0; 
                border: none; 
            } 
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Header -->
            <div class="email-header">
                <img src="https://aplu.io/assets/images/logo.png" alt="Aplu Logo">
            </div>

            <!-- Body -->
            <div class="email-body">
                <h1>Thank You for Purchasing {{ $product->name }}!</h1>
                <p>Hello <strong>{{ $user->name }}</strong>,</p>
                <p>We are excited to confirm your purchase of <strong>{{ $product->name }}</strong>! Below are your order details:</p>

                <div class="purchase-details">
                    <h3>Your Order Information</h3>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Order ID:</strong> {{ $orderId }}</p>
                    <p><strong>Service Name:</strong> {{ $product->name }}</p>
                    <p><strong>Purchase Date:</strong> {{ now()->format('F j, Y') }}</p>
                    <p><strong>Amount Paid:</strong> ₹{{ number_format($payment->amount, 2) }}</p>
                </div>

                <div class="section-divider"></div>

                <h2>How to Start Using Your Self Host Service</h2>
                <ol>
                    <li><strong>Installation Required:</strong> To set up your {{ $product->name }} service, please contact our team. We will assist you with the installation and configuration.</li>
                    <li><strong>Contact Us:</strong> You can reach out to us by any of the following methods:
                        <ul>
                            <li><strong>Email:</strong> <a href="mailto:support@aplu.io">support@aplu.io</a></li>
                            <li><strong>WhatsApp:</strong> <a href="https://wa.me/919999999999" target="_blank">+91-99999-99999</a></li>
                            <li><strong>Phone:</strong> <a href="tel:+911234567890">+91-1234567890</a></li>
                            <li><strong>Contact Person:</strong> Aplu Support Team</li>
                        </ul>
                    </li>
                    <li><strong>After Installation:</strong> Once installed, our team will provide you with all access details and documentation.</li>
                </ol>

                <div style="text-align: center;">
                    <a href="mailto:support@aplu.io" class="cta-button">Contact Support for Installation</a>
                </div>

            </div>

            <!-- Footer -->
            <div class="email-footer">
                &copy; {{ date('Y') }} Aplu. All rights reserved.<br> 
                <a href="https://aplu.io/privacy-policy/">Privacy Policy</a>
            </div>
        </div>
    </div>
</body>
</html>