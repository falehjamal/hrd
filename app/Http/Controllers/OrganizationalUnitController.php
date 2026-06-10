<?php

namespace App\Http\Controllers;

use App\DataTables\OrganizationalUnitDataTable;
use App\Http\Requests\StoreOrganizationalUnitRequest;
use App\Http\Requests\UpdateOrganizationalUnitRequest;
use App\Models\OrganizationalUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationalUnitController extends Controller
{
    public function index(): View
    {
        return view('organizational-units.index');
    }

    public function data(OrganizationalUnitDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        $parents = OrganizationalUnit::query()->active()->orderBy('name')->get();

        return view('organizational-units.create', compact('parents'));
    }

    public function store(StoreOrganizationalUnitRequest $request): RedirectResponse
    {
        OrganizationalUnit::query()->create([
            ...$request->validated(),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('organizational-units.index')->with('success', 'Unit organisasi berhasil ditambahkan.');
    }

    public function edit(OrganizationalUnit $organizationalUnit): View
    {
        $parents = OrganizationalUnit::query()
            ->active()
            ->where('id', '!=', $organizationalUnit->id)
            ->orderBy('name')
            ->get();

        return view('organizational-units.edit', [
            'unit' => $organizationalUnit,
            'parents' => $parents,
        ]);
    }

    public function update(UpdateOrganizationalUnitRequest $request, OrganizationalUnit $organizationalUnit): RedirectResponse
    {
        $organizationalUnit->update([
            ...$request->validated(),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('organizational-units.index')->with('success', 'Unit organisasi berhasil diperbarui.');
    }

    public function destroy(OrganizationalUnit $organizationalUnit): RedirectResponse
    {
        if ($organizationalUnit->children()->exists()) {
            return redirect()->route('organizational-units.index')
                ->with('error', 'Unit organisasi tidak dapat dihapus karena masih memiliki sub-unit.');
        }

        if ($organizationalUnit->employees()->exists()) {
            return redirect()->route('organizational-units.index')
                ->with('error', 'Unit organisasi tidak dapat dihapus karena masih memiliki karyawan.');
        }

        $organizationalUnit->delete();

        return redirect()->route('organizational-units.index')->with('success', 'Unit organisasi berhasil dihapus.');
    }
}
