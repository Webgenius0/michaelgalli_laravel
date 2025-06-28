<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 20px;
        }
        .content {
            padding: 0 15px;
        }
        .details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🚨 New Support Request</h2>
        </div>

        <div class="content">
            <p>Hello Support Team,</p>
            <p>You have received a new support request from a user. Here are the details:</p>

            <div class="details">
                <p><strong>Name:</strong> {{  $name }}</p>
                <p><strong>Phone Number :</strong> {{  $phone }}</p>
                <p><strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a></p>
                <p><strong>Message:</strong></p>
                <p>{{ $userMessage }}</p>
            </div>

            <p>Please respond to this request as soon as possible.</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply directly to this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
