<?php
use Cake\Routing\Router;

Router::plugin(
    'CakeD',
    ['path' => '/cake-d'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
