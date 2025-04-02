<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingMail extends Model {
    use SoftDeletes;

    protected $table = 'outgoing_mail';

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
}
