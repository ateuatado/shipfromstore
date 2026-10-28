<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard', ['filter' => 'auth']);
$routes->get('map', 'Map::index');
$routes->post('api/maps/route', 'Map::route');
$routes->get('login', 'Auth\\LoginController::loginView', ['as' => 'login']);
$routes->post('login', 'Auth\\LoginController::loginAction');

service('auth')->routes($routes, ['except' => ['login', 'register', 'magic-link']]);
