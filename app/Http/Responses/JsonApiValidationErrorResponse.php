<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class JsonApiValidationErrorResponse extends JsonResponse
{

    /**
     * @param ValidationException $exception
     * @param int $status
     */
    public function __construct(ValidationException $exception, int $status = 422)
    {
        
        $data = $this->formatJsonApiErrors($exception);

        $headers = [
            'content-type' => 'application/vnd.api+json'
        ];

        parent::__construct($data, $status, $headers);
    }

    /**
     * @param ValidationException $exception
     * @return array
     */
    public function formatJsonApiErrors(ValidationException $exception): array
    {
        $title = $exception->getMessage();

        return [
            'errors' => collect($exception->errors())
                ->map(function ($message, $field) use ($title) {
                    return [
                        'title' => $title,
                        'detail' => $message[0],
                        'source' => [
                            'pointer' => "/" . str_replace('.', '/', $field)
                        ]
                    ];
                })->values()
        ];
    }
}
