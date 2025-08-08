<?php

namespace App\Http\Controllers\v1\Dashboard;

use App\Filters\CategoryFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\CategoryCreateRequest;
use App\Http\Requests\v1\CategoryUpdateRequest;
use App\Http\Requests\v1\ImageUploadRequest;
use App\Http\Resources\v1\Dashboard\Category\CategoryCollection;
use App\Http\Resources\v1\Dashboard\Category\CategoryDropoutResource;
use App\Http\Resources\v1\Dashboard\Category\CategoryResource;
use App\Http\Resources\v1\MediaResource;
use App\Models\Category;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    use ImageTrait;

    public function index(Request $request, CategoryFilter $filter)
    {
        $categories = Category::filter($filter);
        if (isset($request->search)) {
            $ids = Category::search($request->search)->get()->pluck('id')->toArray();
            Log::debug('categoryids', ['ids' => $ids]);
            $categories = $categories->whereIn('id', $ids);
        }
        $categories = $categories->paginate($request->per_page ?? self::PER_PAGE);

        return new CategoryCollection($categories);
    }

    public function dropout(Request $request)
    {
        $categories = Category::query();
        if (isset($request->search)) {
            $categories = $categories->where('name', 'like', '%' . $request->search . '%');
        }
        $categories = $categories->orderBy('name')->get();

        return CategoryDropOutResource::collection($categories);
    }

    public function store(CategoryCreateRequest $request)
    {
        /** @var Category $category */
        $category = Category::create($request->validated());
        if ($request->file('image')) {
            $this->uploadPhoto($request->file('image'), $category);
        }

        return new CategoryResource($category);
    }

    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    public function update(CategoryUpdateRequest $request, string $id)
    {
        /** @var Category $category */
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return new CategoryResource($category->refresh());
    }

    public function uploadImage(ImageUploadRequest $request, string $id)
    {
        /** @var Category $category */
        $category = Category::findOrFail($id);
        $this->uploadPhoto($request->file('image'), $category);

        return new MediaResource($category->getFirstMedia());
    }

    public function deleteImage(string $id)
    {
        /** @var Category $category */
        $category = Category::findOrFail($id);
        $category->clearMediaCollection();

        return new CategoryResource($category->refresh());
    }

    public function destroy(string $id)
    {
        /** @var Category $category */
        $category = Category::findOrFail($id);

        Category::whereParentId($id)->update(['parent_id' => null]);

        $category->delete();

        return response()->json(['message' => 'ok']);
    }
}
