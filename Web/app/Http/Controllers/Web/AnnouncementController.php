<?php

namespace App\Http\Controllers\Web;

use App\Events\AnnouncementChanged;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        event(AnnouncementChanged::fromAnnouncement($announcement, 'created'));

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil ditambahkan!');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        event(AnnouncementChanged::fromAnnouncement($announcement, 'updated'));

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $event = AnnouncementChanged::fromAnnouncement($announcement, 'deleted');
        $announcement->delete();
        event($event);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman dihapus.');
    }
}
