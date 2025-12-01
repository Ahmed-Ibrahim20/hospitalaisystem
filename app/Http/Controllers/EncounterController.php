<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EncounterRequest;
use App\Services\EncounterService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EncounterController extends Controller
{
    use AuthorizesRequests;

    protected $encounterService;

    public function __construct(EncounterService $encounterService)
    {
        $this->encounterService = $encounterService;
    }

    public function index(Request $request)
    {
        $searchEncounter = $request->query('search');
        $perPageEncounter = $request->query('perPage', 10);

        $encounters = $this->encounterService->indexEncounter($searchEncounter, $perPageEncounter);

        return view('encounters.index', compact('encounters'));
    }

    public function create()
    {
        $patients = $this->encounterService->getAllPatients();
        $doctors = $this->encounterService->getAllDoctors();

        return view('encounters.create', compact('patients', 'doctors'));
    }

    public function store(EncounterRequest $request)
    {
        $result = $this->encounterService->storeEncounter($request->validated());

        return isset($result['message'])
            ? redirect()->route('encounters.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الزيارة الطبية.');
    }

    public function show($id)
    {
        $encounter = $this->encounterService->editEncounter($id);

        if (!$encounter) {
            return redirect()->route('encounters.index')->with('error', 'الزيارة الطبية غير موجودة.');
        }

        return view('encounters.show', compact('encounter'));
    }

    public function edit($id)
    {
        $encounter = $this->encounterService->editEncounter($id);

        if (!$encounter) {
            return redirect()->route('encounters.index')->with('error', 'الزيارة الطبية غير موجودة.');
        }

        $patients = $this->encounterService->getAllPatients();
        $doctors = $this->encounterService->getAllDoctors();

        return view('encounters.edit', compact('encounter', 'patients', 'doctors'));
    }

    public function update(EncounterRequest $request, $id)
    {
        $result = $this->encounterService->updateEncounter($request->validated(), $id);

        return isset($result['message'])
            ? redirect()->route('encounters.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء تحديث بيانات الزيارة الطبية.');
    }

    public function destroy($id)
    {
        $result = $this->encounterService->destroyEncounter($id);

        return isset($result['message'])
            ? redirect()->route('encounters.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء حذف الزيارة الطبية.');
    }
}
