<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Feedback\FeedbackIdentityResource;
use App\Models\FeedbackIdentity;
use Illuminate\Http\Request;

class AuthMeController extends Controller
{
    /**
     * @group Feedback
     */
    public function __invoke(Request $request): FeedbackIdentityResource
    {
        /** @var FeedbackIdentity $identity */
        $identity = $request->attributes->get('feedback_identity');

        return FeedbackIdentityResource::make($identity);
    }
}
