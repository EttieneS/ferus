<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailRecipient extends Model {
    use SoftDeletes;

    const RECIPIENT_TYPE_USER = 0;
    const RECIPIENT_TYPE_CUSTOMER = 1;

    const SEND_TYPE_TO = 0;
    const SEND_TYPE_CC = 1;

    protected $fillable = [
        'mail_id',
        'recipient_id',
        'recipient_type',
        'send_type',
    ];

    public function mail() {
        return $this->belongsTo(Mail::class);
    }

    public function recipient() {
        return $this->morphTo(null, 'recipient_type', 'recipient_id');
    }
}
