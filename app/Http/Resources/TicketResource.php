<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{    
    public function toArray(Request $request): array {
        $data = parent::toArray($request);

        return collect($data)->mapWithKeys(function ($value, $key) {
            return [Str::camel($key) => $value];
        })->all();
    }
}
