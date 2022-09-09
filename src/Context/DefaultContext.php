<?php

namespace App\Context;

use App\Services\Container;
use App\Services\PDO as ServicesPDO;
use App\Services\Router;
use PDO;

class DefaultContext{
    public function registerServices(){
        $container = Container::getInstance();
        $container->asShared(function(){
            return new ServicesPDO('localhost','test','root','');
        });
        
        $container->offsetSet('router', new Router);
    }
}