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

namespace Middleware\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class MiddlewareListener extends AbstractListenerAggregate
{
    const CONFIG        = 'middlewares';
    const CONFIG_GLOBAL = 'global';
    const CONFIG_LOCAL  = 'local';
    const CONFIG_ROUTES  = 'routes';
    const CONFIG_BEFORE = 'before';
    const CONFIG_AFTER  = 'after';

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * Attachs onDispatch event.
     *
     * @param EventManagerInterface $eventManager
     */
    public function attach(EventManagerInterface $eventManager)
    {
        $this->listeners[] = $eventManager->attach(
            MvcEvent::EVENT_DISPATCH,
            array($this, 'onDispatch'),
            100
        );

        // Attach middlewares to the route before controllers
        $this->listeners[] = $eventManager->attach(
            MvcEvent::EVENT_ROUTE,
            array($this, 'onRoute'),
            -1);
    }

    /**
     * On dispatch handles local and global middlewares.
     *
     * @param MvcEvent $event
     * @return \Zend\Stdlib\ResponseInterface|mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        $sm = $event->getApplication()->getServiceManager();
        /** @var \Middleware\Service\MiddlewareRunnerService $service */
        $service = $sm->get('MiddlewareRunnerService');
        $config  = $sm->get('Config');
        $controllerClass = $event->getRouteMatch()->getParam('controller').'Controller';
        $local  = isset($config[self::CONFIG][self::CONFIG_LOCAL][$controllerClass]) ? $config[self::CONFIG][self::CONFIG_LOCAL][$controllerClass] : array();
        $middlewareNames = $this->mergeConfigs($config[self::CONFIG][self::CONFIG_GLOBAL], $local);

        return $service->run($middlewareNames);
    }

    /**
     * Listen to the "route" event and run related middlewares.
     *
     * @param  MvcEvent $event
     * @return null
     */
    public function onRoute(MvcEvent $event)
    {
        $matches = $event->getRouteMatch();
        if (!$matches instanceof \Zend\Mvc\Router\RouteMatch) {
            // Can't do anything without a route match
            return;
        }

        $routeMiddlewares = $matches->getParam('middlewares', false);
        if (!$routeMiddlewares) {
            return;
        }
        $method = $event->getRequest()->getMethod();
        $local = null;
        foreach ($routeMiddlewares as $routeMethod => $methodMiddlewares) {

            // Multiple methods are supported in keys: 'GET,POST'
            $routeMethod = explode(',', $routeMethod);
            if (is_array($routeMethod) && in_array($method, $routeMethod, true)) {
                $local = $methodMiddlewares;
                break;
            }
        }
        if (null === $local) {
            return;
        }
        $sm = $event->getApplication()->getServiceManager();
        /** @var \Middleware\Service\MiddlewareRunnerService $service */
        $service = $sm->get('MiddlewareRunnerService');
        $config  = $sm->get('Config');

        $middlewareNames = $this->mergeConfigs($config[self::CONFIG][self::CONFIG_ROUTES], $local);

        return $service->run($middlewareNames);
    }

    /**
     * @param $global
     * @param $local
     * @return array
     */
    protected function mergeConfigs($global, $local)
    {
        if (array_key_exists(self::CONFIG_BEFORE, $global)) {
            $local = array_merge($global[self::CONFIG_BEFORE], $local);
        }
        if (array_key_exists(self::CONFIG_AFTER, $global)) {
            $local = array_merge($local, $global[self::CONFIG_AFTER]);

            return $local; // skip next BC-condition
        }
        if ( // backward compatibility
            !empty($global)
            && !array_key_exists(self::CONFIG_BEFORE, $global)
            && !array_key_exists(self::CONFIG_AFTER, $global)) {
            $local = array_merge($global, $local);
        }

        return $local;
    }
}
