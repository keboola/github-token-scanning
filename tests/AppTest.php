<?php

declare(strict_types=1);

namespace Keboola\GithubTokenScanning\Tests;

use GuzzleHttp\Psr7\Request;
use Keboola\GithubTokenScanning\App;
use Keboola\GithubTokenScanning\Request\GithubRequestValidator;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testHandle(): void
    {
        $app = new App(new GithubRequestValidator());
        $headers = [
            'Accept' => '*/*',
            'User-Agent' => 'curl/7.54.0',
            'Content-Type' => 'application/json',
            'GITHUB-PUBLIC-KEY-IDENTIFIER' => '90a421169f0a406205f1563a953312f0be898d3c7b6c06b681aa86a874555f4a',
            'GITHUB-PUBLIC-KEY-SIGNATURE' => 'MEQCIAfgjgz6Ou/3DXMYZBervz1TKCHFsvwMcbuJhNZse622AiAG86/cku2XdcmFWNHl2WSJi2fkE8t+auvB24eURaOd2A==',
        ];
        $body = '[{"type":"github_oauth_token","token":"cb4985f91f740272c0234202299f43808034d7f5","url":" https://github.com/github/faketestrepo/blob/b0dd59c0b500650cacd4551ca5989a6194001b10/production.env"}]';
        $request = new Request('POST', '/github-token-scanning/notify', $headers, $body);

        $app->handle($request);
    }
}
