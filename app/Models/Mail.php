<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model {
    use SoftDeletes;

    // user_type
    const USER = 0;
    const CUSTOMER = 1;

    // origin
    const INTERNAL = 0;
    const EXTERNAL = 1;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'origin',
        'subject',
        'body',
        'in_reply_to',
    ];

    public function recipients() {
        return $this->hasMany(MailRecipient::class);
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'sender_id');
    }
}
