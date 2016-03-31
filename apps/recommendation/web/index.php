<?php


use Neoxygen\NeoClient\ClientBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app['neo4j'] = function () {
    return ClientBuilder::create()
        ->addDefaultLocalConnection()
        ->build();
};

$app->get('/{readerId}/recommendations', function ($readerId) use ($app) {
    return $app->json([]);
});


$app->get('/{readerId}/read/{bookId}', function ($readerId, $bookId) use ($app) {

    return $app->json([]);
});

$app->run();
