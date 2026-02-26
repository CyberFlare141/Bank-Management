<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanOtpMail extends Mailable
{
    use SerializesModels;

    public string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->subject('Your Loan OTP Code - MARS Bank')
            ->view('emails.loan-otp');
    }
}