<?php

declare(strict_types=1);

namespace Keboola\GithubTokenScanning;

use Psr\Http\Message\RequestInterface;

interface GithubRequestValidatorInterface
{
    public function validate(RequestInterface $request): bool;
}
