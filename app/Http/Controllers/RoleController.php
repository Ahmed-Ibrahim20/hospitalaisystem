<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        $searchRole = $request->query('search');
        $perPageRole = $request->query('perPage', 10);

        $roles = $this->roleService->indexRole($searchRole, $perPageRole);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(RoleRequest $request)
    {
        $result = $this->roleService->storeRole($request->validated());

        return isset($result['message'])
            ? redirect()->route('roles.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء الدور.');
    }

    public function show($id)
    {
        $role = $this->roleService->editRole($id);

        if (!$role) {
            return redirect()->route('roles.index')->with('error', 'الدور غير موجود.');
        }

        return view('roles.show', compact('role'));
    }

    public function edit($id)
    {
        $role = $this->roleService->editRole($id);

        if (!$role) {
            return redirect()->route('roles.index')->with('error', 'الدور غير موجود.');
        }

        return view('roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, $id)
    {
        $result = $this->roleService->updateRole($request->validated(), $id);

        return isset($result['message'])
            ? redirect()->route('roles.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء تحديث الدور.');
    }

    public function destroy($id)
    {
        $result = $this->roleService->destroyRole($id);

        return isset($result['message'])
            ? redirect()->route('roles.index')->with($result['status'] ? 'success' : 'error', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء حذف الدور.');
    }
}
