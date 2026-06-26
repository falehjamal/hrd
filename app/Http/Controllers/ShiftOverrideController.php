<?php

namespace App\Http\Controllers;

use App\DataTables\ShiftOverrideDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreShiftOverrideRequest;
use App\Http\Requests\UpdateShiftOverrideRequest;
use App\Models\Employee;
use App\Models\EmployeeShiftOverride;
use App\Models\Shift;
use App\Services\ShiftCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftOverrideController extends Controller
{
    use HandlesCrudModal;

    protected function crudModalIndexRoute(): string
    {
        return 'shift-overrides.index';
    }

    protected function crudModalResourceKey(): string
    {
        return 'shift_override';
    }

    public function index(): View
    {
        return view('shift-overrides.index', [
            'employees' => Employee::query()->active()->orderBy('name')->get(),
            'shifts' => Shift::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function calendar(Request $request, ShiftCalendarService $calendar): JsonResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        return response()->json($calendar->buildMonth(
            (int) $validated['year'],
            (int) $validated['month'],
            isset($validated['employee_id']) ? (int) $validated['employee_id'] : null,
        ));
    }

    public function dayDetail(Request $request, ShiftCalendarService $calendar): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        return response()->json($calendar->dayDetail(
            $validated['date'],
            isset($validated['employee_id']) ? (int) $validated['employee_id'] : null,
        ));
    }

    public function data(): JsonResponse
    {
        return (new ShiftOverrideDataTable)->json();
    }

    public function show(EmployeeShiftOverride $shiftOverride): JsonResponse
    {
        $data = $shiftOverride->toArray();
        $data['is_day_off'] = $shiftOverride->shift_id === null;

        return response()->json([
            'shift_override' => $data,
        ]);
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StoreShiftOverrideRequest $request): RedirectResponse
    {
        EmployeeShiftOverride::query()->create($request->validated());

        return redirect()->route('shift-overrides.index')->with('success', 'Override jadwal berhasil ditambahkan.');
    }

    public function edit(EmployeeShiftOverride $shiftOverride): RedirectResponse
    {
        return $this->crudModalEditRedirect($shiftOverride);
    }

    public function update(UpdateShiftOverrideRequest $request, EmployeeShiftOverride $shiftOverride): RedirectResponse
    {
        $shiftOverride->update($request->validated());

        return redirect()->route('shift-overrides.index')->with('success', 'Override jadwal berhasil diperbarui.');
    }

    public function destroy(EmployeeShiftOverride $shiftOverride): RedirectResponse
    {
        $shiftOverride->delete();

        return redirect()->route('shift-overrides.index')->with('success', 'Override jadwal berhasil dihapus.');
    }
}
