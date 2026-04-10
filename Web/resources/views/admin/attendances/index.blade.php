@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Attendance Tracking</h2>
        <a href="{{ route('admin.attendances.create') }}" class="btn btn-success shadow-sm">
            <i class="bi bi-plus-circle me-1"></i>Manual Attendance Input
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('admin.attendances.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="search" class="form-label">Search Name</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="date" class="form-label">Specific Date</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-select">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            @php $mVal = sprintf('%02d', $m); @endphp
                            <option value="{{ $mVal }}" {{ request('month', date('m')) == $mVal && !request('date') ? 'selected' : (request('month') == $mVal ? 'selected' : '') }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-select">
                        @php $currentYear = date('Y'); @endphp
                        @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                            <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" onchange="toggleGradeFilter()">
                        <option value="">All Roles</option>
                        <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Student</option>
                        <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee (Teacher/Staff)</option>
                        <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Teacher</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div class="col-md-1" id="grade-filter-container" style="{{ request('role') == 'siswa' ? '' : 'display: none;' }}">
                    <label for="grade" class="form-label">Grade</label>
                    <select name="grade" id="grade" class="form-select">
                        <option value="">All</option>
                        @foreach($grades as $g)
                            <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-filter"></i>
                        </button>
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleGradeFilter() {
            const roleSelect = document.getElementById('role');
            const gradeContainer = document.getElementById('grade-filter-container');
            if (roleSelect.value === 'siswa') {
                gradeContainer.style.display = 'block';
            } else {
                gradeContainer.style.display = 'none';
                document.getElementById('grade').value = '';
            }
        }
    </script>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Employee / Student Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Time Recorded</th>
                            <th>Photo Proof / Document</th>
                            <th>Notes</th>
                            <th class="text-center pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $attendance)
                            <tr>
                                <td class="ps-4">{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold">{{ $attendance->user->name }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($attendance->user->hasRole('siswa'))
                                        <span class="badge bg-primary">Student</span>
                                    @elseif($attendance->user->hasRole('guru'))
                                        <span class="badge bg-success">Teacher</span>
                                    @elseif($attendance->user->hasRole('staff'))
                                        <span class="badge bg-secondary">Staff</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->status == 'present')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Present</span>
                                    @elseif($attendance->status == 'permission' || $attendance->status == 'sick')
                                        @if($attendance->is_approved === null)
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending {{ ucfirst($attendance->status) }}</span>
                                        @elseif($attendance->is_approved == true)
                                            <span class="badge bg-primary"><i class="bi bi-envelope-paper me-1"></i>Approved {{ ucfirst($attendance->status) }}</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-square me-1"></i>Rejected</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Absent</span>
                                    @endif
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d M Y - H:i') }}
                                </td>
                                <td>
                                    @if($attendance->proof_image)
                                        <a href="{{ Storage::url($attendance->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-image me-1"></i>View File
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic">None</span>
                                    @endif
                                </td>
                                <td>
                                    {{ Str::limit($attendance->notes ?? '-', 20) }}
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if(($attendance->status == 'permission' || $attendance->status == 'sick') && $attendance->is_approved === null)
                                            <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" title="Approve">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" title="Reject">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-sm btn-warning" title="Edit Attendance">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Record">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    No attendance records found for the selected filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white border-0 py-3 ps-4 pe-4 d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }} records
                </div>
                <div>
                    {{ $attendances->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
