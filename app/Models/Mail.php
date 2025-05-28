<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model {
    use SoftDeletes;

    const USER = 0;
    const CUSTOMER = 1;

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'to_users',
        'cc_users',
        'to_customers',
        'cc_customers',
        'is_internal',
    ];

    protected $casts = [
        'to_users' => 'array',
        'cc_users' => 'array',
        'to_customers' => 'array',
        'cc_customers' => 'array',
        'is_internal' => 'boolean'
    ];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'sender_id')->where('sender_type', self::USER);
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'sender_id')->where('sender_type', self::CUSTOMER);
    }

    public function body() {
        return $this->hasOne(MailBody::class);
    }
}
