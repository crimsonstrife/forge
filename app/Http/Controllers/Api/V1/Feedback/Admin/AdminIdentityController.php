<?php

namespace App\Http\Controllers\Api\V1\Feedback\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\AdminIdentityBlockRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackIdentityResource;
use App\Models\FeedbackIdentity;

class AdminIdentityController extends Controller
{
    /**
     * @group Feedback
     */
    public function block(AdminIdentityBlockRequest $request, FeedbackIdentity $identity): FeedbackIdentityResource
    {
        if ($request->boolean('blocked')) {
            $identity->forceFill([
                'blocked_at' => now(),
                'blocked_reason' => $request->string('reason')->toString() ?: null,
                'blocked_by_user_id' => $request->user()?->getKey(),
            ])->save();
        } else {
            $identity->forceFill([
                'blocked_at' => null,
                'blocked_reason' => null,
                'blocked_by_user_id' => null,
            ])->save();
        }

        return FeedbackIdentityResource::make($identity);
    }
}
