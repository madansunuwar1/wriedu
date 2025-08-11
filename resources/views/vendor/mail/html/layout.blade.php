<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
  
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Base styles */
        body {
            background-color: #f5f5f5;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        /* Main container */
        .email-wrapper {
            width: 100%;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(135deg, #ffffff 0%, #f8fff8 100%);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: #ffffff;
            text-align: center;
            padding: 30px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .header-logo {
            max-width: 200px;
            height: auto;
        }

        /* Content */
        .email-content {
            padding: 40px;
            background: #ffffff;
            color: #333333;
        }

        h1 {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 24px;
        }

        p {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        /* Button */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }

        /* Footer */
        .email-footer {
            background: White;
            color: white;
            text-align: center;
          
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            font-size: 14px;
        }

        /* Utility classes */
        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        /* Links */
        a {
            color: #2e7d32;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Table styles for email client compatibility */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            padding: 0;
        }

        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
            }

            .email-content {
                padding: 20px !important;
            }

            .button {
                display: block;
                text-align: center;
                width: 100%;
            }
        }

        /* Optional dark mode support */
        @media (prefers-color-scheme: dark) {
            .email-container {
                background: linear-gradient(135deg, #2a3a2a 0%, #243524 100%);
                color: #ffffff;
            }

            .email-content {
                background-color: #1a2e1a;
                color: #e0f0e0;
            }

            .email-footer {
                background: linear-gradient(135deg, #2a3a2a 0%, #243524 100%);
                color: #a0c8a0;
            }

            h1 {
                color: #81c784;
            }

            a {
                color: #81c784;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" class="email-container">
            <!-- Header -->
            <tr>
                <td class="email-header">
                    {{ $header ?? '' }}
                </td>
            </tr>

            <!-- Content -->
            <tr>
                <td class="email-content">
                    {{ Illuminate\Mail\Markdown::parse($slot) }}

                    @if(isset($subcopy))
                    <div class="mt-20" style="border-top: 1px solid #e8f5e8; padding-top: 20px; color: #666666; font-size: 14px;">
                        {{ $subcopy }}
                    </div>
                    @endif
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="email-footer">
                    {{ $footer ?? '' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>