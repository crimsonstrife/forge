<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Organization listing for machine-to-machine (client credentials) callers.
 *
 * Returns organizations visible to the user identified by `for_forge_user_id`.
 * Protected by the `client` middleware (Passport client credentials).
 */
final class SystemOrganizationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->resolveScopedUser($request);

        if (! $user) {
            return response()->json(['data' => []]);
        }

        $q = (string) $request->string('q');

        $orgs = Organization::query()
            ->visibleTo($user)
            ->when($q !== '', fn ($qb) => $qb->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->get();

        $data = $orgs->map(fn (Organization $org) => [
            'id' => (string) $org->id,
            'name' => $org->name,
            'slug' => $org->slug,
            'crucible_org_id' => $org->crucible_org_id,
            'crucible_org_slug' => $org->crucible_org_slug,
        ]);

        return response()->json(['data' => $data->values()]);
    }

    public function show(Request $request, Organization $organization): JsonResponse
    {
        $user = $this->resolveScopedUser($request);

        abort_if(! $user, Response::HTTP_NOT_FOUND);
        abort_unless($organization->isAccessibleBy($user), Response::HTTP_NOT_FOUND);

        return response()->json(['data' => [
            'id' => (string) $organization->id,
            'name' => $organization->name,
            'slug' => $organization->slug,
            'crucible_org_id' => $organization->crucible_org_id,
            'crucible_org_slug' => $organization->crucible_org_slug,
        ]]);
    }

    private function resolveScopedUser(Request $request): ?User
    {
        $forUserId = $request->string('for_forge_user_id')->toString();

        if ($forUserId === '') {
            $forUserId = $request->string('for_user')->toString();
        }

        if ($forUserId === '') {
            return null;
        }

        return User::find($forUserId);
    }
}
