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

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package WordPress Silex
 */
abstract class Controller implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        if (is_null($container)) {
            throw new \RuntimeException('You can not set the null');
        }

        $this->container = $container;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->container->get('silex')['session'];
    }

    /**
     * @return ManagerRegistry
     */
    public function getDoctrine()
    {
        return $this->container->get('silex')['doctrine'];
    }

    /**
     * @return RecursiveValidator
     */
    public function getValidator()
    {
        return $this->container->get('validator');
    }

    /**
     * @param string $type
     * @param string $message
     */
    public function addFlash($type, $message)
    {
        $this->getSession()->getFlashBag()->add($type, $message);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param Response|null $response
     *
     * @return Response
     */
    public function render($view, array $parameters = [], Response $response = null)
    {
        if (is_null($response)) {
            $response = new Response();
        }

        $response->setContent($this->container->get('twig')->render($view, $parameters));

        return $response;
    }

    /**
     * @param $name
     * @param array $parameters
     * @param int $referenceType
     *
     * @return string
     */
    public function generateUrl($name, array $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        return $this->container->get('silex')['url_generator']->generate($name, $parameters, $referenceType);
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302, array $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * @param $route
     * @param array $parameters
     * @param int $referenceType
     * @param int $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    public function redirectToRoute(
        $route,
        array $parameters = [],
        $referenceType = UrlGenerator::ABSOLUTE_PATH,
        $status = 302,
        array $headers = []
    ) {
        return $this->redirect($this->generateUrl(
            $route, $parameters, $referenceType), $status, $headers);
    }
}
