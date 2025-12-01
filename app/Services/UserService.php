<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * قائمة المستخدمين مع إمكانية البحث والتصفية
     */
    public function indexUser($searchUser = null, $perPageUser = 10)
    {
        return $this->model
            ->where('id', '!=', 1) // استبعاد أول مستخدم
            ->when($searchUser, function ($query) use ($searchUser) {
                $query->where(function ($q) use ($searchUser) {
                    $q->where('name', 'like', "%{$searchUser}%")
                        ->orWhere('email', 'like', "%{$searchUser}%")
                        ->orWhere('phone', 'like', "%{$searchUser}%");
                });
            })
            ->paginate($perPageUser);
    }


    /**
     * إنشاء مستخدم جديد
     */
    public function storeUser(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'name',
                'email',
                'password',
                'role_id',
            ]);

            $data['password'] = Hash::make($data['password']);
            $user = $this->model->create($data);

            return [
                'status' => true,
                'message' => 'تم إنشاء المستخدم بنجاح',
                'data' => $user
            ];
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء المستخدم'
            ];
        }
    }

    /**
     * استرجاع بيانات مستخدم للتعديل
     */
    public function editUser($userId)
    {
        return $this->model->find($userId);
    }

    /**
     * تحديث بيانات مستخدم
     */
    public function updateUser(array $requestData, $userId)
    {
        try {
            $user = $this->model->find($userId);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'User not found'
                ];
            }

            $data = Arr::only($requestData, [
                'name',
                'email',
                'role_id',
            ]);

            // إضافة كلمة السر فقط لو موجودة ومدخولة
            if (!empty($requestData['password'])) {
                $data['password'] = Hash::make($requestData['password']);
            }

            $user->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث المستخدم بنجاح',
                'data' => $user
            ];
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث المستخدم'
            ];
        }
    }

    /**
     * حذف مستخدم
     */
    public function destroyUser($userId)
    {
        try {
            $user = $this->model->find($userId);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'User not found'
                ];
            }

            $user->delete();

            return [
                'status' => true,
                'message' => 'تم حذف المستخدم بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف المستخدم'
            ];
        }
    }
}
