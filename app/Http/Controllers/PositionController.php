<?php

namespace App\Http\Controllers;

use App\DataTables\PositionDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PositionController extends Controller
{
    use HandlesCrudModal;

    protected function crudModalIndexRoute(): string
    {
        return 'positions.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'position';
    }

    public function index(): View
    {
        return view('positions.index');
    }

    public function data(PositionDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function show(Position $position): JsonResponse
    {
        return $this->crudModalJson($position);
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StorePositionRequest $request): RedirectResponse
    {
        Position::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position): RedirectResponse
    {
        return $this->crudModalEditRedirect($position);
    }

    public function update(UpdatePositionRequest $request, Position $position): RedirectResponse
    {
        $position->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        if ($position->employees()->exists()) {
            return redirect()->route('positions.index')
                ->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan karyawan.');
        }

        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
