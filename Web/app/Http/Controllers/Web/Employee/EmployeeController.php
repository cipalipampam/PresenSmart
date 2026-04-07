<?php

namespace App\Http\Controllers\Web\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Web\EmployeeService;
use App\Http\Requests\Web\Employee\StoreEmployeeRequest;
use App\Http\Requests\Web\Employee\UpdateEmployeeRequest;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        // Get users with roles 'guru' or 'staff'
        $query = User::with(['employee', 'roles'])->whereHas('roles', function($q) {
            $q->whereIn('name', ['guru', 'staff']);
        });

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($eq) use ($search) {
                      $eq->where('employee_number', 'like', "%{$search}%");
                  });
            });
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 10));

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->employeeService->createEmployee($request->validated());
        return redirect()->route('admin.employees.index')->with('success', 'Employee successfully created');
    }

    public function show($id)
    {
        $employee = User::with(['employee'])->findOrFail($id);
        return view('admin.employees.detail', compact('employee'));
    }

    public function edit($id)
    {
        $employee = User::with(['employee', 'roles'])->findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->employeeService->updateEmployee($user, $request->validated());
        return redirect()->route('admin.employees.index')->with('success', 'Employee successfully updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->employeeService->deleteEmployee($user);
        return redirect()->route('admin.employees.index')->with('success', 'Employee successfully deleted');
    }
}
