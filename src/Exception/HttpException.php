<?php

declare(strict_types=1);

namespace Keboola\GithubTokenScanning\Exception;

use Exception;

class HttpException extends Exception
{
    public static function notFound(): self
    {
        return new self('Not found', 404);
    }

    public static function badRequest() :self
    {
        return new self('Bad request', 400);
    }
    public static function methodNotAllowed() :self
    {
        return new self('Method Not Allowed', 405);
    }
}
