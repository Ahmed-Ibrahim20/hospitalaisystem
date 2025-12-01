<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MedicineRequest;
use App\Services\MedicineService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MedicineController extends Controller
{
    use AuthorizesRequests;

    protected $medicineService;

    public function __construct(MedicineService $medicineService)
    {
        $this->medicineService = $medicineService;
    }

    public function index(Request $request)
    {
        $searchMedicine = $request->query('search');
        $perPageMedicine = $request->query('perPage', 10);
        $filter = $request->query('filter');

        $medicines = $this->medicineService->indexMedicine($searchMedicine, $perPageMedicine, $filter);

        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('medicines.create');
    }

    public function store(MedicineRequest $request)
    {
        $result = $this->medicineService->storeMedicine($request->validated());

        return isset($result['message'])
            ? redirect()->route('medicines.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء إضافة الدواء.');
    }

    public function show($id)
    {
        $medicine = $this->medicineService->editMedicine($id);

        if (!$medicine) {
            return redirect()->route('medicines.index')->with('error', 'الدواء غير موجود.');
        }

        return view('medicines.show', compact('medicine'));
    }

    public function edit($id)
    {
        $medicine = $this->medicineService->editMedicine($id);

        if (!$medicine) {
            return redirect()->route('medicines.index')->with('error', 'الدواء غير موجود.');
        }

        return view('medicines.edit', compact('medicine'));
    }

    public function update(MedicineRequest $request, $id)
    {
        $result = $this->medicineService->updateMedicine($request->validated(), $id);

        return isset($result['message'])
            ? redirect()->route('medicines.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء تحديث بيانات الدواء.');
    }

    public function destroy($id)
    {
        $result = $this->medicineService->destroyMedicine($id);

        return isset($result['message'])
            ? redirect()->route('medicines.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء حذف الدواء.');
    }

    /**
     * Update medicine stock
     */
    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'operation' => 'required|in:add,subtract'
        ]);

        $result = $this->medicineService->updateStock(
            $id,
            $request->quantity,
            $request->operation
        );

        return response()->json($result);
    }

    /**
     * Bulk update medicine stock
     */
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'updates' => 'required|array|min:1',
            'updates.*.id' => 'required|integer|exists:medicines,id',
            'updates.*.quantity' => 'required|integer|min:1',
            'updates.*.operation' => 'required|in:add,subtract'
        ]);

        $result = $this->medicineService->bulkUpdateStock($request->updates);

        return response()->json($result);
    }

    /**
     * Get pharmacy dashboard data
     */
    public function dashboard()
    {
        $stats = $this->medicineService->getPharmacyStats();
        $attention = $this->medicineService->getMedicinesNeedingAttention();

        return view('medicines.dashboard', compact('stats', 'attention'));
    }
}
