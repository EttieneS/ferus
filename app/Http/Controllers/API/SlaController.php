<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\SlaService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Sla;

class SlaController extends Controller {
    protected SlaService $slaService;

    public function __construct(SlaService $slaService) {
        $this->slaService = $slaService;
    }

    public function index(): JsonResponse {
        $slas = $this->slaService->getAll();
        return response()->json($slas);
    }
}
