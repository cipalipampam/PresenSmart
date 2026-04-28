<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\Services\Web\Attendance\AttendanceExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAttendanceController extends Controller
{
    public function __construct(private AttendanceExportService $exportService) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Index — list with filters + export
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $filters = $this->getFilters($request);
        $query   = $this->buildAttendanceQuery($filters);

        // Multi-format export
        $exportType = $request->input('export');
        if (in_array($exportType, ['excel', 'csv', 'pdf', 'zip'])) {
            return $this->exportService->export($exportType, clone $query);
        }

        $perPage     = $request->input('per_page', 10);
        $attendances = $query->orderBy('recorded_at', 'desc')->paginate($perPage);
        $attendances->appends($filters + ['per_page' => $perPage]);

        $grades = Student::distinct()->whereNotNull('grade')->pluck('grade')->sort();

        return view('admin.attendances.index', array_merge(
            compact('attendances', 'grades'),
            $filters
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CRUD
    // ─────────────────────────────────────────────────────────────────────────

    public function show($id)
    {
        $attendance = Attendance::with(['user.student', 'user.employee'])->findOrFail($id);
        $attendance->proof_url = $attendance->proof_image
            ? url('storage/' . $attendance->proof_image)
            : null;

        return view('admin.attendances.detail', compact('attendance'));
    }

    public function create(Request $request)
    {
        $users   = User::with(['student', 'employee'])->orderBy('name')->get();
        $tanggal = $request->query('date', now()->toDateString());
        return view('admin.attendances.create', compact('users', 'tanggal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'recorded_at' => 'required|date',
            'status'      => 'required|in:present,absent,sick,permission',
            'notes'       => 'nullable|string|max:500',
            'proof_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $file      = $request->file('proof_image');
            $fileName  = time() . '_' . $request->user_id . '.' . $file->getClientOriginalExtension();
            $proofPath = $file->storeAs('attendances', $fileName, 'public'); // ← unified path
        }

        Attendance::create([
            'user_id'     => $request->user_id,
            'recorded_at' => $request->recorded_at,
            'status'      => $request->status,
            'notes'       => $request->notes,
            'proof_image' => $proofPath,
        ]);

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance recorded successfully.');
    }

    public function approve(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate(['action' => 'required|in:approve,reject']);

        if ($request->action === 'approve') {
            $attendance->update(['is_approved' => true]);
            event(new \App\Events\AttendanceApproved($attendance, 'Pengajuan absensi Anda telah disetujui.'));
            event(new \App\Events\DashboardStatsUpdated());
            return redirect()->back()->with('success', 'Permohonan berhasil disetujui (Approved).');
        }

        $attendance->update([
            'is_approved' => false,
            'status'      => 'absent',
        ]);
        event(new \App\Events\AttendanceApproved($attendance, 'Pengajuan absensi Anda ditolak.'));
        event(new \App\Events\DashboardStatsUpdated());
        return redirect()->back()->with('success', 'Permohonan ditolak. Status otomatis menjadi Absent (Alfa).');
    }

    public function edit($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $users      = User::orderBy('name')->get();
        return view('admin.attendances.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'status'      => 'required|in:present,absent,sick,permission',
            'notes'       => 'nullable|string|max:500',
            'proof_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if ($request->hasFile('proof_image')) {
            if ($attendance->proof_image) {
                Storage::disk('public')->delete($attendance->proof_image);
            }
            $file      = $request->file('proof_image');
            $fileName  = time() . '_' . $attendance->user_id . '.' . $file->getClientOriginalExtension();
            $attendance->proof_image = $file->storeAs('attendances', $fileName, 'public'); // ← unified path
        }

        $attendance->status = $request->status;
        $attendance->notes  = $request->notes;
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

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance deleted successfully.');
    }

    public function print(Request $request)
    {
        $filters     = $this->getFilters($request);
        $attendances = $this->buildAttendanceQuery($filters, defaultToday: true)
            ->orderBy('recorded_at', 'desc')
            ->get();

        return view('admin.attendances.print', array_merge(compact('attendances'), $filters));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Shared helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Extract all filter parameters from the request into a consistent array.
     */
    private function getFilters(Request $request): array
    {
        return [
            'search' => $request->input('search', ''),
            'date'   => $request->input('date'),
            'month'  => $request->input('month'),
            'year'   => $request->input('year', date('Y')),
            'role'   => $request->input('role'),
            'grade'  => $request->input('grade'),
        ];
    }

    /**
     * Build the Eloquent query applying all attendance filters.
     * $defaultToday: when no date/month/year given, default to today instead of current month.
     */
    private function buildAttendanceQuery(array $filters, bool $defaultToday = false): \Illuminate\Database\Eloquent\Builder
    {
        $query = Attendance::with(['user.student', 'user.employee']);

        // Search by name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        // Date / month / year filter
        if (!empty($filters['date'])) {
            $query->whereDate('recorded_at', $filters['date']);
        } else {
            if (!empty($filters['month'])) {
                $query->whereMonth('recorded_at', $filters['month']);
            }
            if (!empty($filters['year'])) {
                $query->whereYear('recorded_at', $filters['year']);
            }

            // Default fallback when nothing is provided
            if (empty($filters['date']) && empty($filters['month']) && empty($filters['year'])) {
                if ($defaultToday) {
                    $query->whereDate('recorded_at', now()->toDateString());
                } else {
                    $query->whereMonth('recorded_at', date('m'))
                          ->whereYear('recorded_at', date('Y'));
                }
            }
        }

        // Role filter
        if (!empty($filters['role'])) {
            $role = $filters['role'];
            $query->whereHas('user', function ($q) use ($role) {
                $role === 'employee'
                    ? $q->role(['guru', 'staff'])
                    : $q->role($role);
            });
        }

        // Grade filter (students only)
        if (!empty($filters['grade'])) {
            $grade = $filters['grade'];
            $query->whereHas('user.student', fn($q) => $q->where('grade', $grade));
        }

        return $query;
    }
}
