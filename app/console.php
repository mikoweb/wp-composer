<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Application;
use Core\CoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

try {
    $config = Yaml::parse(file_get_contents(__DIR__ . '/../config/config.yml'));
    $container = new ContainerBuilder();
    $extension = new CoreExtension();
    $extension->load($config, $container);
} catch (\Exception $e) {
    die($e->getMessage());
}

$container->setParameter('core.debug', true);
$app = new Application($container, true);
$app['console']->run();
