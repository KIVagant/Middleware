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

namespace Middleware\Service;

interface MiddlewareRunnerServiceInterface
{
    public function run(array $middlewareNames);
    public function getRequest();
    public function getResponse();
    public function getMiddlewareNames();
    public function setMiddlewareNames($middlewareNames);
}