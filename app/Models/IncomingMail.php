<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $id
 * @property int $customer_id
 * @property string $message_id
 * @property string $subject
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Ticket|null $ticket
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncomingMail withoutTrashed()
 * @mixin \Eloquent
 */
class IncomingMail extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'message_id',
        'subject',
        'body',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = ['created_at', 'deleted_at'];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function ticket(): HasOne {
        return $this->hasOne(Ticket::class, 'incoming_mail_id', 'id');
    }
}
