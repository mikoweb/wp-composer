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

namespace App\Controller;

use Core\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Rafał Mikołajun <rafal@mikoweb.pl>
 * @package WordPress Silex
 */
class HelloWorldController extends Controller
{
    /**
     * @param string $name
     * 
     * @return Response
     */
    public function indexAction($name)
    {
        return $this->render('HelloWorld/index.html.twig', [
            'name' => $name,
        ]);
    }
}
