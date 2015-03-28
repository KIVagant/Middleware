<?php

namespace MiddlewareTest\Listener;


use Middleware\Entity\Middleware;
use Middleware\Listener\MiddlewareListener;

class MiddlewareListenerTest extends \PHPUnit_Framework_TestCase {

    public function testWhenNotHasConfigurationOnDispatchNotShouldTryGetMiddlewareService() {

        $listener       = $this->givenListener();
        $mvcEvent       = $this->givenMvcEventStub();
        $application    = $this->givenApplicationStub();
        $serviceManager = $this->givenServiceManagerStub();

        $mvcEvent->expects($this->once())->method('getApplication')->willReturn($application);
        $application->expects($this->once())->method('getServiceManager')->willReturn($serviceManager);
        $serviceManager->expects($this->once())->method('get')->willReturn([]);

        $listener->onDispatch($mvcEvent);
    }

    /**
     * @return MiddlewareListener
     */
    private function givenListener()
    {
        return new MiddlewareListener();
    }

    /**
     * @return \Zend\Mvc\MvcEvent | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenMvcEventStub()
    {
        return $this->givenStub('Zend\Mvc\MvcEvent');
    }

    /**
     * @param strig $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenStub($className)
    {
        return $this->getMockForAbstractClass($className, [], '', false, false, true, get_class_methods($className));
    }

    /**
     * @return \Zend\Mvc\Application | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenApplicationStub()
    {
        return $this->givenStub('Zend\Mvc\Application');
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager | \PHPUnit_Framework_MockObject_MockObject
     */
    private function givenServiceManagerStub()
    {
        return $this->givenStub('Zend\ServiceManager\ServiceManager');
    }

    public function testWhenGlobalConfigurationIsNotFoundOnDispatchShouldNotRunGlobalMiddleware() {

        $listener           = $this->givenListener();
        $mvcEvent           = $this->givenMvcEventStub();
        $application        = $this->givenApplicationStub();
        $serviceManager     = $this->givenServiceManagerStub();
        $middlewareService  = $this->givenMiddlewareServiceStub();
        $routeMatch         = $this->givenRouteMatch();

        $mvcEvent->expects($this->at(0))->method('getApplication')->willReturn($application);
        $application->expects($this->once())->method('getServiceManager')->willReturn($serviceManager);
        $serviceManager->expects($this->at(0))->method('get')->willReturn([Middleware::CONFIG => []]);
        $serviceManager->expects($this->at(1))->method('get')->with($this->equalTo('MiddlewareService'))->willReturn($middlewareService);

        $middlewareService->expects($this->at(0))->method('setEvent');
        $middlewareService->expects($this->never())->method('run');

        $middlewareService->expects($this->at(1))->method('getEvent')->willReturn($mvcEvent);
        $mvcEvent->expects($this->at(1))->method('getRouteMatch')->willReturn($routeMatch);
        $routeMatch->expects($this->once())->method('getParam')->willReturn('');

        $listener->onDispatch($mvcEvent);
    }

    public function testWhenGlobalConfigurationIsFoundOnDispatchShouldRunGlobalMiddleware() {

        $listener           = $this->givenListener();
        $mvcEvent           = $this->givenMvcEventStub();
        $application        = $this->givenApplicationStub();
        $serviceManager     = $this->givenServiceManagerStub();
        $middlewareService  = $this->givenMiddlewareServiceStub();
        $routeMatch         = $this->givenRouteMatch();

        $mvcEvent->expects($this->at(0))->method('getApplication')->willReturn($application);
        $application->expects($this->once())->method('getServiceManager')->willReturn($serviceManager);
        $serviceManager->expects($this->at(0))->method('get')->willReturn(array(Middleware::CONFIG => array(Middleware::CONFIG_GLOBAL => array('Test') )));
        $serviceManager->expects($this->at(1))->method('get')->with($this->equalTo('MiddlewareService'))->willReturn($middlewareService);

        $middlewareService->expects($this->at(0))->method('setEvent');
        $middlewareService->expects($this->at(1))->method('run');

        $middlewareService->expects($this->at(2))->method('getEvent')->willReturn($mvcEvent);
        $mvcEvent->expects($this->at(1))->method('getRouteMatch')->willReturn($routeMatch);
        $routeMatch->expects($this->once())->method('getParam')->willReturn('');

        $listener->onDispatch($mvcEvent);
    }

    /**
     * @return \Middleware\Service\MiddlewareService | \PHPUnit_Framework_MockObject_MockObject
     */
    public function givenMiddlewareServiceStub()
    {
        return $this->givenStub('Middleware\Service\MiddlewareService');
    }

    public function givenRouteMatch()
    {
        return $this->givenStub('Zend\Mvc\Router\RouteMatch');
    }

}