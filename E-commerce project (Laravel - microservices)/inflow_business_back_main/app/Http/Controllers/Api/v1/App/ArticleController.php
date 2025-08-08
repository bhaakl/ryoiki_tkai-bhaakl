<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\App\ArticleCollection;
use App\Http\Resources\v1\App\ArticleDetailResource;
use App\Http\Resources\v1\App\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::latest()->paginate($request->per_page ?? 15);

        return api_response(new ArticleCollection($articles));
    }

    public function show(Article $article)
    {
        return api_response(new ArticleDetailResource($article));
    }
}
