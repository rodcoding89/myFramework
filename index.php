<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Services\Container;
use App\Services\Dispatcher;
use App\Services\Request;
use App\Context\DefaultContext;

$container = Container::getInstance();
$defaultContext = new DefaultContext();
$defaultContext->registerServices();

$dispatcher = new Dispatcher(new Request());
