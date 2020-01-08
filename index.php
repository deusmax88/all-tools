<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as Response;

require_once("vendor/autoload.php");
require_once("di-container.php");

$request = Request::createFromGlobals();

if ($request->getPathInfo() == '/generate') {
    echo $containerBuilder->get('app.controller.product')->generate($request);
    die();
}

if ($request->getPathInfo() == '/create') {
    echo  $containerBuilder->get('app.controller.order')->createOrder($request);
    die();
}

if ($request->getPathInfo() == '/pay') {
    echo $containerBuilder->get('app.controller.order')->payOrder($request);
    die();
}

echo new Response('Not Found', 404, ['Content-Type' => 'application/json']);