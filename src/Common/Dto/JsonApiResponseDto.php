<?php

declare(strict_types=1);

namespace App\Common\Dto;

use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonApiResponseDto extends JsonResponse
{
    /**
     * @param mixed                $data
     * @param string               $message
     * @param int                  $status
     * @param array<string, mixed> $headers
     *
     * @return self
     */
    public static function success($data = null, string $message = '', int $status = JsonResponse::HTTP_OK, array $headers = []): self
    {
        $response = ['data' => $data];

        if ($message) {
            $response['message'] = $message;
        }

        return new self(['success' => $response], $status, $headers);
    }

    /**
     * @param string               $message
     * @param int                  $status
     * @param array<string, mixed> $headers
     *
     * @return self
     */
    public static function error(string $message, int $status = JsonResponse::HTTP_BAD_REQUEST, array $headers = []): self
    {
        return new self(['error' => ['message' => $message]], $status, $headers);
    }
}
