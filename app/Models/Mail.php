<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model {
    use SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'user_type',
        'subject',
        'body',
    ];

    public function recipients() {
        return $this->hasMany(MailRecipient::class);
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    public function senderUser() {
        return $this->belongsTo(User::class, 'user_id')->where('user_type', 0);
    }

    public function customerSend() {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
