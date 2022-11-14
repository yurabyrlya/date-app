<?php

declare(strict_types=1);

use DI\Container;
use Illuminate\Database\Capsule\Manager;

return function (Container $container) {

    $container->set('connection', function() use ($container) {
        $settings = $container->get('settings')['connection'];
        // Bootstrap Eloquent ORM
        $connection = new Manager;
        try {
            $connection->addConnection($settings);
            $connection->setAsGlobal();
            $connection->bootEloquent();
        } catch(Exception $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        return $connection;
    });
};
