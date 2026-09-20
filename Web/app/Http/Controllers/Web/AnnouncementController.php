<?php

namespace App\Http\Controllers\Web;

use App\Events\AnnouncementChanged;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $totalCount = Announcement::count();
        $activeCount = Announcement::where('is_active', true)->count();
        $inactiveCount = Announcement::where('is_active', false)->count();

        $announcements = $query->paginate($request->input('per_page', 10));

        return view('admin.announcements.index', compact(
            'announcements',
            'totalCount',
            'activeCount',
            'inactiveCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'title.max' => 'Judul pengumuman maksimal :max karakter.',
        ], [
            'title' => 'judul pengumuman',
            'content' => 'isi pengumuman',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        event(AnnouncementChanged::fromAnnouncement($announcement, 'created'));

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman baru berhasil diterbitkan.');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'title.max' => 'Judul pengumuman maksimal :max karakter.',
        ], [
            'title' => 'judul pengumuman',
            'content' => 'isi pengumuman',
        ]);

        $announcement = Announcement::findOrFail($id);
        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->has('is_active'),
        ]);

        event(AnnouncementChanged::fromAnnouncement($announcement, 'updated'));

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function toggleStatus(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->is_active = ! $announcement->is_active;
        $announcement->save();

        event(AnnouncementChanged::fromAnnouncement($announcement, 'updated'));

        $statusText = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.announcements.index')->with('success', "Pengumuman berhasil {$statusText}.");
    }

    public function destroy(string $id)
    {
        $announcement = Announcement::findOrFail($id);
        $event = AnnouncementChanged::fromAnnouncement($announcement, 'deleted');
        $announcement->delete();
        event($event);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
