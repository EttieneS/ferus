<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailRecipient extends Model {
    use SoftDeletes;

    protected $fillable = [
        'mail_id',
        'recipient_id',
        'recipient_type', // 0 = user, 1 = customer
        'send_type', // 0 = to, 1 = cc
    ];

    public function mail() {
        return $this->belongsTo(Mail::class);
    }

    public function recipient() {
        return $this->morphTo(null, 'recipient_type', 'recipient_id');
    }
}
