<?php

namespace App\Http\Controllers;

use App\DataTables\PayrollEntryDataTable;
use App\DataTables\PayrollPeriodDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StorePayrollPeriodRequest;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Services\PayrollPeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PayrollPeriodController extends Controller
{
    use HandlesCrudModal;

    public function __construct(
        protected PayrollPeriodService $payrollPeriodService
    ) {}

    protected function crudModalIndexRoute(): string
    {
        return 'payroll-periods.index';
    }

    protected function crudModalResourceKey(): string
    {
        return '';
    }

    public function index(): View
    {
        $currentYear = (int) date('Y');
        $years = range($currentYear - 2, $currentYear + 1);

        return view('payroll-periods.index', compact('years'));
    }

    public function data(): JsonResponse
    {
        return (new PayrollPeriodDataTable)->json();
    }

    public function create(): RedirectResponse
    {
        return $this->crudModalCreateRedirect();
    }

    public function store(StorePayrollPeriodRequest $request): RedirectResponse
    {
        try {
            $period = $this->payrollPeriodService->create(
                (int) $request->period_year,
                (int) $request->period_month,
                $request->user(),
                $request->notes
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('payroll-periods.index')
                ->withInput()
                ->with('error', $e->getMessage())
                ->with('open_crud_modal', 'create');
        }

        return redirect()
            ->route('payroll-periods.show', $period)
            ->with('success', 'Periode gaji berhasil dibuat.');
    }

    public function show(PayrollPeriod $payrollPeriod): View
    {
        $payrollPeriod->load(['generator', 'finalizer']);

        $summary = [
            'total_employees' => $payrollPeriod->entries()->count(),
            'processed' => $payrollPeriod->entries()->where('is_skipped', false)->count(),
            'skipped' => $payrollPeriod->entries()->where('is_skipped', true)->count(),
            'total_net' => $payrollPeriod->entries()->where('is_skipped', false)->sum('net_salary'),
        ];

        return view('payroll-periods.show', [
            'period' => $payrollPeriod,
            'summary' => $summary,
        ]);
    }

    public function entriesData(PayrollPeriod $payrollPeriod): JsonResponse
    {
        return (new PayrollEntryDataTable($payrollPeriod->id))->json();
    }

    public function regenerate(PayrollPeriod $payrollPeriod): RedirectResponse
    {
        try {
            $this->payrollPeriodService->generate($payrollPeriod);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Periode gaji berhasil dihitung ulang.');
    }

    public function finalize(PayrollPeriod $payrollPeriod): RedirectResponse
    {
        try {
            $this->payrollPeriodService->finalize($payrollPeriod, auth()->user());
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Periode gaji berhasil difinalisasi.');
    }

    public function destroy(PayrollPeriod $payrollPeriod): RedirectResponse
    {
        try {
            $this->payrollPeriodService->delete($payrollPeriod);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('payroll-periods.index')
            ->with('success', 'Periode gaji draft berhasil dihapus.');
    }

    public function showEntry(PayrollPeriod $payrollPeriod, PayrollEntry $entry): View
    {
        abort_unless($entry->payroll_period_id === $payrollPeriod->id, 404);

        $entry->load(['employee.position', 'items', 'period']);

        return view('payroll-periods.entry', [
            'period' => $payrollPeriod,
            'entry' => $entry,
            'backRoute' => route('payroll-periods.show', $payrollPeriod),
        ]);
    }
}
