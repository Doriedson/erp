<?php
namespace App\Http;

use App\Support\Version;
use App\Support\Notifier; // mantém uso atual da sua Notifier

final class Response
{
    public static function json($payload, int $status = 200, bool $envelope = true): string
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

		$out = $envelope
				? [
					'data' => $payload,
					'messages' => Notifier::getMessages(),
					'version' => Version::get()
				]
				: $payload;

        return json_encode($out, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public static function error(string $message, int $status = 400, $code = null, bool $envelope = true): string
    {
        $body = ['error' => $message];
        if ($code !== null) $body['code'] = $code;
        return self::json($body, $status, $envelope);
    }
}