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

namespace Core;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Dominikzogg\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Silex;
use Sorien\Provider\DoctrineProfilerServiceProvider;
use Saxulum\Console\Silex\Provider\ConsoleProvider;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package WordPress Silex
 */
class Application extends Silex\Application
{
    use Silex\Application\UrlGeneratorTrait;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    private $dirCache;

    /**
     * @var string
     */
    private $dirConfig;

    /**
     * @var string
     */
    private $dirWeb;

    /**
     * @var string
     */
    private $dirView;

    /**
     * @var string
     */
    private $dirEntity;

    /**
     * @param ContainerBuilder $container
     * @param bool console
     */
    public function __construct(ContainerBuilder $container, $console = false)
    {
        try {
            parent::__construct();

            $dirRoot = __DIR__ . '/../..';
            $this->container = $container;
            $this['debug'] = $container->getParameter('core.debug');
            $this->dirCache = $dirRoot . '/cache';
            $this->dirConfig = $dirRoot . '/config';
            $this->dirView = $dirRoot . '/views';
            $this->dirWeb = $dirRoot . '/public_html/wp-content/themes';
            $this->dirEntity = $dirRoot . '/src/App/Entity';

            $this->container->set('silex', $this);

            if ($console) {
                $this->register(new ConsoleProvider(), [
                    'console.cache' => $this->getDirCache() . '/console',
                ]);
            }

            $this->register(new Silex\Provider\HttpFragmentServiceProvider());
            $this->register(new Silex\Provider\ServiceControllerServiceProvider());
            $this->register(new Silex\Provider\TwigServiceProvider());
            $this->register(new Silex\Provider\UrlGeneratorServiceProvider());
            $this->register(new Silex\Provider\SessionServiceProvider());
            $this->register(new Silex\Provider\ValidatorServiceProvider());

            $this->container->set('validator', $this['validator']);
            $this->container->set('session', $this['session']);

            $this->createDoctrine();
            $this->createTwig();
            $this->createRouting();

            if ($this['debug']) {
                error_reporting(E_ALL | E_STRICT);
                ini_set('display_errors', '1');

                $this->register(new Silex\Provider\WebProfilerServiceProvider(), array(
                    'profiler.cache_dir' => $this->getDirCache() . '/profiler',
                ));
                $this->register(new DoctrineProfilerServiceProvider());
            }
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getDirCache()
    {
        return $this->dirCache;
    }

    /**
     * @return string
     */
    public function getDirConfig()
    {
        return $this->dirConfig;
    }

    /**
     * @return string
     */
    public function getDirWeb()
    {
        return $this->dirWeb;
    }

    /**
     * @return string
     */
    public function getDirView()
    {
        return $this->dirView;
    }

    /**
     * @return string
     */
    public function getDirEntity()
    {
        return $this->dirEntity;
    }

    /**
     * @param array $variables
     */
    public function setTwigGlobals(array $variables)
    {
        $twig = $this->container->get('twig');
        foreach ($variables as $name=>$val) {
            $twig->addGlobal($name, $val);
        }
    }

    private function createTwig()
    {
        $this['request'] = Request::createFromGlobals();
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');
        /** @var \Twig_Loader_Filesystem $twigLoader */
        $twigLoader = $this->container->get('twig_loader');

        $templateDir = get_template_directory() . '/views';
        if (file_exists($templateDir . '/override')) {
            $twigLoader->addPath($templateDir . '/override');
        }
        $twigLoader->addPath($this->getDirView());
        $twigLoader->addPath($this->getDirView(), 'root');
        if (file_exists($templateDir)) {
            $twigLoader->addPath($templateDir, 'theme');
        }
        $twigLoader->addPath($this->getDirView() . '/theme', 'theme');

        try {
            $taggedServices = $this->container->findTaggedServiceIds('twig.extension');
            foreach ($taggedServices as $id => $attr) {
                $twig->addExtension($this->container->get($id));
            }
        } catch (\Exception $e) {
            die("Load twig extensions error: " . $e->getMessage());
        }

        $twig->setCache($this->getDirCache() . '/twig');

        if ($this['debug']) {
            $twig->enableDebug();
            $twig->enableAutoReload();
        } else {
            $twig->disableDebug();
            $twig->disableAutoReload();
        }
    }

    private function createRouting()
    {
        $locator = new FileLocator(array($this->getDirConfig()));
        $loader = new YamlFileLoader($locator);
        $this['routes']->addCollection($loader->load($this->getDirConfig() . "/routing.yml"));

        if ($this['debug']) {
            $xmlLoader = new XmlFileLoader($locator);
            $this['routes']->addCollection($xmlLoader->load($this->getDirConfig() . "/web_profiler.xml"));
        }

        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');
        $context = $this['request_context'];
        $context->fromRequest(Request::createFromGlobals());
        unset($this["url_generator"]);
        $this["url_generator"] = new UrlGenerator($this['routes'], $context);
        $twig->addExtension(new RoutingExtension($this["url_generator"]));

        $container = $this->container;
        $this->on(HttpKernel\KernelEvents::CONTROLLER, function (HttpKernel\Event\FilterControllerEvent $event) use($container) {
            $controller = $event->getController();

            if ($controller[0] instanceof ContainerAwareInterface) {
                $controller[0]->setContainer($container);
            }
        });

        $this->container->set('url_generator', $this['url_generator']);
    }

    private function createDoctrine()
    {
        $this->register(new Silex\Provider\DoctrineServiceProvider(), array(
            'db.options' => [
                'host'     => $this->container->getParameter('core.database.host'),
                'driver'   => $this->container->getParameter('core.database.driver'),
                'user'     => $this->container->getParameter('core.database.user'),
                'password' => $this->container->getParameter('core.database.password'),
                'dbname'   => $this->container->getParameter('core.database.dbname'),
            ],
        ));

        $this->register(new DoctrineOrmServiceProvider, array(
            'orm.proxies_dir' => $this->getDirCache() . '/doctrine/proxies',
            'orm.em.options' =>[
                'mappings' => [
                    [
                        'type' => 'annotation',
                        'namespace' => 'App\Entity',
                        'path' => $this->getDirEntity(),
                        'use_simple_annotation_reader' => false,
                    ]
                ],
            ],
        ));

        $this->register(new DoctrineOrmManagerRegistryProvider());

        $this->container->set('doctrine', $this['doctrine']);
    }
}
