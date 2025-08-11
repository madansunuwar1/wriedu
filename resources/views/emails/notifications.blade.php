<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        .email-wrapper {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .wri-logo {
            font-size: 24px;
            font-weight: bold;
            color: #4a90e2;
        }
        .email-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4a90e2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <div class="wri-logo">WRIEducations</div>
        </div>

        <div class="email-content">
            <h2>{{ $greeting }}</h2>

            @foreach($content as $line)
                <p>{{ $line }}</p>
            @endforeach

            @if(isset($actionText) && isset($actionUrl))
                <div style="text-align: center;">
                    <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
                </div>
            @endif

            @if(isset($endText))
                <p>{{ $endText }}</p>
            @endif
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} WRIEducations. All rights reserved.</p>
        </div>
    </div>
</body>
</html>