@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Attendance Record Detail</h5>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                
                <div class="card-body p-4">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Full Name</th>
                                <td>{{ $attendance->user->name }}</td>
                            </tr>
                            <tr>
                                <th>Attendance Status</th>
                                <td>
                                    @php
                                        $statusClass = [
                                            'present' => 'success',
                                            'permission' => 'warning',
                                            'sick' => 'info',
                                            'absent' => 'danger'
                                        ];
                                        $texts = [
                                            'present' => 'Present',
                                            'permission' => 'Permission',
                                            'sick' => 'Sick',
                                            'absent' => 'Absent'
                                        ];
                                    @endphp
                                    <span class="badge text-white bg-{{ $statusClass[$attendance->status] ?? 'secondary' }}">
                                        {{ $texts[$attendance->status] ?? ucfirst($attendance->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Record Date & Time</th>
                                <td>{{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d F Y, H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>GPS Coordinates (Location)</th>
                                <td>
                                    @if($attendance->latitude && $attendance->longitude)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->latitude }},{{ $attendance->longitude }}" target="_blank" class="text-primary">
                                            <i class="bi bi-geo-alt-fill"></i> Open in Maps ({{ $attendance->latitude }}, {{ $attendance->longitude }})
                                        </a>
                                    @else
                                        <span class="text-muted">No location recorded</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Notes / Details</th>
                                <td>{{ $attendance->notes ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    @if(isset($attendance->proof_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($attendance->proof_image))
                        <div class="mt-4 text-center">
                            <h6>Attached Document Proof</h6>
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($attendance->proof_image) }}" alt="Supporting Proof" class="img-fluid rounded border" style="max-height: 400px; width: auto;">
                        </div>
                    @endif
                    
                    <div class="d-flex mt-4 gap-2 justify-content-end">
                        <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i> Edit Mode
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
