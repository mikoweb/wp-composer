<?php

/*
 * This file is part of the WordPress Silex package.
 *
 * website: www.mikoweb.pl
 * (c) Rafał Mikołajun <rafal@mikoweb.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\WordPress;

use Core\Application;
use Core\CoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package WordPress Silex
 * @package WordPress
 */
class InitApp
{
    /**
     * @param string $rootPath
     * 
     * @return Application
     */
    public function init($rootPath)
    {
        try {
            $config = Yaml::parse(file_get_contents($rootPath . '/../config/config.yml'));
            $container = new ContainerBuilder();
            $extension = new CoreExtension();
            $extension->load($config, $container);
        } catch (\Exception $e) {
            die($e->getMessage());
        }

        require_once($rootPath . '/wp-load.php');
        wp();

        $app = new Application($container);
        $requestContext = $app['request_context'];
        $requestContext->fromRequest(Request::createFromGlobals());
        $urlMatcher = new UrlMatcher($app['routes'], $requestContext);

        try {
            $urlMatcher->match($requestContext->getPathInfo());
            $appRun = true;
        } catch (ResourceNotFoundException $e) {
            $appRun = false;
        }

        if ($appRun) {
            $app->run();
        } else {
            require_once(ABSPATH . WPINC . '/template-loader.php');
        }
    }
}
