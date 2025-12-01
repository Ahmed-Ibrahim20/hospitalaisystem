<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PatientService
{
    protected $model;

    public function __construct(Patient $patient)
    {
        $this->model = $patient;
    }

    /**
     * Get all patients with search and pagination
     */
    public function indexPatient($search = null, $perPage = 10)
    {
        try {
            $query = $this->model->query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Patient index failed: ' . $e->getMessage());
            return $this->model->paginate($perPage);
        }
    }

    /**
     * Store a new patient
     */
    public function storePatient(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'name',
                'age',
                'gender',
                'phone',
                'address',
            ]);

            $patient = $this->model->create($data);

            return [
                'status' => true,
                'message' => 'تم إنشاء المريض بنجاح',
                'data' => $patient
            ];
        } catch (\Exception $e) {
            Log::error('Patient creation failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء المريض'
            ];
        }
    }

    /**
     * Get patient for editing
     */
    public function editPatient($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Patient edit failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update patient
     */
    public function updatePatient(array $requestData, $id)
    {
        try {
            $patient = $this->model->findOrFail($id);

            $data = Arr::only($requestData, [
                'name',
                'age',
                'gender',
                'phone',
                'address',
            ]);

            $patient->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث بيانات المريض بنجاح',
                'data' => $patient
            ];
        } catch (\Exception $e) {
            Log::error('Patient update failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث بيانات المريض'
            ];
        }
    }

    /**
     * Delete patient
     */
    public function destroyPatient($id)
    {
        try {
            $patient = $this->model->findOrFail($id);

            // Check if patient has encounters
            if ($patient->encounters()->count() > 0) {
                return [
                    'status' => false,
                    'message' => 'لا يمكن حذف المريض لأنه مرتبط بزيارات طبية'
                ];
            }

            $patient->delete();

            return [
                'status' => true,
                'message' => 'تم حذف المريض بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Patient deletion failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف المريض'
            ];
        }
    }

    /**
     * Get all patients for dropdown
     */
    public function getAllPatients()
    {
        try {
            return $this->model->orderBy('name')->get();
        } catch (\Exception $e) {
            Log::error('Get all patients failed: ' . $e->getMessage());
            return collect();
        }
    }
}
