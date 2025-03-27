<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class GenericMail extends Mailable {
    public $subject;
    public $body;

    public function __construct(string $subject, string $body) {
        $this->subject = $subject;
        $this->body = $body;
    }

    public function build() {
        return $this->subject($this->subject)
            ->view('emails.generic')
            ->with(['body' => $this->body]);
    }
}
