<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MailMeta extends Model {
    use SoftDeletes;

    public const RECIPIENT_TYPE_USER = 0;
    public const RECIPIENT_TYPE_CUSTOMER = 1;

    public const RECIPIENT_ROLE_TO = 0;
    public const RECIPIENT_ROLE_CC = 1;

    protected $fillable = [
        'mail_id',
        'in_reply_to',
        'toUsers',
        'ccUsers',
        'toCustomers',
        'ccCustomers',
    ];

    protected $casts = [
        'toUsers' => 'array',
        'ccUsers' => 'array',
        'toCustomers' => 'array',
        'ccCustomers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function mail() {
        return $this->belongsTo(Mail::class);
    }

    public function repliedTo() {
        return $this->belongsTo(Mail::class, 'in_reply_to');
    }
}
