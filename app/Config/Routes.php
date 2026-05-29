<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// السماح بطلبات الفحص القبلي (Preflight) للـ API
$routes->options('product', 'Product::options');
$routes->options('product/(:any)', 'Product::options');

// السطر القديم الخاص بك لربط العمليات
$routes->resource('product');