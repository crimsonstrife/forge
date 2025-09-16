<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

final class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $request->user()->getAuthIdentifier(),
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ]);
    }
}
