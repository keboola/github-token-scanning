<?php
// phpcs:ignoreFile
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Keboola\JobQueueApi\App;
use Keboola\JobQueueApi\Logger;
use Keboola\JobQueueApi\Utils;

lambda(function ($event) {
    $serviceName = Utils::getEnv('SERVICE_NAME');
    $region = Utils::getEnv('REGION');
    $logger = new Logger($serviceName);
    $app = new App($region, $logger);

    $resourceMap = [
        '/jobs' => [
            'GET' => function ($event) use ($app): array {
                return [
                    'statusCode' => 200,
                    'body' => json_encode($app->getJobs($event)),
                ];
            },
            'POST' => function ($event) use ($app): array {
                return [
                    'statusCode' => 201,
                    'body' => json_encode($app->createJob($event)),
                ];
            },
        ],
        '/jobs/{id}' => [
            'GET' => function ($event) use ($app): array {
                return [
                    'statusCode' => 200,
                    'body' => json_encode($app->getJob($event)),
                ];
            },
        ],
        '/jobs/{id}/kill' => [
            'GET' => function ($event) use ($app): array {
                return [
                    'statusCode' => 200,
                    'body' => json_encode($app->killJob($event)),
                ];
            },
        ],
    ];

    if (empty($event['httpMethod']) || empty($event['resource'])) {
        throw new \Exception('Bad Request', 400);
    }

    if (empty($resourceMap[$event['resource']])) {
        throw new \Exception('Route Not Found', 404);
    }
    $resource = $resourceMap[$event['resource']];

    if (empty($resource[$event['httpMethod']])) {
        throw new \Exception('Method Not Allowed', 405);
    }

    $actionFn = $resource[$event['httpMethod']];

    return $actionFn($event);
});
