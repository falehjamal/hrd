<?php

namespace App\Http\Controllers;

use App\DataTables\WorkLocationDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreWorkLocationRequest;
use App\Http\Requests\UpdateWorkLocationRequest;
use App\Models\Branch;
use App\Models\WorkLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkLocationController extends Controller
{
    use HandlesCrudModal;

    protected function crudModalIndexRoute(): string
    {
        return 'work-locations.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'work_location';
    }

    public function index(): View
    {
        return view('work-locations.index', [
            'branches' => Branch::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function data(WorkLocationDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function show(WorkLocation $workLocation): JsonResponse
    {
        return $this->crudModalJson($workLocation);
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreWorkLocationRequest $request): RedirectResponse
    {
        $branchId = $request->input('branch_id');

        if ($request->boolean('is_default')) {
            WorkLocation::clearDefaultExcept(null, $branchId ? (int) $branchId : null);
        }

        WorkLocation::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'is_default' => $request->boolean('is_default'),
        ]);

        return redirect()->route('work-locations.index')->with('success', 'Lokasi kerja berhasil ditambahkan.');
    }

    public function edit(WorkLocation $workLocation): RedirectResponse
    {
        return $this->crudModalEditRedirect($workLocation);
    }

    public function update(UpdateWorkLocationRequest $request, WorkLocation $workLocation): RedirectResponse
    {
        $branchId = $request->input('branch_id', $workLocation->branch_id);

        if ($request->boolean('is_default')) {
            WorkLocation::clearDefaultExcept($workLocation->id, $branchId ? (int) $branchId : null);
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
