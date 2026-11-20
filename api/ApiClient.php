<?php
declare(strict_types=1);

/**
 * Small HTTP client for the existing Laravel REST API.
 * Uses cURL when available and PHP streams as a fallback (no Composer needed).
 */
final class ApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly array $payload = []
    ) {
        parent::__construct($message, $statusCode);
    }
}

final class ApiClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout = 20
    ) {}

    public function request(
        string $method,
        string $path,
        array $data = [],
        ?string $token = null,
        bool $multipart = false
    ): array {
        $method = strtoupper($method);
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');
        $headers = [
            'Accept: application/json',
            'X-Requested-With: XMLHttpRequest',
        ];
        if ($token !== null && $token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $body = null;
        $useCurl = function_exists('curl_init');
        if ($multipart) {
            if (!$useCurl) {
                // PHP streams need a manually encoded multipart body.
                [$body, $contentType] = $this->encodeMultipart($data);
                $headers[] = 'Content-Type: ' . $contentType;
            }
            // With cURL, let libcurl create the multipart boundary from CURLFile.
            // Setting a manually generated Content-Type here would mismatch its boundary.
        } elseif (!in_array($method, ['GET', 'HEAD'], true)) {
            $body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($body === false) {
                throw new ApiException('Data request tidak dapat diubah menjadi JSON.');
            }
            $headers[] = 'Content-Type: application/json';
        }

        if ($method === 'GET' && $data !== []) {
            $query = http_build_query($data);
            $url .= (str_contains($url, '?') ? '&' : '?') . $query;
        }

        if ($useCurl) {
            [$status, $raw] = $this->sendCurl($method, $url, $headers, $body, $multipart, $data);
        } else {
            [$status, $raw] = $this->sendStream($method, $url, $headers, $body);
        }

        $decoded = [];
        if (is_string($raw) && $raw !== '') {
            $parsed = json_decode($raw, true);
            if (is_array($parsed)) {
                $decoded = $parsed;
            }
        }

        if ($status < 200 || $status >= 300) {
            $message = $decoded['message'] ?? ('Laravel API mengembalikan HTTP ' . $status . '.');
            throw new ApiException((string) $message, $status, $decoded);
        }

        return $decoded;
    }

    private function sendCurl(string $method, string $url, array $headers, ?string $body, bool $multipart, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        if ($method !== 'GET' && $method !== 'HEAD') {
            if ($multipart) {
                $fields = $this->curlMultipartFields($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body ?? '');
            }
        }
        $raw = curl_exec($ch);
        if ($raw === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new ApiException('Tidak dapat menghubungi Laravel API: ' . $error);
        }
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$status, (string) $raw];
    }

    private function sendStream(string $method, string $url, array $headers, ?string $body): array
    {
        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'content' => $body ?? '',
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ];
        $context = stream_context_create($options);
        $raw = @file_get_contents($url, false, $context);
        $status = 0;
        foreach (($http_response_header ?? []) as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
                $status = (int) $matches[1];
            }
        }
        if ($raw === false && $status === 0) {
            $last = error_get_last();
            throw new ApiException('Tidak dapat menghubungi Laravel API. ' . ($last['message'] ?? 'Periksa alamat API dan server Laravel.'));
        }
        return [$status, (string) $raw];
    }

    private function encodeMultipart(array $data): array
    {
        $boundary = '----BarenginForm' . bin2hex(random_bytes(12));
        $body = '';
        foreach ($data as $name => $value) {
            if (is_array($value) && isset($value['tmp_name'], $value['error'])) {
                if ((int) $value['error'] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                if ((int) $value['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($value['tmp_name'])) {
                    throw new ApiException('File upload tidak valid.');
                }
                $filename = basename((string) ($value['name'] ?? 'upload.bin'));
                $mime = (string) ($value['type'] ?? 'application/octet-stream');
                $content = file_get_contents($value['tmp_name']);
                if ($content === false) {
                    throw new ApiException('File upload tidak dapat dibaca.');
                }
                $body .= "--{$boundary}\r\n";
                $body .= 'Content-Disposition: form-data; name="' . addslashes((string) $name) . '"; filename="' . addslashes($filename) . "\"\r\n";
                $body .= 'Content-Type: ' . $mime . "\r\n\r\n" . $content . "\r\n";
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $index => $item) {
                    $field = $name . '[' . $index . ']';
                    $body .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"{$field}\"\r\n\r\n" . (string) $item . "\r\n";
                }
                continue;
            }
            $body .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"{$name}\"\r\n\r\n" . (string) $value . "\r\n";
        }
        $body .= "--{$boundary}--\r\n";
        return [$body, 'multipart/form-data; boundary=' . $boundary];
    }

    private function curlMultipartFields(array $data): array
    {
        $fields = [];
        foreach ($data as $name => $value) {
            if (is_array($value) && isset($value['tmp_name'], $value['error'])) {
                if ((int) $value['error'] === UPLOAD_ERR_NO_FILE) continue;
                if ((int) $value['error'] !== UPLOAD_ERR_OK) throw new ApiException('File upload tidak valid.');
                $fields[$name] = new CURLFile($value['tmp_name'], $value['type'] ?: 'application/octet-stream', basename($value['name']));
            } else {
                $fields[$name] = $value;
            }
        }
        return $fields;
    }
}
