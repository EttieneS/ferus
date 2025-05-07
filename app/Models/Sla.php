<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sla extends Model {
    protected $fillable = [
        'name',
        'minutes',
    ];

    public function queues(): HasMany {
        return $this->hasMany(Queue::class);
    }
}
