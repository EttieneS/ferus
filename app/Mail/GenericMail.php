<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable {
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $bodyText;

    public function __construct(string $subjectLine, string $bodyText) {
        $this->subjectLine = $subjectLine;
        $this->bodyText = $bodyText;
    }

    public function build(): self {
        $this->withSwiftMessage(function ($message) {
            $headers = $message->getHeaders();

            foreach (['In-Reply-To', 'References', 'Message-ID'] as $headerName) {
                if ($headers->has($headerName)) $headers->remove($headerName);
            }

            $uniqueId = uniqid('msg_') . '.' . now()->timestamp . '@gmail.com';
            $headers->addTextHeader('Message-ID', "<{$uniqueId}>");
        });

        return $this->subject($this->subjectLine)
            ->view('emails.generic')
            ->with([
                'subjectLine' => $this->subjectLine,
                'bodyText' => $this->bodyText
            ]);
    }
}
