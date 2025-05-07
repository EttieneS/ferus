<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingMailRecipient extends Model {
    use SoftDeletes;

    protected $fillable = [
        'outgoing_mail_id',
        'mail_type',
        'recipient_type',
        'recipient_id',
    ];

    public function outgoingMail(): BelongsTo {
        return $this->belongsTo(OutgoingMail::class);
    }

    public function recipient(): MorphTo {
        return $this->morphTo(null, 'recipient_type', 'recipient_id');
    }
}
