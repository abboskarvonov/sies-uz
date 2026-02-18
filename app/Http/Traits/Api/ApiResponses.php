<?php

namespace App\Http\Traits\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

trait ApiResponses
{
    protected function successResponse($data, int $status = 200, array $meta = []): JsonResponse
    {
        $response = ['data' => $data];

        $meta['locale'] = app()->getLocale();

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    protected function errorResponse(string $code, string $message, int $status, array $details = []): JsonResponse
    {
        $response = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'meta' => [
                'timestamp' => now()->toIso8601String(),
            ],
        ];

        if (!empty($details)) {
            $response['error']['details'] = $details;
        }

        return response()->json($response, $status);
    }

    protected function paginatedResponse(AnonymousResourceCollection $collection): JsonResponse
    {
        $paginator = $collection->resource;

        return response()->json([
            'data' => $collection->resolve(),
            'meta' => [
                'locale' => app()->getLocale(),
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'next' => $paginator->nextPageUrl(),
                'prev' => $paginator->previousPageUrl(),
                'last' => $paginator->url($paginator->lastPage()),
            ],
        ]);
    }

    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse('NOT_FOUND', $message, 404);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse('UNAUTHORIZED', $message, 401);
    }

    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse('FORBIDDEN', $message, 403);
    }
}
