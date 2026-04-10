<?php

namespace App\Http\Controllers\Web\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function location()
    {
        $lat = Setting::where('key', 'school_lat')->first()?->value;
        $long = Setting::where('key', 'school_long')->first()?->value;
        $radius = Setting::where('key', 'school_radius')->first()?->value;
        return view('admin.locations.location', compact('lat', 'long', 'radius'));
    }

    public function updateLocation(Request $request)
    {
        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => $request->lat]);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => $request->long]);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => $request->radius]);
        return redirect()->route('admin.location')->with('success', 'Location settings updated successfully!');
    }

    public function attendance()
    {
        $checkInEnd = Setting::where('key', 'check_in_end')->first()?->value ?? '07:00';
        $checkOutStart = Setting::where('key', 'check_out_start')->first()?->value ?? '15:00';
        $lateTolerance = Setting::where('key', 'late_tolerance_minutes')->first()?->value ?? '10';
        return view('admin.settings.attendance', compact('checkInEnd', 'checkOutStart', 'lateTolerance'));
    }

    public function updateAttendanceSettings(Request $request)
    {
        Setting::updateOrCreate(['key' => 'check_in_end'], ['value' => $request->check_in_end]);
        Setting::updateOrCreate(['key' => 'check_out_start'], ['value' => $request->check_out_start]);
        Setting::updateOrCreate(['key' => 'late_tolerance_minutes'], ['value' => $request->late_tolerance]);
        return redirect()->back()->with('success', 'Konfigurasi Waktu Presensi berhasil diperbarui!');
    }
}
