<?php

namespace App\Http\Controllers;

use App\DataTables\PayslipDataTable;
use App\Models\PayrollEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PayslipController extends Controller
{
    public function index(): View
    {
        return view('payslips.index');
    }

    public function data(): JsonResponse
    {
        $employeeId = auth()->user()->employee?->id;

        abort_unless($employeeId, 403);

        return (new PayslipDataTable($employeeId))->json();
    }

    public function show(PayrollEntry $entry): View
    {
        $user = auth()->user();
        $employee = $user->employee;

        abort_unless($employee && $entry->employee_id === $employee->id, 403);
        abort_unless($entry->period->isFinalized(), 404);
        abort_unless(! $entry->is_skipped, 404);

        $entry->load(['employee.position', 'items', 'period']);

        return view('payslips.show', [
            'entry' => $entry,
            'period' => $entry->period,
            'backRoute' => route('payslips.index'),
        ]);
    }
}
