<?php


require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/{readerId}/recommendations', function ($readerId) use($app) {
    return $app->json([]);
});

$app->run();
