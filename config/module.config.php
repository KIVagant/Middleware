<?php

/*
 * Murilo Amaral (http://muriloamaral.com)
 * Édipo Rebouças (http://edipo.com.br).
 *
 * @link https://github.com/muriloacs/Middleware
 *
 * @copyright Copyright (c) 2015 Murilo Amaral
 * @license The MIT License (MIT)
 *
 * @since File available since Release 1.0
 */

namespace Middleware;

return array(
    'service_manager' => array(
        'factories' => array(
            'MiddlewareRunnerService' => __NAMESPACE__.'\Service\Factory\MiddlewareRunnerServiceFactory',
        ),
    ),
    'middlewares' => array(

        // Call this middlewares for all requests (including console)
        'global' => array(
            'before' => array(),
            'after' => array(),
        ),

        // Call this middlewares only if current matched route has own middlewares in routes config
        'routes' => array(
            'before' => array(),
            'after' => array(),
        ),

        'local' => array(
        ),
    ),
/*
    'router' => array(
        'routes' => array(
            'example_route' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/your_route[/]',
                    'defaults' => array(

                        // In the Middleware-based structure a MVC-controller is unnecessary.
                        // But you still can use this if no one Middleware didn't return a Response object
                        'controller' => \YourNameSpace\Controller\Controller::class,
                        'middlewares' => array(

                            // - You can setup global middlewares in module.config.php ('middlewares.lobal')
                            // that will be called before all middlewares

                            // - You can setup middlewares for all routes in module.config.php ('middlewares.routes')
                            // that will be called before all next middlewares for this route

                            // HTTP METHOD(s)
                            'GET' => array(
                                // The Middlewares list will be run one by one
                                // Each Middleware should call $next($request, $response) by default
                                // Each Middleware can return:
                                // - the result of the next Middleware,
                                // - or HttpResponse to show it immediately,
                                // - or nothing to break the chain
                                // - or throw the any Exception to send default Error response
                                \YourNameSpace\Middlewares\PrepareRequestMiddleware::class,
                                \YourNameSpace\Middlewares\ProcessRequestMiddleware::class,
                                \YourNameSpace\Middlewares\PrepareResponseMiddleware::class,
                            ),
                            // You can set up multiple comma separated methods (without spaces!)
                            'POST,PUT' => array(
                                //...
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
*/
);
