@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Personnel Directory</h2>
            <p class="text-white-50 small mb-0">Manage teaching staff and administrative employees.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.employees.create') }}" class="btn btn-primary shadow-sm border-0 px-4">
                <i class="bi bi-person-plus-fill me-2"></i>Register Personnel
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card glass border-0 shadow-lg mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.employees.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-lg-5 col-md-12">
                    <label for="search" class="form-label text-white-50 small fw-semibold">Directory Search</label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search by name, NIP, or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="role" class="form-label text-white-50 small fw-semibold">Role Classification</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Teachers</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff Members</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="per_page" class="form-label text-white-50 small fw-semibold">Entries</label>
                    <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Per Page</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Per Page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Per Page</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Apply</button>
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary flex-fill border-0 bg-light-soft text-white">Reset</a>
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

    <!-- Data Table -->
    <div class="card border-0 shadow-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Personnel</th>
                        <th>NIP</th>
                        <th>Position</th>
                        <th>Classification</th>
                        <th>Contact</th>
                        <th class="text-end pe-4">Control</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($employees as $index => $user)
                        <tr>
                            <td class="ps-4 text-white-50">{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if(isset($user->employee->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->employee->profile_picture))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->employee->profile_picture) }}" 
                                             alt="Profile" class="avatar-img shadow-sm">
                                    @else
                                        <div class="avatar-placeholder">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                    @endif
                                    <div class="fw-bold text-white">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="text-white fw-medium">{{ $user->employee->nip ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="text-white-50 small">{{ $user->employee->position ?? '-' }}</div>
                            </td>
                            <td>
                                @if($user->hasRole('guru'))
                                    <span class="badge bg-emerald bg-opacity-10 text-emerald border border-success border-opacity-25 px-2 py-1" style="color: #10b981 !important;">Teacher</span>
                                @elseif($user->hasRole('staff'))
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">Administrative Staff</span>
                                @else
                                    <span class="text-white-25 small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-white-50 small"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <a href="{{ route('admin.employees.show', $user->id) }}" class="btn btn-sm btn-info rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; background: #06b6d4; border: none; color: white;" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $user->id) }}" class="btn btn-sm btn-warning rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; background: #f59e0b; border: none; color: white;" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirm deletion of this personnel profile?');">
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
                                <div class="mb-3"><i class="bi bi-person-x fs-1"></i></div>
                                <h5>No personnel records found</h5>
                                <p class="small mb-0">Try adjusting your filters or register a new employee.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-transparent border-0 p-4 border-top border-secondary border-opacity-10 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-white-50 small">
                Showing <span class="text-white fw-bold">{{ $employees->firstItem() ?? 0 }}-{{ $employees->lastItem() ?? 0 }}</span> of total <span class="text-white fw-bold">{{ $employees->total() }}</span> records
            </div>
            <div class="pagination-modern">
                {{ $employees->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
