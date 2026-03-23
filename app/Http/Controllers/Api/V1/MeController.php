<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

final class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id'        => $user->getAuthIdentifier(),
                'name'      => $user->name,
                'email'     => $user->email,
                'avatar_url' => $user->profile_photo_url ?? null,
                'forge_url' => config('app.url'),
            ],
        ]);
    }
}
