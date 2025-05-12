<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Sla;

class SlaService {
    public function getAll(): \Illuminate\Support\Collection {
        return Cache::remember('slas.all', 3600, function () {
            return Sla::select('id', 'name')->get();
        });
    }
}
