<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles(): void
    {

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'new-article',
            'content' => 'Content of article'
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
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'new-article',
            'content' => 'Content of article'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test  */
    public function title_must_be_at_least_4_characters(): void
    {
        $this->postJson(route('api.v1.articles.store'),[
            'title' => 'Nue',
            'slug' => 'new-article',
            'content' => 'Content of article'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'content' => 'Content of article'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique(): void
    {
        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => $article->slug,
            'content' => 'Content of article'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_and_dashes(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => '&%$#"',
            'content' => 'Content of article'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'with_underscores',
            'content' => 'Content of article'
        ])->assertSee(trans('validation.no_underscores', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => '-start-with-dashes',
            'content' => 'Content of article'
        ])->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'end-with-dashes-',
            'content' => 'Content of article'
        ])->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'data.attributes.slug']))
            ->assertJsonApiValidationErrors('slug');
    }


    /** @test */
    public function content_is_required(): void
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'New article',
            'slug' => 'new-article',
        ])->assertJsonApiValidationErrors('content');
    }
}
