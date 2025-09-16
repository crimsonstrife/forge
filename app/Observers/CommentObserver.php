<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    public function saving(Comment $comment): void
    {
        if ($comment->parent_id) {
            $parent = Comment::select(['id','commentable_type','commentable_id'])->find($comment->parent_id);
            if ($parent) {
                $comment->commentable_type = $parent->commentable_type;
                $comment->commentable_id   = $parent->commentable_id;
                // also inherit file-thread context
                if (is_null($comment->context_media_id)) {
                    $comment->context_media_id = $parent->context_media_id;
                }
            }
        }
    }
}
