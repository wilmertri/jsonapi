<?php

namespace Tests;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;

trait MakesJsonApiRequest
{
    protected bool $formatJsonApiDocument = true;

    /**
     * @param $uri
     * @param array $data
     * @return array
     */
    public function getFormattedData($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string)Str::of($path)->after('api/v1/')->before('/');
        $id = (string)Str::of($uri)->after($type)->replace("/", "");
        return [
            'data' => array_filter([
                'type' => $type,
                'id' => $id,
                'attributes' => $data
            ])
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        TestResponse::macro(
            'assertJsonApiValidationErrors',
            $this->assertJsonApiValidationErrors()
        );
    }
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['accept'] = 'application/vnd.api+json';

        if ($this->formatJsonApiDocument)
        {
            $formattedData = $this->getFormattedData($uri, $data);
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';

        return parent::postJson($uri, $data, $headers, $options);
    }

    public function patchJson($uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $headers['content-type'] = 'application/vnd.api+json';
        return parent::patchJson($uri, $data, $headers, $options);
    }

    public function withoutJsonApiDocumentFormmating()
    {
        $this->formatJsonApiDocument = false;
    }

    /**
     * @return \Closure
     */
    protected function assertJsonApiValidationErrors(): \Closure
    {
        return function ($attribute) {
            /** @var TestResponse $this */

            $pointer = Str::of($attribute)->startsWith('data')
                ? "/" . str_replace('.', '/', $attribute)
                : "/data/attributes/{$attribute}";

            try {
                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer]
                ]);
            }catch (ExpectationFailedException $e){
                PHPUnit::fail(
                    "Failed to find a JSON:API validation error for key: '{$attribute}'"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }

            try {
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                    ]
                ]);
            }catch (ExpectationFailedException $e){
                PHPUnit::fail(
                    "Failed to find a valid JSON:API error response"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }



            $this->assertHeader(
                'content-type', 'application/vnd.api+json'
            )->assertStatus(422);
        };
    }
}
