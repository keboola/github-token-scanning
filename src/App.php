<?php

declare(strict_types=1);

namespace Keboola\GithubTokenScanning;

use GuzzleHttp\Client;
use Keboola\GithubTokenScanning\Exception\HttpException;
use Psr\Http\Message\RequestInterface;

class App
{
    /** @var GithubRequestValidatorInterface */
    private $requestValidator;

    public function __construct(
        GithubRequestValidatorInterface $requestValidator
    ) {
        $this->requestValidator = $requestValidator;
    }
    public function handle(RequestInterface $request): array
    {
        if ($request->getUri()->getPath() !== '/github-token-scanning/notify') {
            throw HttpException::notFound();
        }

        if ($request->getMethod() !== 'POST') {
            throw HttpException::methodNotAllowed();
        }

        if (!$this->requestValidator->validate($request)) {
            throw HttpException::badRequest();
        }

        return $this->notify(\GuzzleHttp\json_decode($request->getBody(), true));
    }

    private function notify($data): array
    {

    }
}
