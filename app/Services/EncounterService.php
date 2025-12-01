<?php

namespace App\Services;

use App\Models\Encounter;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class EncounterService
{
    protected $model;

    public function __construct(Encounter $encounter)
    {
        $this->model = $encounter;
    }

    /**
     * Get all encounters with search and pagination
     */
    public function indexEncounter($search = null, $perPage = 10)
    {
        try {
            $query = $this->model->with(['patient', 'doctor']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                            $doctorQuery->where('name', 'like', "%{$search}%");
                        })
                        ->orWhere('visit_date', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('visit_date', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Encounter index failed: ' . $e->getMessage());
            return $this->model->with(['patient', 'doctor'])->paginate($perPage);
        }
    }

    /**
     * Store a new encounter
     */
    public function storeEncounter(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'patient_id',
                'doctor_id',
                'visit_date',
            ]);

            $encounter = $this->model->create($data);

            return [
                'status' => true,
                'message' => 'تم إنشاء الزيارة الطبية بنجاح',
                'data' => $encounter
            ];
        } catch (\Exception $e) {
            Log::error('Encounter creation failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إنشاء الزيارة الطبية'
            ];
        }
    }

    /**
     * Get encounter for editing
     */
    public function editEncounter($id)
    {
        try {
            return $this->model->with(['patient', 'doctor'])->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Encounter edit failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update encounter
     */
    public function updateEncounter(array $requestData, $id)
    {
        try {
            $encounter = $this->model->findOrFail($id);

            $data = Arr::only($requestData, [
                'patient_id',
                'doctor_id',
                'visit_date',
            ]);

            $encounter->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث بيانات الزيارة الطبية بنجاح',
                'data' => $encounter
            ];
        } catch (\Exception $e) {
            Log::error('Encounter update failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث بيانات الزيارة الطبية'
            ];
        }
    }

    /**
     * Delete encounter
     */
    public function destroyEncounter($id)
    {
        try {
            $encounter = $this->model->findOrFail($id);

            // Check if encounter has related records (predictions, medical records, etc.)
            if ($encounter->predictions()->count() > 0 || $encounter->medicalRecords()->count() > 0) {
                return [
                    'status' => false,
                    'message' => 'لا يمكن حذف الزيارة لأنها مرتبطة بسجلات طبية أو تنبؤات'
                ];
            }

            $encounter->delete();

            return [
                'status' => true,
                'message' => 'تم حذف الزيارة الطبية بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Encounter deletion failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف الزيارة الطبية'
            ];
        }
    }

    /**
     * Get all patients for dropdown
     */
    public function getAllPatients()
    {
        try {
            return Patient::orderBy('name')->get();
        } catch (\Exception $e) {
            Log::error('Get all patients failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get all doctors for dropdown
     */
    public function getAllDoctors()
    {
        try {
            return User::whereHas('role', function ($query) {
                $query->where('name', 'doctor');
            })->orderBy('name')->get();
        } catch (\Exception $e) {
            Log::error('Get all doctors failed: ' . $e->getMessage());
            return User::orderBy('name')->get(); // Fallback to all users
        }
    }

    /**
     * Get encounter statistics
     */
    public function getEncounterStats()
    {
        try {
            return [
                'total' => $this->model->count(),
                'today' => $this->model->whereDate('visit_date', today())->count(),
                'this_week' => $this->model->whereBetween('visit_date', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month' => $this->model->whereMonth('visit_date', now()->month)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Get encounter stats failed: ' . $e->getMessage());
            return [
                'total' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
            ];
        }
    }
}
