<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .header {
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .verification-code {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            font-size: 32px;
            letter-spacing: 5px;
            margin: 20px 0;
            border-radius: 5px;
            color: #495057;
        }
        .message {
            color: #6c1c6c;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .expiry-notice {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .footer {
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="logo">
            <!-- Add your company logo here -->
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Company Logo" height="50">
        </div>
        
        <div class="header">
            Verify Your Email Address
        </div>
        
        @if($userName)
        <div class="message">
            Hello {{ $userName }},
        </div>
        @endif
        
        <div class="message">
            Please use the following verification code to complete your email verification:
        </div>
        
        <div class="verification-code">
            {{ $otp }}
        </div>
        
        <div class="expiry-notice">
            This verification code will expire in 10 minutes.
        </div>
        
        <div class="message">
            If you did not request this code, no further action is required.
        </div>
        
        <div class="footer">
            <p>This is an automated message, please do not reply.</p>
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>