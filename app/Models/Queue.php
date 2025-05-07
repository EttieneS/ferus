<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $mailer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QueueUserRole> $userRoles
 * @property-read int|null $user_roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereMailer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Queue withoutTrashed()
 * @mixin \Eloquent
 */
class Queue extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'queues';

    protected $fillable = [
        'name',
        'mailer',
    ];

    protected $casts = [
        'id' => 'integer',
    ];

    public function userRoles() {
        return $this->hasMany(QueueUserRole::class);
    }

    public function sla(): BelongsTo {
        return $this->belongsTo(Sla::class);
    }
}
