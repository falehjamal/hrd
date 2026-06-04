<?php

namespace App\Http\Controllers;

use App\DataTables\WorkLocationDataTable;
use App\Http\Requests\StoreWorkLocationRequest;
use App\Http\Requests\UpdateWorkLocationRequest;
use App\Models\WorkLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkLocationController extends Controller
{
    public function index(): View
    {
        return view('work-locations.index');
    }

    public function data(WorkLocationDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        return view('work-locations.create');
    }

    public function store(StoreWorkLocationRequest $request): RedirectResponse
    {
        if ($request->boolean('is_default')) {
            WorkLocation::clearDefaultExcept();
        }

        WorkLocation::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil ditambahkan.');
    }

    public function edit(WorkLocation $workLocation): View
    {
        return view('work-locations.edit', ['workLocation' => $workLocation]);
    }

    public function update(UpdateWorkLocationRequest $request, WorkLocation $workLocation): RedirectResponse
    {
        if ($request->boolean('is_default')) {
            WorkLocation::clearDefaultExcept($workLocation->id);
        }

        $workLocation->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil diperbarui.');
    }

    public function destroy(WorkLocation $workLocation): RedirectResponse
    {
        $workLocation->delete();

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil dihapus.');
    }
}
