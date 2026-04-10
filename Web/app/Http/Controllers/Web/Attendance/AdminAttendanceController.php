<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $date = $request->input('date');
        $month = $request->input('month');
        $year = $request->input('year', date('Y'));
        $role = $request->input('role');
        $grade = $request->input('grade');
        $perPage = $request->input('per_page', 10);

        $query = Attendance::with(['user.student', 'user.employee']);

        // Search by Name
        if (!empty($search)) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by Date or Month/Year
        if (!empty($date)) {
            $query->whereDate('recorded_at', $date);
        } else {
            if (!empty($month)) {
                $query->whereMonth('recorded_at', $month);
            }
            if (!empty($year)) {
                $query->whereYear('recorded_at', $year);
            }
            
            // If No Date and No Month are provided, default to current month
            if (empty($date) && empty($month) && empty($request->input('year'))) {
                $query->whereMonth('recorded_at', date('m'))
                      ->whereYear('recorded_at', date('Y'));
            }
        }

        // Filter by Role
        if (!empty($role)) {
            $query->whereHas('user', function($q) use ($role) {
                if ($role === 'employee') {
                    $q->role(['guru', 'staff']);
                } else {
                    $q->role($role);
                }
            });
        }

        // Filter by Grade (Students only)
        if (!empty($grade)) {
            $query->whereHas('user.student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        $attendances = $query->orderBy('recorded_at', 'desc')->paginate($perPage);
        
        $attendances->appends([
            'search' => $search,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'role' => $role,
            'grade' => $grade,
            'per_page' => $perPage
        ]);

        $grades = \App\Models\Student::distinct()->whereNotNull('grade')->pluck('grade')->sort();

        return view('admin.attendances.index', compact('attendances', 'search', 'date', 'month', 'year', 'role', 'grade', 'grades'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user.student', 'user.employee'])->findOrFail($id);
        $attendance->proof_url = $attendance->proof_image ? url('storage/' . $attendance->proof_image) : null;
            
        return view('admin.attendances.detail', compact('attendance'));
    }

    public function create(Request $request)
    {
        $users = User::with(['student', 'employee'])->orderBy('name')->get();
        $tanggal = $request->query('date', now()->toDateString());
        return view('admin.attendances.create', compact('users', 'tanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'recorded_at' => 'required|date',
            'status' => 'required|in:present,absent,sick,permission',
            'notes' => 'nullable|string|max:500',
            'proof_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $fileName = time() . '_' . $request->user_id . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('attendances', $fileName, 'public');
        }

        Attendance::create([
            'user_id' => $request->user_id,
            'recorded_at' => $request->recorded_at,
            'status' => $request->status,
            'notes' => $request->notes,
            'proof_image' => $proofPath,
        ]);

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance recorded successfully.');
    }

    public function approve(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        if ($request->action == 'approve') {
            $attendance->update(['is_approved' => true]);
            return redirect()->back()->with('success', 'Permohonan berhasil disetujui (Approved).');
        } elseif ($request->action == 'reject') {
            $attendance->update([
                'is_approved' => false,
                'status' => 'absent'
            ]);
            return redirect()->back()->with('success', 'Permohonan ditolak. Status otomatis menjadi Absent (Alfa).');
        }
    }

    public function edit($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $users = User::orderBy('name')->get();
        return view('admin.attendances.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'status' => 'required|in:present,absent,sick,permission',
            'notes' => 'nullable|string|max:500',
            'proof_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        if ($request->hasFile('proof_image')) {
            $file = $request->file('proof_image');
            $fileName = time() . '_' . $attendance->user_id . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('proof_attendances', $fileName, 'public');
            
            if ($attendance->proof_image) {
                Storage::disk('public')->delete($attendance->proof_image);
            }
            $attendance->proof_image = $proofPath;
        }

        $attendance->status = $request->status;
        $attendance->notes = $request->notes;
        $attendance->save();

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        if ($attendance->proof_image) {
            Storage::disk('public')->delete($attendance->proof_image);
        }

        $attendance->delete();

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance deleted successfully');
    }

    public function print(Request $request)
    {
        $search = $request->input('search', '');
        $date = $request->input('date');
        $month = $request->input('month');
        $year = $request->input('year');
        $role = $request->input('role');
        $grade = $request->input('grade');

        $query = Attendance::with(['user.student', 'user.employee']);

        if (!empty($search)) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (!empty($date)) {
            $query->whereDate('recorded_at', $date);
        } else {
            if (!empty($month)) {
                $query->whereMonth('recorded_at', $month);
            }
            if (!empty($year)) {
                $query->whereYear('recorded_at', $year);
            }
            if (empty($date) && empty($month) && empty($year)) {
                $query->whereDate('recorded_at', now()->toDateString());
            }
        }

        if (!empty($role)) {
            $query->whereHas('user', function($q) use ($role) {
                if ($role === 'employee') {
                    $q->role(['guru', 'staff']);
                } else {
                    $q->role($role);
                }
            });
        }

        if (!empty($grade)) {
            $query->whereHas('user.student', function($q) use ($grade) {
                $q->where('grade', $grade);
            });
        }

        $attendances = $query->orderBy('recorded_at', 'desc')->get();

        return view('admin.attendances.print', compact('attendances', 'date', 'month', 'year', 'role', 'grade'));
    }
}
