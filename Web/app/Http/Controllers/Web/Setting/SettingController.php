<?php

namespace App\Http\Controllers\Web\Setting;

use App\Http\Controllers\Controller;
use App\Services\SettingCache;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings      = SettingCache::all(); // single cached query
        $lat           = $settings->get('school_lat');
        $long          = $settings->get('school_long');
        $radius        = $settings->get('school_radius');
        $checkInEnd    = $settings->get('check_in_end', '07:00');
        $checkOutStart = $settings->get('check_out_start', '15:00');
        $lateTolerance = $settings->get('late_tolerance_minutes', '10');

        return view('admin.settings.index', compact(
            'lat', 'long', 'radius',
            'checkInEnd', 'checkOutStart', 'lateTolerance'
        ));
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat'    => 'required|numeric|between:-90,90',
            'long'   => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:10000',
        ]);

        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => $request->lat]);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => $request->long]);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => $request->radius]);

        event(new \App\Events\SystemSettingsUpdated([
            'school_lat'    => $request->lat,
            'school_long'   => $request->long,
            'school_radius' => $request->radius,
        ]));

        SettingCache::flush(); // invalidate cache

        return redirect()->to(route('admin.settings.index') . '#tab-location')->with('success', 'Pengaturan Batas Lokasi berhasil diperbarui!');
    }

    public function updateAttendanceSettings(Request $request)
    {
        $request->validate([
            'check_in_end'    => 'required|date_format:H:i',
            'check_out_start' => 'required|date_format:H:i|after:check_in_end',
            'late_tolerance'  => 'required|integer|min:0|max:120',
        ]);

        Setting::updateOrCreate(['key' => 'check_in_end'], ['value' => $request->check_in_end]);
        Setting::updateOrCreate(['key' => 'check_out_start'], ['value' => $request->check_out_start]);
        Setting::updateOrCreate(['key' => 'late_tolerance_minutes'], ['value' => $request->late_tolerance]);

        event(new \App\Events\SystemSettingsUpdated([
            'check_in_end'           => $request->check_in_end,
            'check_out_start'        => $request->check_out_start,
            'late_tolerance_minutes' => $request->late_tolerance,
        ]));

        SettingCache::flush(); // invalidate cache

        return redirect()->to(route('admin.settings.index') . '#tab-time')->with('success', 'Konfigurasi Waktu Presensi berhasil diperbarui!');
    }
}
