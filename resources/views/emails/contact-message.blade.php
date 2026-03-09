<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f4f6f9; padding:32px; color:#111827;">
    <div style="max-width:640px; margin:auto; background:#ffffff; border-radius:10px; border:1px solid #e5e7eb; overflow:hidden;">
        <div style="background:#1d4ed8; color:#ffffff; padding:16px 22px;">
            <h2 style="margin:0; font-size:20px;">MARS Bank - Contact Message</h2>
        </div>

        <div style="padding:22px;">
            <p style="margin:0 0 14px;"><strong>Sender Name:</strong> {{ $payload['sender_name'] }}</p>
            <p style="margin:0 0 14px;"><strong>Sender Email:</strong> {{ $payload['sender_email'] }}</p>
            <p style="margin:0 0 14px;"><strong>Subject:</strong> {{ $payload['subject'] }}</p>
            <p style="margin:0 0 14px;"><strong>Submitted At:</strong> {{ $payload['submitted_at'] }}</p>
            <p style="margin:0 0 8px;"><strong>Message:</strong></p>
            <div style="padding:14px; border:1px solid #e5e7eb; border-radius:8px; background:#f9fafb; white-space:pre-wrap;">{{ $payload['message_body'] }}</div>
        </div>
    </div>
</body>
</html>
