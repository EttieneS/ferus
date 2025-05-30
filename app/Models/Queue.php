<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name
 * @property string $mailer
 * @property string|null $host
 * @property int|null $port
 * @property string|null $encryption
 * @property string|null $username
 * @property string|null $password
 * @property string|null $from_name
 * @property string|null $from_email
 * @property int|null $sla_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QueueUserRole> $userRoles
 * @property-read int|null $user_roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue query()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereMailer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereHost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereEncryption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereFromEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereSlaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Queue withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Queue withoutTrashed()
 * @mixin \Eloquent
 */
class Queue extends Model {
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

    public static function fromArray(array $data): self {
        $queue = new self;

        $queue->id = $data['id'] ?? null;
        $queue->name = $data['name'] ?? '';
        
        return $queue;
    }

    public function userRoles() {
        return $this->hasMany(QueueUserRole::class);
    }

    public function sla(): BelongsTo {
        return $this->belongsTo(Sla::class);
    }
}
