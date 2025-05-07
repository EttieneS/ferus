<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model {
    use SoftDeletes;

    protected $fillable = [
        'mail_id',
        'queue_id',
        'assigned_by',
        'assigned_to',
        'status',
        'priority',
        'split',
        'due_date',
    ];

    public function mail() {
        return $this->belongsTo(Mail::class);
    }

    public function queue() {
        return $this->belongsTo(Queue::class);
    }

    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy() {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
