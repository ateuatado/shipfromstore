<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('dashboard', 'Home::dashboard', ['filter' => 'auth']);
$routes->get('dashboard/pickups', 'Home::pickups', ['filter' => 'auth']);
$routes->get('dashboard/deliveries', 'Home::deliveries', ['filter' => 'auth']);
$routes->get('dashboard/assignments', 'Assignments::index', ['filter' => 'auth']);
$routes->post('dashboard/assignments', 'Assignments::assign', ['filter' => 'auth']);
$routes->get('dashboard/routes', 'RouteTracking::index', ['filter' => 'auth']);
$routes->get('dashboard/routes/data', 'RouteTracking::data', ['filter' => 'auth']);
$routes->post('courier/collection/confirm', 'Courier::confirmCollection', ['filter' => 'auth']);
$routes->post('courier/delivery/confirm', 'Courier::confirmDelivery', ['filter' => 'auth']);
$routes->post('courier/tracking/point', 'Courier::recordTrackingPoint', ['filter' => 'auth']);
$routes->get('map', 'Map::index');
$routes->post('api/maps/route', 'Map::route');
$routes->get('login', 'Auth\\LoginController::loginView', ['as' => 'login']);
$routes->post('login', 'Auth\\LoginController::loginAction');

service('auth')->routes($routes, ['except' => ['login', 'register', 'magic-link']]);
