<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class Ticket extends Model {
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'incoming_mail_id',
        'assigned_by',
        'assigned_to',
        'queue_id',
        'status',
        'priority',
        'split',
    ];

    protected $casts = [
        'split' => 'boolean',
        'status' => 'integer',
        'priority' => 'integer',
    ];

    public function incomingMail(): BelongsTo {
        return $this->belongsTo(IncomingMail::class, 'incoming_mail_id', 'id');
    }

    public function assignedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function assignedTo(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function queue() {
        return $this->belongsTo(Queue::class, 'queue_id', 'id');
    }

    public function customer() {
        return $this->hasOneThrough(
            Customer::class,
            IncomingMail::class,
            'id',
            'id',
            'incoming_mail_id',
            'customer_id'
        );
    }

    public static function fromRequest(Request $request): self {
        return new self($request->only([
            'id',
            'customer_id',
            'incoming_mail_id',
            'assigned_to',
            'assigned_by',
            'queue_id',
            'status',
            'priority',
            'split'
        ]));
    }

    public static function getCollection(Collection $tickets): array {
        return $tickets->map(fn($ticket) => $ticket->toArray())->values()->toArray();
    }
}
