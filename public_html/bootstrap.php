<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Core\Application;
use Core\CoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Yaml\Yaml;

try {
    $config = Yaml::parse(file_get_contents(__DIR__ . '/../config/config.yml'));
    $container = new ContainerBuilder();
    $extension = new CoreExtension();
    $extension->load($config, $container);
} catch (\Exception $e) {
    die($e->getMessage());
}

$app = new Application($container);
$requestContext = $app['request_context'];
$requestContext->fromRequest(Request::createFromGlobals());
$urlMatcher = new UrlMatcher($app['routes'], $requestContext);

try {
    $urlParameters = $urlMatcher->match($requestContext->getPathInfo());
    $appRun = true;
} catch (ResourceNotFoundException $e) {
    $appRun = false;
}

ob_start();
require_once(__DIR__ . '/index.php');

if ($appRun) {
    ob_clean();
    $app->run();
}
