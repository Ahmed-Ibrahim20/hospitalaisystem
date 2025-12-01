<?php

namespace App\Services;

use App\Models\Medicine;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MedicineService
{
    protected $model;

    public function __construct(Medicine $medicine)
    {
        $this->model = $medicine;
    }

    /**
     * Get all medicines with search and pagination
     */
    public function indexMedicine($search = null, $perPage = 10, $filter = null)
    {
        try {
            $query = $this->model->query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('generic_name', 'like', "%{$search}%")
                        ->orWhere('manufacturer', 'like', "%{$search}%")
                        ->orWhere('batch_number', 'like', "%{$search}%");
                });
            }

            // Apply filters
            if ($filter) {
                switch ($filter) {
                    case 'low_stock':
                        $query->lowStock();
                        break;
                    case 'expired':
                        $query->expired();
                        break;
                    case 'active':
                        $query->active();
                        break;
                    case 'inactive':
                        $query->where('status', 'inactive');
                        break;
                }
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('Medicine index failed: ' . $e->getMessage());
            return $this->model->paginate($perPage);
        }
    }

    /**
     * Store a new medicine
     */
    public function storeMedicine(array $requestData)
    {
        try {
            $data = Arr::only($requestData, [
                'name',
                'generic_name',
                'description',
                'manufacturer',
                'price',
                'quantity_in_stock',
                'minimum_stock_level',
                'unit',
                'expiry_date',
                'batch_number',
                'status',
            ]);

            $medicine = $this->model->create($data);

            return [
                'status' => true,
                'message' => 'تم إضافة الدواء بنجاح',
                'data' => $medicine
            ];
        } catch (\Exception $e) {
            Log::error('Medicine creation failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء إضافة الدواء'
            ];
        }
    }

    /**
     * Get medicine for editing
     */
    public function editMedicine($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Medicine edit failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update medicine
     */
    public function updateMedicine(array $requestData, $id)
    {
        try {
            $medicine = $this->model->findOrFail($id);

            $data = Arr::only($requestData, [
                'name',
                'generic_name',
                'description',
                'manufacturer',
                'price',
                'quantity_in_stock',
                'minimum_stock_level',
                'unit',
                'expiry_date',
                'batch_number',
                'status',
            ]);

            $medicine->update($data);

            return [
                'status' => true,
                'message' => 'تم تحديث بيانات الدواء بنجاح',
                'data' => $medicine
            ];
        } catch (\Exception $e) {
            Log::error('Medicine update failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث بيانات الدواء'
            ];
        }
    }

    /**
     * Delete medicine
     */
    public function destroyMedicine($id)
    {
        try {
            $medicine = $this->model->findOrFail($id);

            $medicine->delete();

            return [
                'status' => true,
                'message' => 'تم حذف الدواء بنجاح'
            ];
        } catch (\Exception $e) {
            Log::error('Medicine deletion failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء حذف الدواء'
            ];
        }
    }

    /**
     * Get pharmacy statistics
     */
    public function getPharmacyStats()
    {
        try {
            return [
                'total_medicines' => $this->model->count(),
                'active_medicines' => $this->model->active()->count(),
                'low_stock_medicines' => $this->model->lowStock()->count(),
                'expired_medicines' => $this->model->expired()->count(),
                'total_stock_value' => $this->model->sum(DB::raw('price * quantity_in_stock')),
            ];
        } catch (\Exception $e) {
            Log::error('Get pharmacy stats failed: ' . $e->getMessage());
            return [
                'total_medicines' => 0,
                'active_medicines' => 0,
                'low_stock_medicines' => 0,
                'expired_medicines' => 0,
                'total_stock_value' => 0,
            ];
        }
    }

    /**
     * Update medicine stock with smart logic
     */
    public function updateStock($id, $quantity, $operation = 'add')
    {
        try {
            $medicine = $this->model->findOrFail($id);
            $oldQuantity = $medicine->quantity_in_stock;

            // تحديث الكمية حسب العملية
            if ($operation === 'add') {
                $medicine->quantity_in_stock += $quantity;
                $operationText = 'إضافة';
            } else {
                // التأكد من عدم الوصول لرقم سالب
                $newQuantity = $medicine->quantity_in_stock - $quantity;
                if ($newQuantity < 0) {
                    return [
                        'status' => false,
                        'message' => 'لا يمكن خصم كمية أكبر من المتوفر في المخزون',
                        'current_stock' => $medicine->quantity_in_stock,
                        'requested_quantity' => $quantity
                    ];
                }
                $medicine->quantity_in_stock = $newQuantity;
                $operationText = 'خصم';
            }

            // تحديث حالة الدواء تلقائياً حسب المخزون
            if ($medicine->quantity_in_stock == 0) {
                $statusMessage = ' - تحذير: نفد المخزون!';
            } elseif ($medicine->quantity_in_stock <= $medicine->minimum_stock_level) {
                $statusMessage = ' - تحذير: المخزون منخفض!';
            } else {
                $statusMessage = '';
            }

            $medicine->save();

            // رسالة تفصيلية
            $message = sprintf(
                'تم %s %d %s بنجاح. الكمية السابقة: %d، الكمية الحالية: %d%s',
                $operationText,
                $quantity,
                $medicine->unit,
                $oldQuantity,
                $medicine->quantity_in_stock,
                $statusMessage
            );

            return [
                'status' => true,
                'message' => $message,
                'data' => [
                    'id' => $medicine->id,
                    'name' => $medicine->name,
                    'old_quantity' => $oldQuantity,
                    'new_quantity' => $medicine->quantity_in_stock,
                    'unit' => $medicine->unit,
                    'minimum_stock_level' => $medicine->minimum_stock_level,
                    'is_low_stock' => $medicine->quantity_in_stock <= $medicine->minimum_stock_level,
                    'is_out_of_stock' => $medicine->quantity_in_stock == 0,
                    'status_badge' => $medicine->getStatusBadgeClass(),
                    'status_text' => $medicine->getStatusText()
                ]
            ];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return [
                'status' => false,
                'message' => 'الدواء غير موجود في النظام'
            ];
        } catch (\Exception $e) {
            Log::error('Stock update failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء تحديث المخزون: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk update stock for multiple medicines
     */
    public function bulkUpdateStock($updates)
    {
        try {
            $results = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($updates as $update) {
                $result = $this->updateStock(
                    $update['id'],
                    $update['quantity'],
                    $update['operation'] ?? 'add'
                );

                $results[] = $result;

                if ($result['status']) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            }

            return [
                'status' => true,
                'message' => sprintf('تم تحديث %d دواء بنجاح، فشل في %d', $successCount, $failureCount),
                'results' => $results,
                'summary' => [
                    'total' => count($updates),
                    'success' => $successCount,
                    'failure' => $failureCount
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Bulk stock update failed: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'حدث خطأ أثناء التحديث المجمع للمخزون'
            ];
        }
    }

    /**
     * Get medicines that need attention (low stock or expired)
     */
    public function getMedicinesNeedingAttention()
    {
        try {
            return [
                'low_stock' => $this->model->lowStock()->limit(5)->get(),
                'expired' => $this->model->expired()->limit(5)->get(),
                'expiring_soon' => $this->model->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>', now())
                    ->limit(5)->get(),
            ];
        } catch (\Exception $e) {
            Log::error('Get medicines needing attention failed: ' . $e->getMessage());
            return [
                'low_stock' => collect(),
                'expired' => collect(),
                'expiring_soon' => collect(),
            ];
        }
    }
}
