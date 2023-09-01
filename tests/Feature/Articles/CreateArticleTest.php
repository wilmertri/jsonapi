<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles(): void
    {

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New article',
                    'slug' => 'new-article',
                    'content' => 'Content of article'
                ]
            ]
        ]);

        $response->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'New article',
                    'slug' => 'new-article',
                    'content' => 'Content of article'
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

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'slug' => 'new-article',
                    'content' => 'Content of article'
                ]
            ]
        ]);

        $response->assertJsonValidationErrors('data.attributes.title');
    }

    /** @test */
    public function slug_is_required(): void
    {

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New article',
                    'content' => 'Content of article'
                ]
            ]
        ]);

        $response->assertJsonValidationErrors('data.attributes.slug');
    }

    /** @test */
    public function content_is_required(): void
    {

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'New article',
                    'slug' => 'new-article',
                ]
            ]
        ]);

        $response->assertJsonValidationErrors('data.attributes.content');
    }
}
