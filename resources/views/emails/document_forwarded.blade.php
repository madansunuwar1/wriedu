<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Forwarded</title>
        <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .xyz{
            font-size:20px;
            font-weight:bold;
            font-family:poppins;
            text-align:center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2e8b57, #3cb371);
            padding: 20px;
            text-align: center;
            color: white;
        }
        .logo-container {
            margin-bottom: 15px;
        }
        .logo {
            height: 50px;
            width: auto;
        }
        .content {
            padding: 30px;
            background-color: #fdfdfd;
        }
        .user-greeting {
            font-weight: bold;
            font-size: 20px;
            color: #2e8b57;
            text-align: center;
        }
        .lead-details {
            background-color: #fdfdfd;
            border-left: 4px solid #2e8b57;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 4px 4px 0;
        }
        .lead-details h3 {
            margin-top: 0;
            color: #2e8b57;
        }
        .lead-details ul {
            list-style-type: none;
            padding-left: 0;
        }
        .lead-details li {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .lead-details li:last-child {
            border-bottom: none;
        }
        .notes-section {
            background-color: #fdfdfd;
            border-left: 4px solid #2e8b57;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 4px 4px 0;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #2e8b57, #3cb371);
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 4px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .action-button:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #267349, #329d61);
        }
        .footer {
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding: 15px 30px;
            background-color: #f5f5f5;
            text-align: center;
        }

        /* Media Queries for Responsive Design */
        @media only screen and (max-width: 640px) {
            .container {
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0;
                box-shadow: none;
            }
            .content {
                padding: 20px !important;
            }
            .header h2 {
                font-size: 22px !important;
            }
            .user-greeting {
                font-size: 18px !important;
            }
        }

        @media only screen and (max-width: 480px) {
            .content {
                padding: 15px !important;
            }
            .lead-details, .notes-section {
                padding: 10px !important;
            }
            .header {
                padding: 15px !important;
            }
            .header h2 {
                font-size: 20px !important;
                margin: 10px 0 !important;
            }
            .logo {
                height: 40px !important;
            }
            .action-button {
                display: block !important;
                width: 100% !important;
                padding: 10px !important;
                text-align: center !important;
                box-sizing: border-box !important;
            }
        }

        /* Ensure Outlook doesn't mess up the styling */
        <!--[if mso]>
        <style>
            .container {
                width: 600px !important;
            }
        </style>
        <![endif]-->
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="https://wrieducation.com/wp-content/uploads/2024/01/cropped-logo.png" alt="WRI Logo" class="logo" />
            </div>
            <h2 class="xyz">Lead Forwarded Notification</h2>
        </div>
        
         <div class="content">
            <p>Hello <span  class="user-greeting">{{ $recipient->name }}</span>,</p>
            
            <p>A lead has been forwarded to you by <strong>{{ $sender->name }}</strong>.</p>
            
            <div class="lead-details">
                <h3>Lead Details:</h3>
                <ul>
                    <li><strong>Name:</strong> {{ $lead->name }}</li>
                    <li><strong>Email:</strong> {{ $lead->email }}</li>
                    <li><strong>Phone:</strong> {{ $lead->phone }}</li>
                    <li><strong>Location:</strong> {{ $lead->location }}</li>
                    @if($lead->university)
                    <li><strong>University:</strong> {{ $lead->university }}</li>
                    @endif
                    @if($lead->course)
                    <li><strong>Course:</strong> {{ $lead->course }}</li>
                    @endif
                    @if($lead->intake)
                    <li><strong>Intake:</strong> {{ $lead->intake }}</li>
                    @endif
                </ul>
            </div>
            
            @if($notes)
            <div class="notes-section">
                <h3>Notes:</h3>
                <p>{{ $notes }}</p>
            </div>
            @endif
            
            <p>Please log in to the system to view the complete details and take appropriate action.</p>
            
            <div style="text-align: center;">
    <a href="{{ route('backend.application.view', ['id' => $applicationId]) }}" class="action-button">
        View Full Details for {{ $applicantName }}
    </a>
</div>
        </div> 
        
        <div class="footer">
            <p>This is an automated email. Please do not reply.</p>
            <p>&copy; {{ date('Y') }} WRI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>