<?php

namespace App\Http\Controllers;

use App\DataTables\LeaveTypeDataTable;
use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function index(): View
    {
        return view('leave-types.index');
    }

    public function data(LeaveTypeDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function create(): View
    {
        return view('leave-types.create');
    }

    public function store(StoreLeaveTypeRequest $request): RedirectResponse
    {
        LeaveType::query()->create([
            ...$request->validated(),
            'is_paid' => $request->boolean('is_paid', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti berhasil ditambahkan.');
    }

    public function edit(LeaveType $leaveType): View
    {
        return view('leave-types.edit', ['leaveType' => $leaveType]);
    }

    public function update(UpdateLeaveTypeRequest $request, LeaveType $leaveType): RedirectResponse
    {
        $leaveType->update([
            ...$request->validated(),
            'is_paid' => $request->boolean('is_paid'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti berhasil diperbarui.');
    }

    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        if ($leaveType->leaveRequests()->exists()) {
            return back()->with('error', 'Jenis cuti tidak dapat dihapus karena sudah digunakan dalam pengajuan.');
        }

        $leaveType->delete();

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti berhasil dihapus.');
    }
}
