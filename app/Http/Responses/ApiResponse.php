<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiResponse
{
    public static function success(string $msg = 'Success', $data = null, int $code = 200): JsonResponse
    {
        if ($data instanceof AnonymousResourceCollection) {
            $paginator = $data->resource;

            if ($paginator instanceof LengthAwarePaginator || $paginator instanceof Paginator) {
                $paginationData = $paginator->toArray();

                return response()->json(array_merge(
                    [
                        'success' => true,
                        'message' => $msg,
                        'data' => $data->collection,
                    ],
                    $paginationData
                ), $code);
            }
        }

        if ($data instanceof LengthAwarePaginator || $data instanceof Paginator) {
            $paginationData = $data->toArray();

            return response()->json(array_merge(
                [
                    'success' => true,
                    'message' => $msg,
                    'data' => $data->items(),
                ],
                $paginationData
            ), $code);
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $data,
        ], $code);
    }

    public static function error(string $msg = 'Error', $errors = [], int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
            'data' => null,
            'errors' => $errors,
        ], $code);
    }

    public static function notFound(string $message = 'Not Found'): JsonResponse
    {
        return self::error($message, [], 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error($message, [], 401);
    }

    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error($message, [], 403);
    }
    public static function validationError(string $message = 'Validation Error', $errors = []): JsonResponse
    {
        return self::error($message, $errors, 422);
    }
}
