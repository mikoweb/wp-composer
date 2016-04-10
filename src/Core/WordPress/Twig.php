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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package WordPress Silex
 * @subpackage WordPress
 */
class Twig
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * @param string $name
     * @param array $context
     *
     * @return string
     */
    public static function render($name, array $context = [])
    {
        return self::$container->get('twig')->render($name, $context);
    }
}
