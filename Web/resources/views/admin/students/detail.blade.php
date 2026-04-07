@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Profile Detail</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @if(isset($student->student->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->student->profile_picture))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($student->student->profile_picture) }}" alt="Profile Photo" class="img-thumbnail rounded" style="width: 250px; height: 300px; object-fit: cover;">
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
                                        <td>{{ $student->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $student->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>NISN</th>
                                        <td>{{ $student->student->national_student_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Student ID (NIS)</th>
                                        <td>{{ $student->student->student_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Grade</th>
                                        <td>{{ $student->student->grade ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role / Access Level</th>
                                        <td><span class="badge bg-primary">Student</span></td>
                                    </tr>
                                    <tr>
                                        <th>Gender</th>
                                        <td>
                                            @if(($student->student->gender ?? '') == 'male')
                                                Male
                                            @elseif(($student->student->gender ?? '') == 'female')
                                                Female
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Place, Date of Birth</th>
                                        <td>
                                            {{ $student->student->place_of_birth ?? '-' }}, 
                                            {{ isset($student->student->date_of_birth) ? \Carbon\Carbon::parse($student->student->date_of_birth)->format('d F Y') : '-' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Religion</th>
                                        <td>{{ ucfirst($student->student->religion ?? '-') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number</th>
                                        <td>{{ $student->student->phone_number ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Full Address</th>
                                        <td>{{ $student->student->address ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div class="d-flex mt-4 gap-2">
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning">
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
