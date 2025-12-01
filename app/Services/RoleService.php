<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RoleService
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function indexRole($search = null, $perPage = 10)
    {
        $query = $this->model->query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function storeRole(array $requestData)
    {
        try {
            $data = Arr::only($requestData, ['name']);

            $role = $this->model->create($data);

            return [
                'status' => true,
                'message' => 'تم إنشاء الدور بنجاح',
                'data' => $role
            ];
        } catch (\Exception $e) {
            Log::error('Role creation failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء الدور'
            ];
        }
    }

    public function editRole($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Role not found: ' . $e->getMessage());
            return null;
        }
    }

    public function updateRole(array $requestData, $id)
    {
        try {
            $role = $this->model->findOrFail($id);

            $data = Arr::only($requestData, ['name']);

            $role->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث الدور بنجاح',
                'data' => $role
            ];
        } catch (\Exception $e) {
            Log::error('Role update failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث الدور'
            ];
        }
    }

    public function destroyRole($id)
    {
        try {
            $role = $this->model->findOrFail($id);

            // Check if role has users
            if ($role->users()->count() > 0) {
                return [
                    'status' => false,
                    'message' => 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين'
                ];
            }

            $role->delete();

            return [
                'status' => true,
                'message' => 'تم حذف الدور بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Role deletion failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف الدور'
            ];
        }
    }

    public function getAllRoles()
    {
        return $this->model->orderBy('name')->get();
    }
}
