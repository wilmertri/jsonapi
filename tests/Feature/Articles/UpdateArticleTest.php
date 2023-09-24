<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_articles(): void
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Update article',
            'slug' => 'update-article',
            'content' => 'Updated content'
        ])->assertOk();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' => [
                    'title' => 'Update article',
                    'slug' => 'update-article',
                    'content' => 'Updated content'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'update-article',
            'content' => 'Updated Content'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test  */
    public function title_must_be_at_least_4_characters(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article),[
            'title' => 'Upd',
            'slug' => 'update-article',
            'content' => 'Updated Content'
        ])->assertJsonApiValidationErrors('title');
    }


    /** @test */
    public function slug_is_required(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'content' => 'Updated Content'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required(): void
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Updated article',
            'slug' => 'updated-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
