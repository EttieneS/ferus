<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Queue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'queues';

    protected $fillable = [
        'name',
        'mailer',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_name',
        'from_email',
        'sla_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'port' => 'integer',
        'sla_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function fromArray(array $data): self
    {
        $queue = new self;

        $queue->id = $data['id'] ?? null;
        $queue->name = $data['name'] ?? '';

        return $queue;
    }


    public static function fromRequest(Request $request): self {
        $data = [
            'name' => $request->input('name'),
            'mailer' => $request->input('mailer'),
            'host' => $request->input('host'),
            'port' => $request->input('port'),
            'encryption' => $request->input('encryption'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'from_name' => $request->input('from_name'),
            'from_email' => $request->input('from_email'),
            'sla_id' => $request->input('sla_id'),
        ];

        $queue = new self($data);

        if ($request->has('id')) {
            $queue->id = (int) $request->input('id');
        }

        return $queue;
    }

    public function userRoles() {
        return $this->hasMany(QueueUserRole::class);
    }

    public function sla(): BelongsTo
    {
        return $this->belongsTo(Sla::class);
    }
}
