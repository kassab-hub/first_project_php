<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Product extends ResourceController
{
    // نحدد أن الصيغة الافتراضية للرد هي JSON
    protected $format = 'json'; 

    public function index()
    {
        // البيانات التي نريد إرسالها للتطبيق
        $products = [
            [
                "id"    => 1, 
                "name"  => "تفاح أحمر", 
                "price" => 5.5
            ],
            [
                "id"    => 2, 
                "name"  => "موز طازج", 
                "price" => 1.8
            ],
            [
                "id"    => 3, 
                "name"  => "برتقال طبيعي", 
                "price" => 3.0
            ]
        ];

        // تفعيل الـ Headers لفك حظر CORS وضمان وصول البيانات كاملة
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Headers', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');

        // إرجاع الاستجابة للفلاتر
        return $this->respond($products, 200);
    }
}