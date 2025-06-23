<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }

        .otp-code {
            font-weight: bold;
            font-size: 20px;
            text-align: center;
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>OTP Verification</h1>
        <p>Hi,</p>
        <p>Thank you for choosing Your Brand. Use the following OTP to complete your sign-up procedures. OTP is valid
            for 15 minutes.</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>Best Regards,<br>bdCalling It Ltd.</p>
    </div>
</body>

</html>
