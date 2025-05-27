<?php

namespace App\Models;

use App\DTOs\MailDTO;
use Illuminate\Database\Eloquent\Model;

class MailBody extends Model {
    protected $fillable = [
        'mail_id',
        'subject',
        'body',
    ];

    public function mail() {
        return $this->belongsTo(Mail::class);
    }

    public function fromMailDTO(MailDTO $dto): self {
        $body = new self;

        $body->subject = $dto->subject;
        $body->body = $dto->body;
        return $body;        
    }
}
