<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;
use Pusher\Pusher;

class Product extends ResourceController
{
protected $format = 'json';

public function __construct()
    {
        // السماح للهاتف بإرسال واستقبال البيانات بأمان دون قيود المتصفح أو المنصة
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        
        // التعامل مع طلب الفحص المبدئي (OPTIONS) الذي يرسله نظام الأندرويد
        if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
            die();
        }
    }

    // دالة عرض المنتجات (GET)
    public function index()
    {
        $model = new ProductModel();
        return $this->respond($model->findAll());
    }

// دالة إضافة منتج جديد (POST)
// دالة إضافة منتج جديد (POST)
    public function create()
    {
        $model = new ProductModel();
        $json = $this->request->getJSON();
        
        $name = isset($json->name) ? $json->name : $this->request->getPost('name');
        $price = isset($json->price) ? $json->price : $this->request->getPost('price');

        if (!empty($name) && !empty($price)) {
            $data = [
                'name'  => $name,
                'price' => $price
            ];

            if ($model->insert($data)) {
                $this->triggerPusher();
                // 🌟 إرجاع كود 200 صريح مع رسالة النجاح ليفهمها الفلاتر والموقع
                return $this->respond(['status' => 200, 'message' => 'تم إضافة المنتج بنجاح'], 200);
            } else {
                return $this->fail(['message' => 'فشل الإدخال في قاعدة البيانات', 'errors' => $model->errors()], 500);
            }
        }

        return $this->fail('فشل في إضافة المنتج، البيانات المرسلة فارغة أو غير مكتملة', 400);
    }

    // دالة حذف منتج (DELETE)
    public function delete($id = null)
    {
        $model = new ProductModel();

        if (empty($id)) {
            return $this->fail('فشل الطلب: لم يتم إرسال الـ ID الخاص بالمنتج', 400);
        }

        $product = $model->find($id);

        if ($product) {
            if ($model->delete($id)) {
                $this->triggerPusher();
                // 🌟 إرجاع كود 200 صريح ليتطابق مع شرط الفلاتر (response.statusCode == 200)
                return $this->respond(['status' => 200, 'message' => 'تم حذف المنتج بنجاح'], 200);
            } else {
                return $this->fail('فشل الحذف من قاعدة البيانات', 500);
            }
        }

        return $this->failNotFound('المنتج غير موجود في قاعدة البيانات');
    }

    // دالة تعديل منتج (PUT)
    public function update($id = null)
    {
        $model = new ProductModel();
        $product = $model->find($id);
        
        if (!$product) {
            return $this->failNotFound('المنتج غير موجود');
        }

        $json = $this->request->getJSON();

        if (!empty($json)) {
            $data = [
                'name'  => $json->name,
                'price' => $json->price
            ];

            if ($model->update($id, $data)) {
                $this->triggerPusher();
                return $this->respond(['message' => 'تم تحديث المنتج بنجاح']);
            }
        }

        return $this->fail('فشل في التعديل، البيانات غير مكتملة', 400);
    }

    // دالة إرسال الإشارة الحية لـ Pusher
    private function triggerPusher()
    {
        $options = [
            'cluster' => 'ap1',
            'useTLS' => true
        ];
        
$pusher = new Pusher(
    env('PUSHER_APP_KEY'),
    env('PUSHER_APP_SECRET'),
    env('PUSHER_APP_ID'),
    [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'useTLS' => true
    ]
);

        $pusher->trigger('fruit-channel', 'update-event', array('message' => 'refresh'));
    }
}