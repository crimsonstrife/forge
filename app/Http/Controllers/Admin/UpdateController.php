<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SelfUpdateService;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    public function __construct(private SelfUpdateService $selfUpdate) {}

    public function check(): JsonResponse
    {
        $current = $this->selfUpdate->currentVersion();

        return response()->json([
            'current' => $current,
            'updateAvailable' => $this->selfUpdate->isUpdateAvailable($current),
        ]);
    }

    public function run(): JsonResponse
    {
        $current = $this->selfUpdate->currentVersion();
        $result = $this->selfUpdate->runUpdate($current);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
