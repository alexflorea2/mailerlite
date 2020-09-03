<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../defines.php';
require __DIR__ . '/../global_helpers.php';

use Api\Middleware\ValidateSubscriberDoesNotHaveField;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\{Request, JsonResponse, Response};
use DI\ContainerBuilder;
use Api\Libraries\MySQL;
use Api\Repositories\{Fields,
    FieldsRepositoryInterface,
    SubscriberFields,
    SubscriberFieldsRepositoryInterface,
    Subscribers,
    SubscribersRepositoryInterface};

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env');

if (isset($_ENV['DB_HOST']) && $_ENV['DB_HOST'] === ENV_DEVELOPMENT) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

$mysql = new MySQL(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_NAME'],
    $_ENV['DB_PORT']
);

/*Routing*/
$dispatcher = FastRoute\simpleDispatcher(
    function (FastRoute\RouteCollector $r) {
        $r->addRoute(
            'GET',
            '/subscribers',
            [
                [],
                Api\Controllers\SubscribersController::class,
                'index',
            ]
        );

        $r->addRoute(
            'GET',
            '/subscribers/{id:\d+}',
            [
                [],
                Api\Controllers\SubscribersController::class,
                'find',
            ]
        );

        $r->addRoute(
            'GET',
            '/subscribers/{id:\d+}/fields',
            [
                [],
                Api\Controllers\SubscribersController::class,
                'getFields',
            ]
        );

        $r->addRoute(
            'POST',
            '/subscribers/{id:\d+}/fields',
            [
                [ValidateSubscriberDoesNotHaveField::class],
                Api\Controllers\SubscribersController::class,
                'addField',
            ]
        );

        $r->addRoute(
            'POST',
            '/subscribers',
            [
                [
                    Api\Middleware\ValidateEmail::class,
                    Api\Middleware\ValidateIsNewSubscriber::class,
                ],
                Api\Controllers\SubscribersController::class,
                'add',
            ]
        );

        $r->addRoute(
            'PATCH',
            '/subscribers/{id:\d+}/state',
            [
                [
                    Api\Middleware\ValidateState::class,
                ],
                Api\Controllers\SubscribersController::class,
                'updateState',
            ]
        );

        $r->addRoute(
            'DELETE',
            '/subscribers/{id:\d+}',
            [
                [],
                Api\Controllers\SubscribersController::class,
                'delete',
            ]
        );

        //-------------
        $r->addRoute(
            'GET',
            '/fields',
            [
                [],
                Api\Controllers\FieldsController::class,
                'index',
            ]
        );

        $r->addRoute(
            'POST',
            '/fields',
            [
                [],
                Api\Controllers\FieldsController::class,
                'add',
            ]
        );
    }
);

$request = Request::createFromGlobals();

$builder = new ContainerBuilder();
$builder->addDefinitions(
    [
        Request::class => function () use ($request) {
            return $request;
        },
        MySQL::class => function () use ($mysql) {
            return $mysql;
        },
        FieldsRepositoryInterface::class => DI\get(Fields::class),
        SubscribersRepositoryInterface::class => DI\get(Subscribers::class),
        SubscriberFieldsRepositoryInterface::class => DI\get(SubscriberFields::class),
    ]
);
$container = $builder->build();

$httpMethod = $request->getMethod();

// Force override for verbs that are not official in html spec
$_method = $request->get('_method');
if($_method)
{
    $httpMethod = $_method;
}

$uri = $request->getPathInfo();
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// Fake options for PHP built in webserver and axios preflight request
if( $httpMethod === 'OPTIONS' )
{
    sendResponse(new Response());
}

try {
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            sendResponse(new JsonResponse([], JsonResponse::HTTP_NOT_FOUND));
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            sendResponse(new JsonResponse([], JsonResponse::HTTP_METHOD_NOT_ALLOWED));
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            $response = '';
            $i = 0;
            if (!empty($handler[0])) {
                do {
                    $response = $container->get($handler[0][$i]);
                    $response = $response->handle(...array_values($vars));

                    $i++;
                } while (
                    (!is_object($response) || get_class($response) != JsonResponse::class)
                    && $i != count($handler[0])
                );

                if (is_object($response) && get_class($response) === JsonResponse::class) {
                    sendResponse($response);
                }
            }
            $controller = $container->get($handler[1]);
            $response = $controller->{$handler[2]}(...array_values($vars));
            sendResponse($response);
            // ... call $handler with $vars
            break;
    }
} catch (Exception $e) {
    sendResponse(new JsonResponse(['error'=>$e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR));
}
