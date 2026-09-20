<?php

namespace App\Http\Controllers\Web\Setting;

use App\Events\SystemSettingsUpdated;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Shared\Settings\SettingCache;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SettingCache::all(); // single cached query
        $lat = $settings->get('school_lat', '-6.200000');
        $long = $settings->get('school_long', '106.816666');
        $radius = $settings->get('school_radius', '100');

        $checkInStart = $settings->get('check_in_start', '06:00');
        $checkInEnd = $settings->get('check_in_end', '07:00');
        $lateTolerance = $settings->get('late_tolerance_minutes', '15');
        $checkOutStart = $settings->get('check_out_start', '15:00');
        $checkOutEnd = $settings->get('check_out_end', '17:00');

        $schoolName = $settings->get('school_name', 'SMA Negeri 1 Contoh');
        $schoolAddress = $settings->get('school_address', 'Jl. Pendidikan No.1, Jakarta Pusat');
        $schoolPhone = $settings->get('school_phone', '021-12345678');
        $schoolEmail = $settings->get('school_email', 'info@sekolah.sch.id');

        return view('admin.settings.index', compact(
            'lat', 'long', 'radius',
            'checkInStart', 'checkInEnd', 'lateTolerance', 'checkOutStart', 'checkOutEnd',
            'schoolName', 'schoolAddress', 'schoolPhone', 'schoolEmail'
        ));
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'radius' => 'required|integer|min:10|max:10000',
        ], [
            'lat.required' => 'Titik koordinat Latitude wajib diisi.',
            'long.required' => 'Titik koordinat Longitude wajib diisi.',
            'radius.required' => 'Radius geofence wajib diisi.',
            'radius.min' => 'Radius minimal adalah :min meter.',
            'radius.max' => 'Radius maksimal adalah :max meter.',
        ]);

        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => $request->lat]);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => $request->long]);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => $request->radius]);

        SettingCache::flush();

        event(new SystemSettingsUpdated([
            'school_lat' => $request->lat,
            'school_long' => $request->long,
            'school_radius' => $request->radius,
        ]));

        return redirect()->to(route('admin.settings.index').'#tab-location')->with('success', 'Batas lokasi dan perimeter geofence berhasil diperbarui.');
    }

    public function updateAttendanceSettings(Request $request)
    {
        $request->validate([
            'check_in_start' => 'required|date_format:H:i',
            'check_in_end' => 'required|date_format:H:i|after:check_in_start',
            'late_tolerance' => 'required|integer|min:0|max:180',
            'check_out_start' => 'required|date_format:H:i|after:check_in_end',
            'check_out_end' => 'required|date_format:H:i|after:check_out_start',
        ], [
            'check_in_start.required' => 'Jam mulai presensi masuk wajib diisi.',
            'check_in_end.required' => 'Batas jam masuk tepat waktu wajib diisi.',
            'check_in_end.after' => 'Batas masuk tepat waktu harus setelah jam mulai masuk.',
            'late_tolerance.required' => 'Toleransi keterlambatan wajib diisi.',
            'check_out_start.required' => 'Jam mulai presensi pulang wajib diisi.',
            'check_out_start.after' => 'Jam presensi pulang harus setelah batas jam masuk.',
            'check_out_end.required' => 'Batas akhir presensi pulang wajib diisi.',
            'check_out_end.after' => 'Batas akhir pulang harus setelah jam mulai pulang.',
        ]);

        Setting::updateOrCreate(['key' => 'check_in_start'], ['value' => $request->check_in_start]);
        Setting::updateOrCreate(['key' => 'check_in_end'], ['value' => $request->check_in_end]);
        Setting::updateOrCreate(['key' => 'late_tolerance_minutes'], ['value' => $request->late_tolerance]);
        Setting::updateOrCreate(['key' => 'check_out_start'], ['value' => $request->check_out_start]);
        Setting::updateOrCreate(['key' => 'check_out_end'], ['value' => $request->check_out_end]);

        SettingCache::flush();

        event(new SystemSettingsUpdated([
            'check_in_start' => $request->check_in_start,
            'check_in_end' => $request->check_in_end,
            'late_tolerance_minutes' => $request->late_tolerance,
            'check_out_start' => $request->check_out_start,
            'check_out_end' => $request->check_out_end,
        ]));

        return redirect()->to(route('admin.settings.index').'#tab-time')->with('success', 'Konfigurasi jadwal & jam operasional presensi berhasil diperbarui.');
    }

    public function updateInstitution(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:100',
        ], [
            'school_name.required' => 'Nama institusi / sekolah wajib diisi.',
            'school_email.email' => 'Format email resmi institusi tidak valid.',
        ]);

        Setting::updateOrCreate(['key' => 'school_name'], ['value' => $request->school_name]);
        Setting::updateOrCreate(['key' => 'school_address'], ['value' => $request->school_address]);
        Setting::updateOrCreate(['key' => 'school_phone'], ['value' => $request->school_phone]);
        Setting::updateOrCreate(['key' => 'school_email'], ['value' => $request->school_email]);

        SettingCache::flush();

        event(new SystemSettingsUpdated([
            'school_name' => $request->school_name,
            'school_address' => $request->school_address,
            'school_phone' => $request->school_phone,
            'school_email' => $request->school_email,
        ]));

        return redirect()->to(route('admin.settings.index').'#tab-school')->with('success', 'Identitas profil instansi / sekolah berhasil diperbarui.');
    }
}
