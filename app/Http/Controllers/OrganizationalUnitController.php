<?php

namespace App\Http\Controllers;

use App\DataTables\OrganizationalUnitDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreOrganizationalUnitRequest;
use App\Http\Requests\UpdateOrganizationalUnitRequest;
use App\Models\OrganizationalUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganizationalUnitController extends Controller
{
    use HandlesCrudModal;

    protected function crudModalIndexRoute(): string
    {
        return 'organizational-units.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'organizational_unit';
    }

    public function index(): View
    {
        $total = OrganizationalUnit::query()->count();
        $active = OrganizationalUnit::query()->where('is_active', true)->count();

        return view('organizational-units.index', [
            'stats' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $total - $active,
                'with_employees' => OrganizationalUnit::query()->has('employees')->count(),
            ],
            'parents' => OrganizationalUnit::query()->orderBy('name')->get(),
        ]);
    }

    public function data(OrganizationalUnitDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function show(OrganizationalUnit $organizationalUnit): JsonResponse
    {
        return $this->crudModalJson($organizationalUnit);
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
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

    public function edit(OrganizationalUnit $organizationalUnit): RedirectResponse
    {
        return $this->crudModalEditRedirect($organizationalUnit);
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
