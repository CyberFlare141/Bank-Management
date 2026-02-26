<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loan OTP</title>
</head>
<body style="font-family: Arial; background:#f4f6f9; padding:40px;">

    <div style="max-width:500px;margin:auto;background:white;padding:30px;border-radius:10px;text-align:center;">
        <h2 style="color:#1e40af;">MARS Bank</h2>

        <p>Your One-Time Password (OTP) for loan verification is:</p>

        <h1 style="letter-spacing:5px;color:#111;">{{ $otp }}</h1>

        <p>This code will expire in 5 minutes.</p>

        <p style="color:gray;font-size:14px;">
            If you did not request this loan, please ignore this email.
        </p>
    </div>

</body>
</html>