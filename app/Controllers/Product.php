<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;

class Product extends ResourceController
{
    protected $format = 'json';

    // دالة عرض المنتجات السابقة (GET)
    public function index()
    {
        $model = new ProductModel();
        $products = $model->findAll(); 

        $this->setCorsHeaders();
        return $this->respond($products, 200);
    }

    // الدالة الجديدة لإضافة منتج (POST)
    public function create()
    {
        $model = new ProductModel();

        // استقبال البيانات القادمة من تطبيق الهاتف بصيغة JSON
        $json = $this->request->getJSON();

        // التحقق من أن البيانات ليست فارغة
        if (!empty($json)) {
            $data = [
                'name'  => $json->name,
                'price' => $json->price
            ];

            // إدخال البيانات إلى قاعدة البيانات عبر الموديل
            if ($model->insert($data)) {
                $this->setCorsHeaders();
                // نرد على الفلاتر برسالة نجاح مع كود الحالة 201 (Created)
                return $this->respondCreated(['message' => 'تم إضافة المنتج بنجاح']);
            }
        }

        $this->setCorsHeaders();
        return $this->fail('فشل في إضافة المنتج، البيانات غير مكتملة', 400);
    }

    // دالة مساعدة لتجنب تكرار أكواد الـ CORS
    private function setCorsHeaders()
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Headers', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    }

    // دالة للتعامل مع طلبات الـ OPTIONS (مهمة لبعض المتصفحات والمحاكيات)
    public function options()
    {
        $this->setCorsHeaders();
        return $this->response->setStatusCode(200);
    }

// دالة حذف منتج (DELETE)
    public function delete($id = null)
    {
        $model = new ProductModel();

        // التأكد من أن المنتج موجود فعلياً في قاعدة البيانات
        $product = $model->find($id);

        if ($product) {
            // تنفيذ الحذف
            $model->delete($id);
            $this->setCorsHeaders();
            return $this->respondDeleted(['message' => 'تم حذف المنتج بنجاح']);
        }

        $this->setCorsHeaders();
        return $this->failNotFound('المنتج غير موجود أو تم حذفه مسبقاً');
    }

    // دالة تعديل منتج (PUT)
    public function update($id = null)
    {
        $model = new ProductModel();

        // التأكد من وجود المنتج أولاً
        $product = $model->find($id);
        if (!$product) {
            $this->setCorsHeaders();
            return $this->failNotFound('المنتج غير موجود');
        }

        // قراءة البيانات الجديدة المرسلة بصيغة JSON
        $json = $this->request->getJSON();

        if (!empty($json)) {
            $data = [
                'name'  => $json->name,
                'price' => $json->price
            ];

            // تحديث البيانات في الجدول بناءً على الـ id
            if ($model->update($id, $data)) {
                $this->setCorsHeaders();
                return $this->respond(['message' => 'تم تحديث المنتج بنجاح']);
            }
        }

        $this->setCorsHeaders();
        return $this->fail('فشل في التعديل، البيانات غير مكتملة', 400);
    }
    
}