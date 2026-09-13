<?php

namespace App\Http\Controllers\Web\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Attendance\ResolveAttendanceRequest;
use App\Http\Requests\Web\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Web\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\Services\Shared\Storage\AttendanceProofStorage;
use App\Services\Web\Attendance\AdminAttendanceService;
use App\Services\Web\Attendance\AttendanceExportService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AdminAttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceExportService $exportService,
        private readonly AdminAttendanceService $attendanceService,
    ) {}

    public function index(Request $request)
    {
        return $this->renderIndex($request);
    }

    public function students(Request $request)
    {
        return $this->renderIndex($request, 'siswa');
    }

    public function employees(Request $request)
    {
        return $this->renderIndex($request, 'employee');
    }

    public function show(int $id)
    {
        $attendance = Attendance::with(['user.student', 'user.employee'])->findOrFail($id);
        $attendance->proof_url = $attendance->proof_image
            ? route('admin.attendances.proof', $attendance)
            : null;

        return view('admin.attendances.detail', compact('attendance'));
    }

    public function proof(int $id, AttendanceProofStorage $proofStorage)
    {
        return $proofStorage->response(Attendance::findOrFail($id));
    }

    public function create(Request $request)
    {
        $users = User::with(['student', 'employee'])->orderBy('name')->get();
        $tanggal = $request->query('date', now()->toDateString());

        return view('admin.attendances.create', compact('users', 'tanggal'));
    }

    public function store(StoreAttendanceRequest $request)
    {
        $this->attendanceService->create($request->validated());

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance recorded successfully.');
    }

    public function approve(ResolveAttendanceRequest $request, int $id)
    {
        $this->attendanceService->resolve(Attendance::findOrFail($id), $request->validated('action'));

        $message = $request->action === 'approve'
            ? 'Permohonan berhasil disetujui (Approved).'
            : 'Permohonan ditolak. Status otomatis menjadi Absent (Alfa).';

        return redirect()->back()->with('success', $message);
    }

    public function edit(int $id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $users = User::orderBy('name')->get();

        return view('admin.attendances.edit', compact('attendance', 'users'));
    }

    public function update(UpdateAttendanceRequest $request, int $id)
    {
        $this->attendanceService->update(Attendance::findOrFail($id), $request->validated());

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->attendanceService->delete(Attendance::findOrFail($id));

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance deleted successfully.');
    }

    public function print(Request $request)
    {
        $filters = $this->filters($request);
        $attendances = $this->attendanceQuery($filters)->orderByDesc('recorded_at')->get();

        return view('admin.attendances.print', array_merge(compact('attendances'), $filters));
    }

    private function renderIndex(Request $request, ?string $attendanceType = null)
    {
        $filters = $this->filters($request);
        $query = $this->attendanceQuery($filters, $attendanceType);

        if (in_array($request->input('export'), ['excel', 'csv', 'pdf', 'zip'], true)) {
            return $this->exportService->export($request->input('export'), clone $query);
        }

        $perPage = min(max((int) $request->input('per_page', 10), 10), 100);
        $attendances = $query->orderByDesc('recorded_at')->paginate($perPage);
        $attendances->appends($filters + ['per_page' => $perPage]);
        $grades = Student::query()->whereNotNull('grade')->distinct()->pluck('grade')->sort();

        return view('admin.attendances.index', array_merge(compact('attendances', 'grades'), $filters, [
            'attendanceType' => $attendanceType,
            'attendanceRouteName' => match ($attendanceType) {
                'siswa' => 'admin.attendances.students',
                'employee' => 'admin.attendances.employees',
                default => 'admin.attendances.index',
            },
        ]));
    }

    private function filters(Request $request): array
    {
        return [
            'search' => $request->string('search')->trim()->toString(),
            'date' => $request->has('date') ? $request->input('date') : now()->toDateString(),
            'month' => $request->integer('month') ?: null,
            'year' => $request->integer('year') ?: null,
            'role' => $request->input('role'),
            'grade' => $request->input('grade'),
        ];
    }

    private function attendanceQuery(array $filters, ?string $roleOverride = null): Builder
    {
        $query = Attendance::with(['user.student', 'user.employee']);

        if ($filters['search'] !== '') {
            $query->whereHas('user', fn (Builder $query) => $query->where('name', 'like', "%{$filters['search']}%"));
        }

        if ($filters['date']) {
            $date = Carbon::parse($filters['date'])->startOfDay();
            $query->where('recorded_at', '>=', $date)->where('recorded_at', '<', $date->copy()->addDay());
        } elseif ($filters['month'] || $filters['year']) {
            $start = Carbon::create($filters['year'] ?: now()->year, $filters['month'] ?: now()->month)->startOfMonth();
            $query->where('recorded_at', '>=', $start)->where('recorded_at', '<', $start->copy()->addMonth());
        }

        $role = $roleOverride ?: $filters['role'];
        if ($role) {
            $query->whereHas('user', fn (Builder $query) => $role === 'employee'
                ? $query->role(['guru', 'staff'])
                : $query->role($role));
        }

        if ($filters['grade']) {
            $query->whereHas('user.student', fn (Builder $query) => $query->where('grade', $filters['grade']));
        }

        return $query;
    }
}
