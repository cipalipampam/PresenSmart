@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1">Attendance Records</h2>
            <p class="text-white-50 small mb-0">Monitor and manage daily attendance logs for all members.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
            {{-- Export Dropdown --}}
            <div class="dropdown">
                <button class="btn border-0 bg-light-soft text-white dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-2"></i>Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end glass border-0 shadow py-2">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.attendances.index', array_merge(request()->query(), ['export' => 'excel'])) }}">
                            <i class="bi bi-file-earmark-excel me-2 text-emerald"></i>Excel (.xlsx)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.attendances.index', array_merge(request()->query(), ['export' => 'pdf'])) }}">
                            <i class="bi bi-file-earmark-pdf me-2" style="color:#ef4444;"></i>PDF
                        </a>
                    </li>
                </ul>
            </div>
            <a href="{{ route('admin.attendances.create') }}" class="btn btn-primary shadow-sm border-0">
                <i class="bi bi-plus-lg me-2"></i>Record Manual Attendance
            </a>
        </div>
    </div>

    {{-- ===== FILTER CARD ===== --}}
    <div class="card glass border-0 shadow-lg mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.attendances.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label for="search" class="form-label text-white-50 small fw-semibold">Search Member</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Member name..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="date" class="form-label text-white-50 small fw-semibold">Date</label>
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
                                <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4" id="grade-filter-container" style="{{ request('role') == 'siswa' ? '' : 'display:none;' }}">
                        <label for="grade" class="form-label text-white-50 small fw-semibold">Grade</label>
                        <select name="grade" id="grade" class="form-select">
                            <option value="">All</option>
                            @foreach($grades as $g)
                                <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="role" class="form-label text-white-50 small fw-semibold">Role Filter</label>
                        <select name="role" id="role" class="form-select" onchange="toggleGradeFilter()">
                            <option value="">All Members</option>
                            <option value="siswa"    {{ request('role') == 'siswa'    ? 'selected' : '' }}>Students</option>
                            <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>All Employees</option>
                            <option value="guru"     {{ request('role') == 'guru'     ? 'selected' : '' }}>Teachers</option>
                            <option value="staff"    {{ request('role') == 'staff'    ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <a href="{{ route('admin.attendances.index') }}" class="btn border-0 bg-light-soft text-white flex-fill">
                                <i class="bi bi-x-lg me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert border-0 d-flex align-items-center mb-4" role="alert"
             style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2)!important;border-radius:10px;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="filter:invert(1);opacity:0.5;"></button>
        </div>
    @endif

    {{-- ===== DATA TABLE ===== --}}
    <div class="card border-0 shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width:50px;">No</th>
                        <th>Member Name</th>
                        <th>Role</th>
                        <th>Attendance Status</th>
                        <th>Time</th>
                        <th>Proof</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="ps-4 text-white-50">
                                {{ ($attendances->currentPage() - 1) * $attendances->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light-soft rounded-circle text-white d-flex align-items-center justify-content-center fw-bold"
                                         style="width:34px;height:34px;font-size:0.8rem;flex-shrink:0;">
                                        {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold text-white">{{ $attendance->user->name }}</span>
                                </div>
                            </td>
                            <td>
                                @if($attendance->user->hasRole('siswa'))
                                    <span class="badge px-2 py-1" style="background:rgba(6,182,212,0.1);color:#06b6d4;border:1px solid rgba(6,182,212,0.2);">Student</span>
                                @elseif($attendance->user->hasRole('guru'))
                                    <span class="badge px-2 py-1" style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);">Teacher</span>
                                @elseif($attendance->user->hasRole('staff'))
                                    <span class="badge px-2 py-1" style="background:rgba(148,163,184,0.1);color:#94a3b8;border:1px solid rgba(148,163,184,0.2);">Staff</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status == 'present')
                                    <div class="d-flex align-items-center gap-2" style="color:#10b981;">
                                        <div class="rounded-circle" style="width:7px;height:7px;background:#10b981;flex-shrink:0;"></div>
                                        <span class="small fw-medium">Present</span>
                                    </div>
                                @elseif($attendance->status == 'permission' || $attendance->status == 'sick')
                                    @if($attendance->is_approved === null)
                                        <div class="d-flex align-items-center gap-2" style="color:#f59e0b;">
                                            <div class="rounded-circle" style="width:7px;height:7px;background:#f59e0b;flex-shrink:0;"></div>
                                            <span class="small fw-medium">Pending — {{ $attendance->status == 'sick' ? 'Sick' : 'Permission' }}</span>
                                        </div>
                                    @elseif($attendance->is_approved == true)
                                        <div class="d-flex align-items-center gap-2" style="color:#06b6d4;">
                                            <div class="rounded-circle" style="width:7px;height:7px;background:#06b6d4;flex-shrink:0;"></div>
                                            <span class="small fw-medium">Approved — {{ $attendance->status == 'sick' ? 'Sick' : 'Permission' }}</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center gap-2" style="color:#ef4444;">
                                            <div class="rounded-circle" style="width:7px;height:7px;background:#ef4444;flex-shrink:0;"></div>
                                            <span class="small fw-medium">Rejected</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="d-flex align-items-center gap-2" style="color:#ef4444;">
                                        <div class="rounded-circle" style="width:7px;height:7px;background:#ef4444;flex-shrink:0;"></div>
                                        <span class="small fw-medium">Absent</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-white-50 small">
                                {{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d M, H:i') }}
                            </td>
                            <td>
                                @if($attendance->proof_image)
                                    <a href="{{ Storage::url($attendance->proof_image) }}" target="_blank"
                                       class="btn btn-sm border-0 px-3 py-1 rounded-pill"
                                       style="background:rgba(6,182,212,0.1);color:#06b6d4;font-size:0.78rem;">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                @else
                                    <span class="text-white-50 small fst-italic">None</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    @if(($attendance->status == 'permission' || $attendance->status == 'sick') && $attendance->is_approved === null)
                                        <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" name="action" value="approve"
                                                class="btn btn-action btn-action-approve" title="Approve">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" name="action" value="reject"
                                                class="btn btn-action btn-action-reject" title="Reject">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.attendances.edit', $attendance->id) }}"
                                       class="btn btn-action btn-action-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button"
                                       class="btn btn-action btn-action-delete" title="Delete"
                                       data-delete-url="{{ route('admin.attendances.destroy', $attendance->id) }}"
                                       data-delete-name="attendance for {{ $attendance->user->name }} on {{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d M Y') }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="mb-3"><i class="bi bi-calendar-x fs-1 text-white-25"></i></div>
                                <h6 class="text-white-50 fw-medium">No attendance records found</h6>
                                <p class="text-muted small mb-0">Try adjusting filters to broaden your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-transparent border-0 p-4 border-top border-secondary border-opacity-10 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-white-50 small">
                Showing <span class="text-white fw-bold">{{ $attendances->firstItem() ?? 0 }}-{{ $attendances->lastItem() ?? 0 }}</span>
                from total <span class="text-white fw-bold">{{ $attendances->total() }}</span> records
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
@endsection
