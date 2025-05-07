<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

/**
 * 
 *
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $subject
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OutgoingMail withoutTrashed()
 * @mixin \Eloquent
 */
class OutgoingMail extends Model {
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'subject',
        'body',
        'sent_at',
    ];

    protected $dates = [
        'sent_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function fromDtoRequest(Request $request) {
        $this->ticket_id = $request->input('ticket_id');
        $this->user_id = $request->input('user_id');
        $this->subject = $request->input('subject');
        $this->body = $request->input('body');
        $this->sent_at = $request->input('created_at') ?? now();

        return $this;
    }

    public function sender() {
        return match ($this->user_type) {
            0 => $this->belongsTo(User::class, 'user_id'),
            1 => $this->belongsTo(Customer::class, 'user_id'),
            default => null,
        };
    }
}
