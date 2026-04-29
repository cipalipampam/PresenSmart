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
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('nisn', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('grade') && $request->grade != '') {
            $grade = $request->grade;
            $query->whereHas('student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        // Handle sorting
        if ($request->has('sort') && in_array($request->sort, ['name', 'grade'])) {
            $direction = $request->direction === 'desc' ? 'desc' : 'asc';
            
            if ($request->sort === 'name') {
                $query->orderBy('name', $direction);
            } else if ($request->sort === 'grade') {
                // Sorting by a relationship column requires joining or subquery in Laravel.
                // Alternatively, we can join the students table.
                $query->join('students', 'users.id', '=', 'students.user_id')
                      ->orderBy('students.grade', $direction)
                      ->select('users.*'); // Ensure we select users' columns
            }
        } else {
            $query->orderBy('users.created_at', 'desc');
        }

        $students = $query->paginate($request->input('per_page', 10));
        
        $grades = \App\Models\Student::select('grade')->whereNotNull('grade')->distinct()->pluck('grade');

        return view('admin.students.index', compact('students', 'grades'));
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
