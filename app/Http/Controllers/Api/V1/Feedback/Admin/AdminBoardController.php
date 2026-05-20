<?php

namespace App\Http\Controllers\Api\V1\Feedback\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\AdminBoardRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackBoardResource;
use App\Models\FeedbackBoard;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminBoardController extends Controller
{
    /**
     * @group Feedback
     */
    public function index(): AnonymousResourceCollection
    {
        return FeedbackBoardResource::collection(
            FeedbackBoard::query()->with(['product:id,name', 'categories', 'statuses'])->latest()->paginate(25)
        );
    }

    /**
     * @group Feedback
     */
    public function store(AdminBoardRequest $request): FeedbackBoardResource
    {
        /** @var FeedbackBoard $board */
        $board = FeedbackBoard::query()->create($request->validated());

        return FeedbackBoardResource::make($board->load(['product:id,name', 'categories', 'statuses']));
    }

    /**
     * @group Feedback
     */
    public function update(AdminBoardRequest $request, FeedbackBoard $board): FeedbackBoardResource
    {
        $board->fill($request->validated())->save();

        return FeedbackBoardResource::make($board->fresh(['product:id,name', 'categories', 'statuses']));
    }
}
