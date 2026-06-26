<?php

namespace App\Http\Controllers;

use App\DataTables\ShiftDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Models\Shift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShiftController extends Controller
{
    use HandlesCrudModal;

    protected function crudModalIndexRoute(): string
    {
        return 'shifts.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'shift';
    }

    public function index(): View
    {
        return view('shifts.index');
    }

    public function data(ShiftDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function show(Shift $shift): JsonResponse
    {
        return $this->crudModalJson($shift);
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreShiftRequest $request): RedirectResponse
    {
        Shift::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil ditambahkan.');
    }

    public function edit(Shift $shift): RedirectResponse
    {
        return $this->crudModalEditRedirect($shift);
    }

    public function update(UpdateShiftRequest $request, Shift $shift): RedirectResponse
    {
        $shift->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $shift->delete();

        return redirect()->route('shifts.index')->with('success', 'Shift berhasil dihapus.');
    }
}
