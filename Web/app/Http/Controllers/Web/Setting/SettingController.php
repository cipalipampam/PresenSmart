<?php

namespace App\Http\Controllers\Web\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $lat = Setting::where('key', 'school_lat')->first()?->value;
        $long = Setting::where('key', 'school_long')->first()?->value;
        $radius = Setting::where('key', 'school_radius')->first()?->value;

        $checkInEnd = Setting::where('key', 'check_in_end')->first()?->value ?? '07:00';
        $checkOutStart = Setting::where('key', 'check_out_start')->first()?->value ?? '15:00';
        $lateTolerance = Setting::where('key', 'late_tolerance_minutes')->first()?->value ?? '10';

        return view('admin.settings.index', compact(
            'lat', 'long', 'radius', 
            'checkInEnd', 'checkOutStart', 'lateTolerance'
        ));
    }

    public function updateLocation(Request $request)
    {
        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => $request->lat]);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => $request->long]);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => $request->radius]);
        return redirect()->to(route('admin.settings.index') . '#tab-location')->with('success', 'Pengaturan Batas Lokasi berhasil diperbarui!');
    }

    public function updateAttendanceSettings(Request $request)
    {
        Setting::updateOrCreate(['key' => 'check_in_end'], ['value' => $request->check_in_end]);
        Setting::updateOrCreate(['key' => 'check_out_start'], ['value' => $request->check_out_start]);
        Setting::updateOrCreate(['key' => 'late_tolerance_minutes'], ['value' => $request->late_tolerance]);
        return redirect()->to(route('admin.settings.index') . '#tab-time')->with('success', 'Konfigurasi Waktu Presensi berhasil diperbarui!');
    }
}
