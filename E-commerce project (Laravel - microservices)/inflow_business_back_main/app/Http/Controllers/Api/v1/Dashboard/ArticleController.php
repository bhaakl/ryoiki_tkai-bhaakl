<?php

namespace App\Http\Controllers\Api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Dashboard\ArticleCreateRequest;
use App\Http\Requests\v1\Dashboard\ArticleUpdateRequest;
use App\Http\Requests\v1\Dashboard\ArticleUploadImageRequest;
use App\Http\Resources\v1\Dashboard\ArticleCollection;
use App\Http\Resources\v1\Dashboard\ArticleDetailResource;
use App\Models\Article;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ImageTrait;

    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function index(Request $request)
    {
        $articles = Article::latest()->paginate($request->per_page ?? 15);

        return api_response(new ArticleCollection($articles));
    }

    public function show($id)
    {
        /** @var Article $article */
        $article = Article::findOrFail($id);

        return api_response(new ArticleDetailResource($article));
    }

    public function store(ArticleCreateRequest $request)
    {
        $article = Article::create($request->validated());
        $this->uploadPhoto($request->file('image'), $article);

        return api_response(new ArticleDetailResource($article));
    }

    public function update(ArticleUpdateRequest $request, $id)
    {
        /** @var Article $article */
        $article = Article::findOrFail($id);

        $article->update($request->validated());

        return api_response(new ArticleDetailResource($article));
    }

    public function updateImage(ArticleUploadImageRequest $request, $id)
    {
        /** @var Article $article */
        $article = Article::findOrFail($id);

        $this->uploadPhoto($request->file('image'), $article);

        return api_response(new ArticleDetailResource($article));
    }

    public function destroy($id)
    {
        /** @var Article $article */
        $article = Article::findOrFail($id);

        $article->delete();

        return api_response(['message' => 'ok']);
    }
}
