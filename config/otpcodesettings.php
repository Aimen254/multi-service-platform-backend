<?php

    return [
        'twilio_sid' => getenv("TWILIO_SID"),
        'twilio_token' => getenv("TWILIO_TOKEN"),
        'twilio_phone' => getenv("TWILIO_FROM"),
        'otp_message' => 'Your otp code is:'
    ];

?>