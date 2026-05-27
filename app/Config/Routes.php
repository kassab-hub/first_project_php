<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// إضافة مسار جديد لمنتجات
$routes->get('/product', 'Product::index');