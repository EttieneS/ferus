<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Note extends Model {
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
    ];

    protected $casts = [
        'id' => 'integer',
        'ticket_id' => 'integer',
        'user_id' => 'integer',
        'body' => 'string',
    ];

    public static function fromRequest(Request $data): self {
        return new self([
            'ticket_id' => $data['ticket_id'],
            'user_id' => $data['user_id'],
            'body' => $data['body'],
        ]);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
