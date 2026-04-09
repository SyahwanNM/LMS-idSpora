<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizeApiEnvelope
{
    /**
     * Ensure consistent JSON envelope for API responses.
     *
     * Adds missing keys for conventional API payloads:
     * - status: 'success' | 'error'
     * - message: string|null
     * - data: mixed|null
     * - errors: mixed|null
     * - pagination: mixed|null
     *
     * Only applies to top-level JSON objects (associative arrays) that already contain
     * at least one of: `status`, `message`, `data`, `errors`, `pagination`.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$request->is('api/*')) {
            return $response;
        }

        // Standard JsonResponse
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $normalized = $this->normalize($data, $response->getStatusCode());
            if ($normalized !== null) {
                $response->setData($normalized);
            }
            return $response;
        }

        // Other response types that are JSON
        $contentType = (string) ($response->headers->get('Content-Type') ?? '');
        if (!str_contains(strtolower($contentType), 'application/json')) {
            return $response;
        }

        $content = (string) $response->getContent();
        if ($content === '') {
            return $response;
        }

        $decoded = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $response;
        }

        $normalized = $this->normalize($decoded, $response->getStatusCode());
        if ($normalized !== null) {
            $response->setContent(json_encode($normalized, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        return $response;
    }

    /**
     * @param mixed $data
     * @return array<string,mixed>|null
     */
    private function normalize($data, int $statusCode): ?array
    {
        if (!is_array($data) || array_is_list($data)) {
            return null;
        }

        $hasEnvelopeKey = array_key_exists('status', $data)
            || array_key_exists('message', $data)
            || array_key_exists('data', $data)
            || array_key_exists('errors', $data)
            || array_key_exists('pagination', $data);

        if (!$hasEnvelopeKey) {
            return null;
        }

        if (!array_key_exists('status', $data)) {
            $data['status'] = $statusCode >= 400 ? 'error' : 'success';
        }

        if (!array_key_exists('message', $data)) {
            $data['message'] = null;
        }

        if (!array_key_exists('data', $data)) {
            $data['data'] = null;
        }

        if (!array_key_exists('errors', $data)) {
            $data['errors'] = null;
        }

        if (!array_key_exists('pagination', $data)) {
            $data['pagination'] = null;
        }

        return $data;
    }
}
