<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Http\Resources\ItemCollection;
use App\Models\Item;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function getItems(): ItemCollection
    {
        return new ItemCollection(Item::all());
    }

    public function getItemDescription(Item $item): JsonResponse
    {
        return \response()->json([
            'description' => trim($item->description())
        ]);
    }

    public function storeItem(CreateItemRequest $itemRequest): Application|ResponseFactory|\Illuminate\Foundation\Application|Response
    {
        $item = new Item();
        $item->title = $itemRequest->title;
        $item->price = $itemRequest->price;
        $item->type = $itemRequest->type;
        $item->author_id = $itemRequest->author_id;
        $item->details = $itemRequest->details;
        $item->save();

        return \response('', 201);
    }
}
