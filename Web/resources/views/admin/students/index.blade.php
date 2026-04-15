@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1">Student Management</h2>
            <p class="text-white-50 small mb-0">Manage student profiles, academic data, and credentials.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.students.create') }}" class="btn btn-primary shadow-sm border-0 px-4">
                <i class="bi bi-person-plus-fill me-2"></i>Add Student
            </a>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER ===== --}}
    <div class="card glass border-0 shadow-lg mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.students.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-12">
                    <label for="search" class="form-label text-white-50 small fw-semibold">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Search name, NISN, or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="grade" class="form-label text-white-50 small fw-semibold">Filter Grade</label>
                    <select name="grade" id="grade" class="form-select">
                        <option value="">All Grades</option>
                        @if(isset($grades))
                            @foreach($grades as $g)
                                <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="per_page" class="form-label text-white-50 small fw-semibold">Show</label>
                    <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="10"  {{ request('per_page', 10) == 10  ? 'selected' : '' }}>10 Rows</option>
                        <option value="25"  {{ request('per_page') == 25 ? 'selected' : '' }}>25 Rows</option>
                        <option value="50"  {{ request('per_page') == 50 ? 'selected' : '' }}>50 Rows</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search me-1"></i>Apply
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn border-0 bg-light-soft text-white flex-fill">
                            Reset
                        </a>
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
                        <th class="sortable">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort'=>'name','direction'=>request('sort')=='name'&&request('direction')=='asc'?'desc':'asc'])) }}"
                               class="{{ request('sort')=='name' ? 'active-sort' : '' }}">
                                Student Profile
                                <i class="bi bi-arrow-down-up ms-1" style="font-size:0.7rem;"></i>
                            </a>
                        </th>
                        <th>NISN</th>
                        <th class="sortable">
                            <a href="{{ route('admin.students.index', array_merge(request()->query(), ['sort'=>'grade','direction'=>request('sort')=='grade'&&request('direction')=='asc'?'desc':'asc'])) }}"
                               class="{{ request('sort')=='grade' ? 'active-sort' : '' }}">
                                Grade
                                <i class="bi bi-arrow-down-up ms-1" style="font-size:0.7rem;"></i>
                            </a>
                        </th>
                        <th>Contact</th>
                        <th>Gender</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($students as $user)
                        <tr>
                            <td class="ps-4 text-white-50">
                                {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if(isset($user->student->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->student->profile_picture))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->student->profile_picture) }}"
                                             alt="Profile" class="avatar-img">
                                    @else
                                        <div class="avatar-placeholder">
                                            <i class="bi bi-person"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="fw-semibold text-white">{{ $user->name }}</div>
                                        <div class="text-white-50 small">ID: {{ $user->student->nis ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-white fw-medium">{{ $user->student->nisn ?? '-' }}</div>
                                <div class="text-white-50 small" style="font-size:0.72rem;">NISN</div>
                            </td>
                            <td>
                                @if($user->student->grade ?? false)
                                    <span class="badge px-2 py-1" style="background:rgba(6,182,212,0.1);color:#06b6d4;border:1px solid rgba(6,182,212,0.2);">
                                        {{ $user->student->grade }}
                                    </span>
                                @else
                                    <span class="text-white-50 small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-white-50 small">
                                    <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                                </div>
                            </td>
                            <td>
                                @if(isset($user->student->gender) && $user->student->gender == 'male')
                                    <span class="text-cyan small fw-semibold"><i class="bi bi-gender-male me-1"></i>Male</span>
                                @elseif(isset($user->student->gender) && $user->student->gender == 'female')
                                    <span class="small fw-semibold" style="color:#f472b6;"><i class="bi bi-gender-female me-1"></i>Female</span>
                                @else
                                    <span class="text-white-50 small">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <a href="{{ route('admin.students.show', $user->id) }}"
                                       class="btn btn-action btn-action-view" title="View Details"
                                       data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.students.edit', $user->id) }}"
                                       class="btn btn-action btn-action-edit" title="Edit"
                                       data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button"
                                       class="btn btn-action btn-action-delete" title="Delete"
                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                       data-delete-url="{{ route('admin.students.destroy', $user->id) }}"
                                       data-delete-name="{{ $user->name }}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="mb-3"><i class="bi bi-people fs-1 text-white-25"></i></div>
                                <h6 class="text-white-50 fw-medium">No students found</h6>
                                <p class="text-muted small mb-3">Try clearing filters or add a new student.</p>
                                <a href="{{ route('admin.students.create') }}" class="btn btn-sm btn-primary px-4">
                                    <i class="bi bi-person-plus-fill me-1"></i>Add Student
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer bg-transparent border-0 p-4 border-top border-secondary border-opacity-10 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-white-50 small">
                Showing <span class="text-white fw-bold">{{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }}</span>
                of total <span class="text-white fw-bold">{{ $students->total() }}</span> students
            </div>
            <div class="pagination-modern">
                {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
