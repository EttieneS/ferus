<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queue extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'queues';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function assignedTickets() {
        return $this->hasMany(QueuedTicket::class, 'queue_id');
    }
}
