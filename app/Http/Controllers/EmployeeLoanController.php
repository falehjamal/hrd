<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeeLoanDataTable;
use App\Http\Concerns\HandlesCrudModal;
use App\Http\Requests\StoreEmployeeLoanRequest;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Services\EmployeeLoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeLoanController extends Controller
{
    use HandlesCrudModal;

    public function __construct(
        protected EmployeeLoanService $loanService
    ) {}

    protected function crudModalIndexRoute(): string
    {
        return 'employee-loans.index';
    }

    protected function crudModalResourceKey(): string
    {
        return '';
    }

    public function index(): View
    {
        $employees = Employee::query()->active()->orderBy('name')->get();

        return view('employee-loans.index', compact('employees'));
    }

    public function data(): JsonResponse
    {
        return (new EmployeeLoanDataTable)->json();
    }

    public function dataForEmployee(Employee $employee): JsonResponse
    {
        return (new EmployeeLoanDataTable($employee->id))->json();
    }

    public function create(Request $request): RedirectResponse
    {
        $params = $request->filled('employee_id')
            ? ['employee_id' => $request->employee_id]
            : [];

        return redirect()->route('employee-loans.index', $params)
            ->with('open_crud_modal', 'create');
    }

    public function store(StoreEmployeeLoanRequest $request): RedirectResponse
    {
        $employee = Employee::query()->findOrFail($request->employee_id);

        try {
            $loan = $this->loanService->createLoan(
                $employee,
                $request->loan_date,
                (float) $request->principal_amount,
                (float) $request->installment_amount,
                $request->notes,
                $request->user()
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('employee-loans.index')
                ->withInput()
                ->with('error', $e->getMessage())
                ->with('open_crud_modal', 'create');
        }

        return redirect()
            ->route('employee-loans.show', $loan)
            ->with('success', 'Piutang berhasil dicatat.');
    }

    public function show(EmployeeLoan $employeeLoan): View
    {
        $employeeLoan->load(['employee', 'installments.payer', 'creator']);

        return view('employee-loans.show', ['loan' => $employeeLoan]);
    }

    public function destroy(EmployeeLoan $employeeLoan): RedirectResponse
    {
        try {
            $this->loanService->deleteLoan($employeeLoan);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('employee-loans.index')
            ->with('success', 'Piutang berhasil dihapus.');
    }

    public function cancel(EmployeeLoan $employeeLoan): RedirectResponse
    {
        try {
            $this->loanService->cancelLoan($employeeLoan);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Piutang berhasil dibatalkan.');
    }

    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate([
            'principal_amount' => ['required', 'numeric', 'min:1'],
            'installment_amount' => ['required', 'numeric', 'min:1'],
        ]);

        return response()->json(
            $this->loanService->previewInstallments(
                (float) $data['principal_amount'],
                (float) $data['installment_amount']
            )
        );
    }
}
