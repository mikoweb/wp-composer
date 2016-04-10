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
 * @subpackage WordPress
 */
class InitApp
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Application
     */
    private $app;

    /**
     * @param string $rootPath
     * 
     * @return Application
     */
    public function init($rootPath)
    {
        require_once($rootPath . '/wp-load.php');
        wp();

        $app = $this->getApp($rootPath);
        $app->setTwigGlobals($this->wpGlobals());
        Twig::setContainer($this->getContainer($rootPath));

        if ($this->canAppRun($app)) {
            $app->run();
        } else {
            echo $this->wpTemplateData();
        }
    }

    /**
     * @param string $rootPath
     *
     * @return ContainerBuilder
     */
    private function getContainer($rootPath)
    {
        if (!$this->container) {
            try {
                $config = Yaml::parse(file_get_contents($rootPath . '/../config/config.yml'));
                $this->container = new ContainerBuilder();
                $extension = new CoreExtension();
                $extension->load($config, $this->container);
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }

        return $this->container;
    }

    /**
     * @param string $rootPath
     *
     * @return Application
     */
    private function getApp($rootPath)
    {
        if (!$this->app) {
            $this->app = new Application($this->getContainer($rootPath));
        }

        return $this->app;
    }

    /**
     * @param Application $app
     *
     * @return bool
     */
    private function canAppRun(Application $app)
    {
        $requestContext = $app['request_context'];
        $requestContext->fromRequest(Request::createFromGlobals());
        $urlMatcher = new UrlMatcher($app['routes'], $requestContext);

        try {
            $urlMatcher->match($requestContext->getPathInfo());
            $appRun = true;
        } catch (ResourceNotFoundException $e) {
            $appRun = false;
        }

        return $appRun;
    }

    /**
     * @return array
     */
    private function wpTemplateData()
    {
        ob_start();
        require(ABSPATH . WPINC . '/template-loader.php');
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * @return array
     */
    private function wpGlobals()
    {
        global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

        if (is_array( $wp_query->query_vars)) {
            extract( $wp_query->query_vars, EXTR_SKIP );
        }

        return get_defined_vars();
    }
}
