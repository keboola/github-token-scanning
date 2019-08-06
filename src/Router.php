<?php

declare(strict_types=1);

namespace Keboola\GithubTokenScanning;

use GuzzleHttp\Psr7\Request;
use HttpException;

class Router
{
    public function __invoke($event)
    {
        $serviceName = Utils::getEnv('SERVICE_NAME');
        $region = Utils::getEnv('REGION');
        $logger = new Logger($serviceName);
        $app = new App($region, $logger);

        if (empty($event['httpMethod']) || empty($event['resource'])) {
            throw new \Exception('Bad Request', 400);
        }

        $request = new Request($event, $event['resource'], $event['headers'], $event['body']);

        if (empty($resource[$event['httpMethod']])) {
            throw new \Exception('Method Not Allowed', 405);
        }

        try {
            return $app->handle($request);
        } catch (HttpException $e) {
            return ['body' => $e->getMessage(), 'statusCode' => $e->getCode()];
        } catch (\Throwable $e) {
            return ['body' => 'Server error', 'statusCode' => 500];
        }
    }
}
