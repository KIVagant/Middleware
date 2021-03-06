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

namespace MiddlewareTest\Service;

use Middleware\Service\MiddlewareRunnerService;

class MiddlewareRunnerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MiddlewareRunnerService
     */
    private $service;

    public function testInvokeShouldCallHandleMethodFromMiddleware()
    {
        $middleware      = $this->givenMiddlewareStub();
        $factory         = $this->givenMiddlewareFactory($middleware);
        $service         = $this->givenService($factory);
        $middlewareClass = get_class($middleware);

        $middleware->expects($this->once())->method('__invoke');

        $service->run(array($middlewareClass));
    }

    public function testShouldNotCallTheFactoryWhenReceiveZeroMiddlewareNames()
    {
        $factoryCallCount = 0;

        $service = $this->givenService(function() use(&$factoryCallCount) {
            $factoryCallCount++;
            return function(){};
        });

        $service->run(array());

        $this->assertEquals(0, $factoryCallCount);
    }

    public function testNextShouldCallNextMiddleware()
    {
        $called = 0;

        $middlewareMock = $this->givenMiddlewareStub();

        $service = $this->givenService(function () use(&$called, $middlewareMock){

            $called++;

            if ($called ==  1) {
                return function ($request, $response, $next) use (&$called) {
                    if ($called < 3) {
                        $next();
                    }
                };
            }

            return $middlewareMock;
        });

        $middlewareMock->expects($this->once())->method('__invoke');

        $service->run(array(
            function($request, $response, $next){
                $next();
            },
            'teste1',
            'teste3'
        ));
    }

    /**
     * @return \Middleware\Service\MiddlewareRunnerService
     */
    private function givenService(\Closure $middlewareFactory = null)
    {
        $service = new MiddlewareRunnerService(
            $this->givenRequestStub(),
            $this->givenResponseStub(),
            $middlewareFactory ?: function () {}
        );

        return $service;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    private function givenRequestStub()
    {
        $request = $this->getStub('Zend\Http\PhpEnvironment\Request');

        return $request;
    }

    /**
     * @return \Zend\Http\PhpEnvironment\Response|\PHPUnit_Framework_MockObject_MockObject
     */
    private function givenResponseStub()
    {
        $request = $this->getStub('Zend\Http\PhpEnvironment\Response');

        return $request;
    }

    /**
     * @param $className
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getStub($className)
    {
        return $this->getMockForAbstractClass(
            $className,
            array(),
            '',
            false,
            false,
            true,
            get_class_methods($className)
        );
    }

    /**
     * @return \Middleware\MiddlewareInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function givenMiddlewareStub()
    {
        $middleware = $this->getStub('Middleware\MiddlewareInterface');

        return $middleware;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorAwareInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenServiceLocatorAwareStub()
    {
        $middleware = $this->getStub('Zend\ServiceManager\ServiceLocatorAwareInterface');

        return $middleware;
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenServiceLocatorStub()
    {
        $middleware = $this->getStub('Zend\ServiceManager\ServiceLocatorInterface');

        return $middleware;
    }

    /**
     * @return \Zend\Mvc\MvcEvent | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenMvcEventStub()
    {
        return $this->getStub('\Zend\Mvc\MvcEvent');
    }

    /**
     * @param $middleware
     *
     * @return \Closure
     */
    private function givenMiddlewareFactory($middleware)
    {
        return function ($middlewareClass) use ($middleware) {
            return $middleware;
        };
    }
}
