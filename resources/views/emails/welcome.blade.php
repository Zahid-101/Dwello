<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #f97316;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 30px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 10px 10px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Dwello!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $user->name }},</p>
            <p>Your account is successfully created in Dwello.</p>
            <p>Thanks for using Dwello!</p>
            <br>
            <p>Best regards,<br>The Dwello Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Dwello. All rights reserved.
        </div>
    </div>
</body>

</html>