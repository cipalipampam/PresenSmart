<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = \App\Models\Announcement::latest()->get();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        \App\Models\Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $announcement = \App\Models\Announcement::findOrFail($id);
        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active') // 'on' check usually resolves properly or use $request->is_active logic if boolean api
        ]);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        \App\Models\Announcement::findOrFail($id)->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman dihapus.');
    }
}
