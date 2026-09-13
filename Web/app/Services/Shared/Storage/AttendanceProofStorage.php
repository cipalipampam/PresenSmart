<?php

namespace App\Services\Shared\Storage;

use App\Models\Attendance;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceProofStorage
{
    /**
     * Store new proof files outside the public web root.
     */
    public function store(UploadedFile $file): string
    {
        return $file->storeAs(
            'attendances',
            Str::uuid().'.'.$file->extension(),
            'local',
        );
    }

    /**
     * Remove a current private proof, or a legacy public proof.
     */
    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }

    /**
     * Return a proof through an authorized route. Legacy public files remain readable.
     */
    public function response(Attendance $attendance)
    {
        abort_unless($attendance->proof_image, 404);

        $disk = Storage::disk('local')->exists($attendance->proof_image)
            ? 'local'
            : 'public';

        abort_unless(Storage::disk($disk)->exists($attendance->proof_image), 404);

        return Storage::disk($disk)->response($attendance->proof_image);
    }
}
