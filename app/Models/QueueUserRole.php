<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $queue_id
 * @property int $user_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Queue $queue
 * @property-read \App\Models\UserRole $role
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole whereQueueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QueueUserRole whereUserId($value)
 * @mixin \Eloquent
 */
class QueueUserRole extends Model {
    use HasFactory;

    protected $table = 'queue_user_roles';

    protected $fillable = [
        'queue_id',
        'user_id',
        'role_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function queue() {
        return $this->belongsTo(Queue::class);
    }

    public function role() {
        return $this->belongsTo(UserRole::class, 'role_id');
    }
}
