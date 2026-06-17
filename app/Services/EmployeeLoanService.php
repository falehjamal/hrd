<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanInstallment;
use App\Models\User;
use Carbon\Carbon;

class EmployeeLoanService
{
    public function createLoan(
        Employee $employee,
        Carbon|string $loanDate,
        float $principalAmount,
        float $installmentAmount,
        ?string $notes,
        ?User $creator
    ): EmployeeLoan {
        if ($principalAmount <= 0) {
            throw new \InvalidArgumentException('Nominal pinjaman harus lebih dari 0.');
        }

        if ($installmentAmount <= 0) {
            throw new \InvalidArgumentException('Nominal cicilan harus lebih dari 0.');
        }

        if ($installmentAmount > $principalAmount) {
            throw new \InvalidArgumentException('Nominal cicilan tidak boleh melebihi pinjaman.');
        }

        $totalInstallments = (int) ceil($principalAmount / $installmentAmount);

        if ($totalInstallments < 1) {
            throw new \InvalidArgumentException('Jumlah cicilan tidak valid.');
        }

        $loan = EmployeeLoan::query()->create([
            'employee_id' => $employee->id,
            'loan_date' => Carbon::parse($loanDate)->toDateString(),
            'principal_amount' => $principalAmount,
            'installment_amount' => $installmentAmount,
            'total_installments' => $totalInstallments,
            'paid_amount' => 0,
            'status' => EmployeeLoan::STATUS_ACTIVE,
            'notes' => $notes,
            'created_by' => $creator?->id,
        ]);

        $this->generateInstallments($loan);

        return $loan->fresh(['installments', 'employee']);
    }

    public function generateInstallments(EmployeeLoan $loan): void
    {
        $startDate = $loan->loan_date->copy();
        $remaining = (float) $loan->principal_amount;
        $installmentAmount = (float) $loan->installment_amount;

        for ($i = 1; $i <= $loan->total_installments; $i++) {
            $amount = $i === $loan->total_installments
                ? round($remaining, 2)
                : round(min($installmentAmount, $remaining), 2);

            EmployeeLoanInstallment::query()->create([
                'employee_loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $startDate->copy()->addMonths($i - 1)->toDateString(),
                'amount' => $amount,
                'status' => EmployeeLoanInstallment::STATUS_PENDING,
            ]);

            $remaining -= $amount;
        }
    }

    public function payInstallment(EmployeeLoanInstallment $installment, User $user, ?string $notes = null): void
    {
        $this->markInstallmentPaid($installment, $user, $notes);
    }

    public function payInstallmentViaPayroll(EmployeeLoanInstallment $installment, User $user, ?string $notes = null): void
    {
        $this->markInstallmentPaid($installment, $user, $notes);
    }

    protected function markInstallmentPaid(EmployeeLoanInstallment $installment, User $user, ?string $notes = null): void
    {
        $installment->load('loan');

        if ($installment->status !== EmployeeLoanInstallment::STATUS_PENDING) {
            throw new \InvalidArgumentException('Cicilan ini sudah diproses.');
        }

        if ($installment->loan->status !== EmployeeLoan::STATUS_ACTIVE) {
            throw new \InvalidArgumentException('Pinjaman tidak aktif.');
        }

        $installment->update([
            'status' => EmployeeLoanInstallment::STATUS_PAID,
            'paid_at' => now(),
            'paid_by' => $user->id,
            'notes' => $notes,
        ]);

        $loan = $installment->loan;
        $loan->increment('paid_amount', (float) $installment->amount);

        if ($loan->fresh()->remaining_amount <= 0) {
            $loan->update(['status' => EmployeeLoan::STATUS_PAID]);
        }
    }

    public function cancelLoan(EmployeeLoan $loan): void
    {
        if ($loan->status !== EmployeeLoan::STATUS_ACTIVE) {
            throw new \InvalidArgumentException('Hanya pinjaman aktif yang dapat dibatalkan.');
        }

        $loan->installments()
            ->where('status', EmployeeLoanInstallment::STATUS_PENDING)
            ->update(['status' => EmployeeLoanInstallment::STATUS_CANCELLED]);

        $loan->update(['status' => EmployeeLoan::STATUS_CANCELLED]);
    }

    public function deleteLoan(EmployeeLoan $loan): void
    {
        if ($loan->installments()->where('status', EmployeeLoanInstallment::STATUS_PAID)->exists()) {
            throw new \InvalidArgumentException('Pinjaman dengan cicilan terbayar tidak dapat dihapus. Batalkan saja.');
        }

        $loan->delete();
    }

    public function previewInstallments(float $principalAmount, float $installmentAmount): array
    {
        if ($principalAmount <= 0 || $installmentAmount <= 0) {
            return ['total_installments' => 0, 'last_installment_amount' => 0];
        }

        $totalInstallments = (int) ceil($principalAmount / $installmentAmount);
        $regularTotal = ($totalInstallments - 1) * $installmentAmount;
        $lastAmount = round($principalAmount - $regularTotal, 2);

        return [
            'total_installments' => $totalInstallments,
            'last_installment_amount' => $lastAmount,
        ];
    }
}
