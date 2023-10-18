<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SaveArticleRequest;
use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ArticleController extends Controller
{

    public function index(): ArticleCollection
    {
        $articles = Article::allowedSorts(['title', 'content']);

        return ArticleCollection::make($articles->get());
    }
    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    public function store(SaveArticleRequest $request): ArticleResource
    {

        $article = Article::create($request->validated());

        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request): ArticleResource
    {

        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $article->delete();
        return response()->noContent();
    }
}
