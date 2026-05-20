<?php

namespace App\Http\Controllers\Api\V1\Feedback\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminCommentController extends Controller
{
    /**
     * @group Feedback
     */
    public function destroy(FeedbackComment $comment): JsonResponse
    {
        DB::transaction(static function () use ($comment): void {
            $post = $comment->post;
            $comment->delete();
            $post?->forceFill(['comment_count' => $post->comments()->count()])->save();
        });

        return response()->json(['message' => 'Comment deleted.']);
    }
}
