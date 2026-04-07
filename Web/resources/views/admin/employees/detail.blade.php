@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Employee Detail</h5>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @if(isset($employee->employee->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->employee->profile_picture))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($employee->employee->profile_picture) }}" alt="Profile Photo" class="img-thumbnail rounded" style="width: 250px; height: 300px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center mx-auto" style="width: 250px; height: 300px; font-size: 5rem;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <p class="text-muted mt-2">No photo available</p>
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <th style="width: 30%;">Full Name</th>
                                        <td>{{ $employee->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $employee->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Employee ID (NIP)</th>
                                        <td>{{ $employee->employee->employee_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Position / Subject</th>
                                        <td>{{ $employee->employee->position ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role / Access Level</th>
                                        <td>
                                            @if($employee->hasRole('guru'))
                                                <span class="badge bg-success">Teacher</span>
                                            @elseif($employee->hasRole('staff'))
                                                <span class="badge bg-secondary">Staff</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>
                                            @if(($employee->employee->gender ?? '') == 'male')
                                                Male
                                            @elseif(($employee->employee->gender ?? '') == 'female')
                                                Female
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Place, Date of Birth</th>
                                        <td>
                                            {{ $employee->employee->place_of_birth ?? '-' }}, 
                                            {{ isset($employee->employee->date_of_birth) ? \Carbon\Carbon::parse($employee->employee->date_of_birth)->format('d F Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Religion</th>
                                        <td>{{ ucfirst($employee->employee->religion ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number</th>
                                        <td>{{ $employee->employee->phone_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Full Address</th>
                                        <td>{{ $employee->employee->address ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="d-flex mt-4 gap-2">
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil me-1"></i> Edit Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
