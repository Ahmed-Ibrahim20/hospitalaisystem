<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use App\Services\RoleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    protected $userService;
    protected $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public function index(Request $request)
    {
        $searchUser = $request->query('search');
        $perPageUser = $request->query('perPage', 10);

        $users = $this->userService->indexUser($searchUser, $perPageUser);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();
        return view('users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        // $this->authorize('create', \App\Models\User::class);

        $result = $this->userService->storeUser($request->validated());

        return isset($result['message'])
            ? redirect()->route('users.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء المستخدم.');
    }

    public function show($id)
    {
        $user = $this->userService->editUser($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }

        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = $this->userService->editUser($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }

        $roles = $this->roleService->getAllRoles();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, $id)
    {
        // $this->authorize('update', \App\Models\User::class);

        $result = $this->userService->updateUser($request->validated(), $id);

        return isset($result['message'])
            ? redirect()->route('users.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء تحديث المستخدم.');
    }

    public function destroy($id)
    {
        // $this->authorize('delete', \App\Models\User::class);

        $result = $this->userService->destroyUser($id);

        return isset($result['message'])
            ? redirect()->route('users.index')->with('success', $result['message'])
            : redirect()->back()->with('error', 'حدث خطأ أثناء حذف المستخدم.');
    }
}
