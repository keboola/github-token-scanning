<?php

declare(strict_types=1);

namespace Keboola\GithubTokenScanning\Request;

use GuzzleHttp\Client;
use Keboola\GithubTokenScanning\GithubRequestValidatorInterface;
use Psr\Http\Message\RequestInterface;

class GithubRequestValidator implements GithubRequestValidatorInterface
{
    const PUBLIC_KEY_URI = 'https://api.github.com/meta/public_keys/token_scanning';

    public function validate(RequestInterface $request): bool
    {
        $keyIdentifier = array_pop($request->getHeader('GITHUB-PUBLIC-KEY-IDENTIFIER'));
        $signature = array_pop($request->getHeader('GITHUB-PUBLIC-KEY-SIGNATURE'));
        $payload = $request->getBody()->getContents();

        // get known public keys
        $validKeysJson = (new Client())->get(self::PUBLIC_KEY_URI)->getBody();
        $keys = \GuzzleHttp\json_decode($validKeysJson, true);
        // find key matching key
        $validKey = current(
            array_filter(
                $keys['public_keys'],
                function ($key) use ($keyIdentifier) {
                    $isCorrect = $key['key_identifier'] === $keyIdentifier;
                    $isCurrent = $key['is_current'] === true;
                    return $isCorrect && $isCurrent;
                }
            )
        );

        if (!$validKey) {
            throw new \Exception(sprintf('Cannot find github signing key "%s"', $keyIdentifier), 400);
        }

        $validationResult = openssl_verify(
            $payload,
            base64_decode($signature),
            $validKey['key'],
            OPENSSL_ALGO_SHA256
        );

        if ($validationResult === 0) {
            throw new \Exception(sprintf(
                'Signature "%s" does not match',
                $signature,
            ), 400);
        }
        if ($validationResult === -1) {
            throw new \Exception(sprintf(
                'Error decoding the signature, openssl error string: "%s"',
                openssl_error_string()
            ), 400);
        }

        return true;
    }
}
