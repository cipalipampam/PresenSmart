<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Web\StudentService;
use App\Http\Requests\Web\Student\StoreStudentRequest;
use App\Http\Requests\Web\Student\UpdateStudentRequest;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $query = User::with(['student', 'roles'])->role('siswa');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('national_student_number', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 10));

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request)
    {
        $this->studentService->createStudent($request->validated());
        return redirect()->route('admin.students.index')->with('success', 'Student successfully created');
    }

    public function show($id)
    {
        $student = User::with(['student'])->findOrFail($id);
        return view('admin.students.detail', compact('student'));
    }

    public function edit($id)
    {
        $student = User::with(['student'])->findOrFail($id);
        return view('admin.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->studentService->updateStudent($user, $request->validated());
        return redirect()->route('admin.students.index')->with('success', 'Student successfully updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->studentService->deleteStudent($user);
        return redirect()->route('admin.students.index')->with('success', 'Student successfully deleted');
    }
}
