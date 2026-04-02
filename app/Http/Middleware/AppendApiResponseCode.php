<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppendApiResponseCode
{
    /**
     * Append numeric HTTP status code into JSON body as `code`.
     * Only applies to top-level JSON objects (associative arrays).
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$request->is('api/*')) {
            return $response;
        }

        // 1) Standard JsonResponse
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            if ($this->shouldAppendCode($data) && !array_key_exists('code', $data)) {
                $data['code'] = $response->getStatusCode();
                $response->setData($data);
            }
            return $response;
        }

        // 2) Any other response that already returns JSON
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

        if ($this->shouldAppendCode($decoded) && !array_key_exists('code', $decoded)) {
            $decoded['code'] = $response->getStatusCode();
            $response->setContent(json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }

        return $response;
    }

    /**
     * @param mixed $data
     */
    private function shouldAppendCode($data): bool
    {
        if (!is_array($data) || array_is_list($data)) {
            return false;
        }

        // Only append for our conventional API envelopes.
        return array_key_exists('status', $data) || array_key_exists('message', $data);
    }
}
