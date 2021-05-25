<?php
require __DIR__ . '/../vendor/autoload.php';
$settings = require(__DIR__ . "/../app/config/settings.php");
$app = new Slim\App($settings);
$container = $app->getContainer();
$dbSettings = $container->get('settings')['db'];
$capsule = new Illuminate\Database\Capsule\Manager;
$capsule->addConnection($dbSettings);
$capsule->setAsGlobal();
$capsule->bootEloquent();
require __DIR__ . '/../app/app_loader.php';
$app->run();
