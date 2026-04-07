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
        $tanggal = $request->input('date', now()->toDateString());
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = Attendance::with(['user.student', 'user.employee'])
            ->whereDate('recorded_at', $tanggal);

        if (!empty($search)) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderBy('recorded_at', 'desc')->paginate($perPage);
        $attendances->appends(['date' => $tanggal, 'search' => $search, 'per_page' => $perPage]);

        return view('admin.attendances.index', compact('attendances', 'tanggal'));
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
        $tanggal = $request->input('date', now()->toDateString());
        $attendances = Attendance::with(['user.student', 'user.employee'])
            ->whereDate('recorded_at', $tanggal)
            ->get();

        return view('admin.attendances.print', [
            'attendances' => $attendances,
            'tanggal' => $tanggal,
        ]);
    }
}
