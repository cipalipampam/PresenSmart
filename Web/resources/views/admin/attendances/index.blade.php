@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Attendance Tracking</h2>
            <p class="text-white-50 small mb-0">Manage and monitor daily attendance records across the institution.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary shadow-sm border-0">
                <i class="bi bi-plus-lg me-2"></i>Record Manual Entry
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card glass border-0 shadow-lg mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.attendances.index') }}" method="GET" class="row g-4 align-items-end">
                <div class="col-lg-2 col-md-4">
                    <label for="search" class="form-label text-white-50 small fw-semibold">Search Member</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="date" class="form-label text-white-50 small fw-semibold">Specific Day</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="month" class="form-label text-white-50 small fw-semibold">Month</label>
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
                <div class="col-lg-1 col-md-4">
                    <label for="year" class="form-label text-white-50 small fw-semibold">Year</label>
                    <select name="year" id="year" class="form-select">
                        @php $currentYear = date('Y'); @endphp
                        @for ($y = $currentYear; $y >= $currentYear - 4; $y--)
                            <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label for="role" class="form-label text-white-50 small fw-semibold">Identify Role</label>
                    <select name="role" id="role" class="form-select" onchange="toggleGradeFilter()">
                        <option value="">Full Staff & Students</option>
                        <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Students Only</option>
                        <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>All Employees</option>
                        <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Teachers Only</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff Only</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-4" id="grade-filter-container" style="{{ request('role') == 'siswa' ? '' : 'display: none;' }}">
                    <label for="grade" class="form-label text-white-50 small fw-semibold">Grade</label>
                    <select name="grade" id="grade" class="form-select">
                        <option value="">All</option>
                        @foreach($grades as $g)
                            <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-arrow-right-short fs-4"></i>
                        </button>
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary flex-fill border-0 bg-light-soft text-white">
                            <i class="bi bi-arrow-counterclockwise fs-4"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="card border-0 shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Personnel Name</th>
                        <th>Classification</th>
                        <th>Presence Status</th>
                        <th>Log Time</th>
                        <th>Documentation</th>
                        <th class="text-end pe-4">Control</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($attendances as $index => $attendance)
                        <tr>
                            <td class="ps-4 text-white-50">{{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light-soft rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                    </div>
                                    <div class="fw-semibold text-white">{{ $attendance->user->name }}</div>
                                </div>
                            </td>
                            <td>
                                @if($attendance->user->hasRole('siswa'))
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1">Student</span>
                                @elseif($attendance->user->hasRole('guru'))
                                    <span class="badge bg-emerald bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="color: #10b981 !important;">Teacher</span>
                                @elseif($attendance->user->hasRole('staff'))
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Staff</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status == 'present')
                                    <div class="d-flex align-items-center text-success">
                                        <div class="rounded-circle bg-success me-2" style="width: 8px; height: 8px;"></div>
                                        <span>Present</span>
                                    </div>
                                @elseif($attendance->status == 'permission' || $attendance->status == 'sick')
                                    @if($attendance->is_approved === null)
                                        <div class="d-flex align-items-center text-warning">
                                            <div class="rounded-circle bg-warning me-2" style="width: 8px; height: 8px;"></div>
                                            <span>Pending {{ ucfirst($attendance->status) }}</span>
                                        </div>
                                    @elseif($attendance->is_approved == true)
                                        <div class="d-flex align-items-center text-info">
                                            <div class="rounded-circle bg-info me-2" style="width: 8px; height: 8px;"></div>
                                            <span>Approved {{ ucfirst($attendance->status) }}</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-danger">
                                            <div class="rounded-circle bg-danger me-2" style="width: 8px; height: 8px;"></div>
                                            <span>Rejected</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center text-danger">
                                        <div class="rounded-circle bg-danger me-2" style="width: 8px; height: 8px;"></div>
                                        <span>Absent</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-white-50">
                                {{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d M, H:i') }}
                            </td>
                            <td>
                                @if($attendance->proof_image)
                                    <a href="{{ Storage::url($attendance->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                @else
                                    <span class="text-white-25 small fst-italic">No attachment</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    @if(($attendance->status == 'permission' || $attendance->status == 'sick') && $attendance->is_approved === null)
                                        <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0;" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0;" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-sm btn-warning rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; background: #f59e0b; border: none; color: white;" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm deletion of this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; background: #ef4444; border: none;" title="Delete">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-white-25">
                                <div class="mb-3">
                                    <i class="bi bi-search fs-1"></i>
                                </div>
                                <h5>No logs found matching those criteria</h5>
                                <p class="small mb-0">Try adjusting your filters to broaden your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-transparent border-0 p-4 border-top border-secondary border-opacity-10 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-white-50 small">
                Showing entries <span class="text-white">{{ $attendances->firstItem() ?? 0 }}-{{ $attendances->lastItem() ?? 0 }}</span> of total <span class="text-white">{{ $attendances->total() }}</span>
            </div>
            <div class="pagination-modern">
                {{ $attendances->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
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

<style>
    /* Pagination Specific Styling for Dark Theme */
    .pagination-modern .pagination {
        margin-bottom: 0;
    }
    .pagination-modern .page-link {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
        padding: 0.5rem 0.9rem;
    }
    .pagination-modern .page-item.active .page-link {
        background-color: var(--accent-primary);
        border-color: var(--accent-primary);
        color: white;
    }
    .pagination-modern .page-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: var(--text-primary);
    }
</style>
@endsection
