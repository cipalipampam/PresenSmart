@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Employee / Teacher List</h2>
        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Add New Employee
        </a>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <form action="{{ route('admin.employees.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search name or NIP" 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <form action="{{ route('admin.employees.index') }}" method="GET" class="d-flex justify-content-end">
                <select name="per_page" class="form-select w-auto me-2" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Rows</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Rows</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Rows</option>
                </select>
                <button class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Employee ID (NIP)</th>
                    <th>Position / Subject</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $index => $user)
                    <tr>
                        <td>{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                        <td>
                            @if(isset($user->employee->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->employee->profile_picture))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->employee->profile_picture) }}" 
                                     alt="Profile Photo" 
                                     style="max-width: 50px; max-height: 50px; border-radius: 50%;">
                            @else
                                <span class="text-muted">No Photo</span>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->employee->employee_number ?? '-' }}</td>
                        <td>{{ $user->employee->position ?? '-' }}</td>
                        <td>
                            @if($user->hasRole('guru'))
                                <span class="badge bg-success">Teacher</span>
                            @elseif($user->hasRole('staff'))
                                <span class="badge bg-secondary">Staff</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.employees.show', $user->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                Showing {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} of {{ $employees->total() }} users
            </div>
            <div>
                {{ $employees->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
