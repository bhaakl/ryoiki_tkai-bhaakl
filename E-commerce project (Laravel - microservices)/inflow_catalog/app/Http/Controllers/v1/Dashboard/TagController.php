<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\TagCreateRequest;
use App\Http\Resources\v1\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::query();
        if ($request->has('search')) {
            $tags = $tags->where('value', 'like', "%{$request->get('search')}%");
        }
        $tags = $tags->get();

        return response()->json(TagResource::collection($tags));
    }

    public function store(TagCreateRequest $request)
    {
        $tag = Tag::create($request->validated());

        return response()->json(new TagResource($tag));
    }

    public function destroy($id)
    {
        if (!Tag::whereId($id)->exists()) {
            abort(404, 'Тэг не найден');
        }
        Tag::whereId($id)->delete();

        return response()->json(['message' => 'ok']);
    }
}
