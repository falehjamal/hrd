<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayEmployeeLoanInstallmentRequest;
use App\Models\EmployeeLoanInstallment;
use App\Services\EmployeeLoanService;
use Illuminate\Http\RedirectResponse;

class EmployeeLoanInstallmentController extends Controller
{
    public function __construct(
        protected EmployeeLoanService $loanService
    ) {}

    public function pay(PayEmployeeLoanInstallmentRequest $request, EmployeeLoanInstallment $employeeLoanInstallment): RedirectResponse
    {
        try {
            $this->loanService->payInstallment(
                $employeeLoanInstallment,
                $request->user(),
                $request->notes
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran cicilan berhasil dicatat.');
    }
}
