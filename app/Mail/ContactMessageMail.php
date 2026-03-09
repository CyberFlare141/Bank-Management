<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use SerializesModels;

    /**
     * @var array<string, string>
     */
    public array $payload;

    /**
     * @param array<string, string> $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function build(): self
    {
        return $this
            ->subject('Contact Message: '.$this->payload['subject'])
            ->view('emails.contact-message');
    }
}
