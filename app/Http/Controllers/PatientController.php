<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PatientRequest;
use App\Services\PatientService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PatientController extends Controller
{
    use AuthorizesRequests;

    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index(Request $request)
    {
        $searchPatient = $request->query('search');
        $perPagePatient = $request->query('perPage', 10);

        $patients = $this->patientService->indexPatient($searchPatient, $perPagePatient);

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(PatientRequest $request)
    {
        $result = $this->patientService->storePatient($request->validated());

        return isset($result['message'])
            ? redirect()->route('patients.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء المريض.');
    }

    public function show($id)
    {
        $patient = $this->patientService->editPatient($id);

        if (!$patient) {
            return redirect()->route('patients.index')->with('error', 'المريض غير موجود.');
        }

        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = $this->patientService->editPatient($id);

        if (!$patient) {
            return redirect()->route('patients.index')->with('error', 'المريض غير موجود.');
        }

        return view('patients.edit', compact('patient'));
    }

    public function update(PatientRequest $request, $id)
    {
        $result = $this->patientService->updatePatient($request->validated(), $id);

        return isset($result['message'])
            ? redirect()->route('patients.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء تحديث بيانات المريض.');
    }

    public function destroy($id)
    {
        $result = $this->patientService->destroyPatient($id);

        return isset($result['message'])
            ? redirect()->route('patients.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء حذف المريض.');
    }
}
