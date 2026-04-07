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
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Name</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="month" class="form-label">Filter Month</label>
                    <select name="month" id="month" class="form-select">
                        <option value="">All Months</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ request('month', date('m')) == sprintf('%02d', $m) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Filter Year</label>
                    <select name="year" id="year" class="form-select">
                        @php $currentYear = date('Y'); @endphp
                        @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                            <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Apply Filter
                    </button>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
                </div>
            </form>
        </div>
    </div>

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
                                    @elseif($attendance->status == 'permission')
                                        <span class="badge bg-warning text-dark"><i class="bi bi-envelope-paper me-1"></i>Permission</span>
                                    @elseif($attendance->status == 'sick')
                                        <span class="badge bg-info text-dark"><i class="bi bi-thermometer-half me-1"></i>Sick</span>
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
                                    <div class="btn-group shadow-sm" role="group">
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
