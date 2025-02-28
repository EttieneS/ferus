<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'address'
    ];

    public function tickets() {
        return $this->hasMany(Ticket::class, 'customer_id', 'id');
    }

    public function queuedTickets() {
        return $this->hasMany(QueuedTicket::class, 'customer_id', 'id');
    }
}
