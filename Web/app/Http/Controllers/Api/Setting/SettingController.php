<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Retrieve global setting values needed by the mobile app, primarily geofence boundaries.
     */
    public function location(): JsonResponse
    {
        $lat = Setting::where('key', 'school_lat')->first()?->value;
        $long = Setting::where('key', 'school_long')->first()?->value;
        $radius = Setting::where('key', 'school_radius')->first()?->value;

        if (!$lat || !$long || !$radius) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi lokasi sekolah belum diatur oleh admin.',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Koordinat lokasi sekolah berhasil diambil.',
            'data' => [
                'latitude' => (float) $lat,
                'longitude' => (float) $long,
                'radius_meters' => (int) $radius
            ]
        ]);
    }
}
