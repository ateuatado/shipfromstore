<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard', ['filter' => 'auth']);
$routes->get('map', 'Map::index');
$routes->post('api/maps/route', 'Map::route');

service('auth')->routes($routes);
