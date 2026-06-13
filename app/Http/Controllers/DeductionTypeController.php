<?php

namespace App\Http\Controllers;

use App\DataTables\DeductionTypeDataTable;
use App\Http\Requests\StoreDeductionTypeRequest;
use App\Http\Requests\UpdateDeductionTypeRequest;
use App\Models\DeductionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeductionTypeController extends Controller
{
    public function index(): View
    {
        return view('deduction-types.index');
    }

    public function data(DeductionTypeDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        return view('deduction-types.create');
    }

    public function store(StoreDeductionTypeRequest $request): RedirectResponse
    {
        DeductionType::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('deduction-types.index')->with('success', 'Jenis pemotongan berhasil ditambahkan.');
    }

    public function edit(DeductionType $deductionType): View
    {
        return view('deduction-types.edit', compact('deductionType'));
    }

    public function update(UpdateDeductionTypeRequest $request, DeductionType $deductionType): RedirectResponse
    {
        $deductionType->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('deduction-types.index')->with('success', 'Jenis pemotongan berhasil diperbarui.');
    }

    public function destroy(DeductionType $deductionType): RedirectResponse
    {
        if ($deductionType->employeeDeductions()->exists()) {
            return back()->with('error', 'Jenis pemotongan tidak dapat dihapus karena sudah digunakan.');
        }

        $deductionType->delete();

        return redirect()->route('deduction-types.index')->with('success', 'Jenis pemotongan berhasil dihapus.');
    }
}
