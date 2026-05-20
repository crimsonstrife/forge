<?php

namespace App\Services\Feedback;

use App\Models\FeedbackBoard;
use App\Models\ServiceProduct;
use Illuminate\Http\Request;

class FeedbackBoardResolver
{
    public function forRequest(Request $request, string $slug): FeedbackBoard
    {
        /** @var ServiceProduct|null $product */
        $product = $request->attributes->get('service_product');

        abort_if($product === null, 401, 'A valid ingest key is required.');

        /** @var FeedbackBoard|null $board */
        $board = FeedbackBoard::query()
            ->where('service_product_id', $product->getKey())
            ->where('slug', $slug)
            ->first();

        abort_if(
            $board === null,
            404,
            "Feedback board [{$slug}] was not found for this ingest key's service product."
        );

        return $board;
    }
}
