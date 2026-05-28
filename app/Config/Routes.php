<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// إضافة مسار جديد لمنتجات
$routes->get('/product', 'Product::index');
// هذا السطر يربط تلقائياً الـ GET والـ POST والـ DELETE بالدوال المقابلة لها في الكنترولر
$routes->resource('product');
