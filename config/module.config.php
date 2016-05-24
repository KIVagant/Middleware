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
        'global' => array(
            'before' => array(),
            'after' => array(),
        ),
        'local' => array(
        ),
    ),
);
