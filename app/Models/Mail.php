<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'message',
        'message_id',
        'customer_id'
    ];

    protected $casts = [
        'id' => 'string',
        'customer_id' => 'string',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function tickets() {
        return $this->hasMany(Ticket::class, 'mail_id');
    }
}
