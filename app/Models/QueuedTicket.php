<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QueuedTicket extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'queued_tickets';

    protected $fillable = [
        'ticket_id',
        'queue_id',
        'assigned_by',
        'status',
        'priority',
    ];

    protected $casts = [
        'id' => 'integer',
        'ticket_id' => 'integer',
        'queue_id' => 'integer',
        'assigned_by' => 'integer',
        'status' => 'integer',
        'priority' => 'integer',
    ];

    public function ticket() {
        return $this->hasOne(Ticket::class, 'id', 'ticket_id');
    }

    public function customer() {
        return $this->hasOneThrough(Customer::class, Ticket::class, 'id', 'id', 'ticket_id', 'customer_id');
    }   
}
