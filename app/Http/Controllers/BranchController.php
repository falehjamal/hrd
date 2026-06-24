<?php

namespace App\Http\Controllers;

use App\DataTables\BranchDataTable;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $total = Branch::query()->count();
        $active = Branch::query()->where('is_active', true)->count();

        return view('branches.index', [
            'stats' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $total - $active,
                'with_employees' => Branch::query()->has('employees')->count(),
            ],
        ]);
    }

    public function data(BranchDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        return view('branches.create');
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        if ($request->boolean('is_head_office')) {
            Branch::clearHeadOfficeExcept();
        }

        Branch::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'is_head_office' => $request->boolean('is_head_office'),
        ]);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch): View
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        if ($request->boolean('is_head_office')) {
            Branch::clearHeadOfficeExcept($branch->id);
        }

        $branch->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
            'is_head_office' => $request->boolean('is_head_office'),
        ]);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        if ($branch->employees()->exists()) {
            return redirect()->route('branches.index')
                ->with('error', 'Cabang tidak dapat dihapus karena masih digunakan karyawan.');
        }

        if ($branch->workLocations()->exists()) {
            return redirect()->route('branches.index')
                ->with('error', 'Cabang tidak dapat dihapus karena masih memiliki lokasi kerja.');
        }

        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
