<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

/**
 * 
 *
 * @property int $id
 * @property string $reference
 * @property int $incoming_mail_id
 * @property int $queue_id
 * @property int|null $assigned_by
 * @property int|null $assigned_to
 * @property int $status
 * @property int $priority
 * @property bool $split
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedBy
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \App\Models\Customer|null $customer
 * @property-read string $ref_number
 * @property-read \App\Models\IncomingMail $incomingMail
 * @property-read \App\Models\Queue $queue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereAssignedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereIncomingMailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereQueueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereSplit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket withoutTrashed()
 * @mixin \Eloquent
 */
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
        'due_date',
    ];

    protected $casts = [
        'split' => 'boolean',
        'status' => 'integer',
        'priority' => 'integer',
    ];

    public function getRefNumberAttribute(): string {
        return 'TCK-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function incomingMail(): BelongsTo {
        return $this->belongsTo(IncomingMail::class, 'incoming_mail_id', 'id');
    }

    public function assignedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function assignedTo(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    // public function queue() {
    //     return $this->belongsTo(Queue::class, 'queue_id', 'id');
    // }

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

    protected $appends = ['ref_number'];

    protected static function booted() {
        static::creating(function ($ticket) {
            if (!$ticket->due_date) {
                $ticket->due_date = now()->addDays(3);
            }
        });
    }

    public static function fromRequest(Request $request): self {
        $ticket = new self();
        $ticket->id = $request->input('id');
        $ticket->assigned_to = $request->input('assigned_to');
        $ticket->assigned_by = $request->input('assigned_by');
        $ticket->queue_id = $request->input('queue_id');

        return $ticket;
    }

    public static function getCollection(Collection $tickets): array {
        return $tickets->map(fn($ticket) => $ticket->toArray())->values()->toArray();
    }

    public function queue(): BelongsTo {
        return $this->belongsTo(Queue::class, 'queue_id', 'id');
    }

    // public function ticketUsers(): HasMany {
    //     return $this->hasMany(TicketUser::class);
    // }
}
