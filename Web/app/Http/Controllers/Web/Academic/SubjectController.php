<?php

namespace App\Http\Controllers\Web\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Academic\StoreSubjectRequest;
use App\Http\Requests\Web\Academic\UpdateSubjectRequest;
use App\Models\Subject;
use App\Services\Web\Academic\SubjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function __construct(
        private readonly SubjectService $subjectService,
    ) {}

    public function index(Request $request): View
    {
        $subjects = $this->subjectService->getPaginatedSubjects(
            $request->only(['search']),
            10,
        );

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        $this->subjectService->createSubject($request->validated());

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $this->subjectService->updateSubject($subject, $data);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Data mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $deleted = $this->subjectService->deleteSubject($subject);

        if (! $deleted) {
            return redirect()->route('admin.subjects.index')
                ->with('error', 'Tidak dapat menghapus mata pelajaran karena masih digunakan dalam jadwal aktif.');
        }

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
