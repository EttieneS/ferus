<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignedTicket extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'assigned_tickets';

    protected $fillable = [
        'ticket_id',
        'queue_id',
        'assigned_by',
        'status',
        'priority',
    ];

    protected $casts = [
        'id' => 'string',
        'ticket_id' => 'string',
        'queue_id' => 'string',
        'assigned_by' => 'string',
    ];
}
