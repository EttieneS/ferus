<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IncomingMail extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'incoming_mails';

    protected $primaryKey = 'id';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'id' => 'int',
        'customer_id' => 'int',
    ];

    protected $fillable = [
        'subject',
        'body',
        'message_id',
        'customer_id'
    ];

    protected $dates = ['created_at', 'deleted_at'];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function ticket(): HasOne {
        return $this->hasOne(Ticket::class, 'incoming_mail_id', 'id');
    }
}
