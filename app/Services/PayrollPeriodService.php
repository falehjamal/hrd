<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeLoanInstallment;
use App\Models\OvertimeRequest;
use App\Models\PayrollEntry;
use App\Models\PayrollEntryItem;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayrollPeriodService
{
    public function __construct(
        protected PayrollCalculationService $calculationService,
        protected EmployeeLoanService $loanService
    ) {}

    public function create(int $year, int $month, User $user, ?string $notes = null): PayrollPeriod
    {
        if ($month < 1 || $month > 12) {
            throw new \InvalidArgumentException('Bulan tidak valid.');
        }

        $exists = PayrollPeriod::query()
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException('Periode gaji untuk bulan ini sudah ada.');
        }

        $period = PayrollPeriod::query()->create([
            'period_year' => $year,
            'period_month' => $month,
            'status' => PayrollPeriod::STATUS_DRAFT,
            'notes' => $notes,
            'generated_by' => $user->id,
        ]);

        $this->generate($period);

        return $period->fresh(['generator']);
    }

    public function generate(PayrollPeriod $period): void
    {
        if (! $period->isDraft()) {
            throw new \InvalidArgumentException('Hanya periode draft yang dapat di-generate ulang.');
        }

        DB::transaction(function () use ($period) {
            $period->entries()->each(function (PayrollEntry $entry) {
                $entry->items()->delete();
                $entry->delete();
            });

            Employee::query()
                ->active()
                ->orderBy('name')
                ->each(function (Employee $employee) use ($period) {
                    $result = $this->calculationService->calculateForEmployee($employee, $period);
                    $this->calculationService->persistEntry($period, $employee, $result);
                });
        });
    }

    public function finalize(PayrollPeriod $period, User $user): void
    {
        if (! $period->isDraft()) {
            throw new \InvalidArgumentException('Periode ini sudah difinalisasi.');
        }

        DB::transaction(function () use ($period, $user) {
            $period->load(['entries.items']);

            foreach ($period->entries as $entry) {
                if ($entry->is_skipped) {
                    continue;
                }

                foreach ($entry->items as $item) {
                    if ($item->category === PayrollEntryItem::CATEGORY_LOAN_INSTALLMENT
                        && $item->reference_type === EmployeeLoanInstallment::class
                        && $item->reference_id) {
                        $installment = EmployeeLoanInstallment::query()->find($item->reference_id);

                        if ($installment && $installment->status === EmployeeLoanInstallment::STATUS_PENDING) {
                            $this->loanService->payInstallmentViaPayroll(
                                $installment,
                                $user,
                                'Dipotong via payroll '.$period->periodLabel()
                            );
                        }
                    }

                    if ($item->category === PayrollEntryItem::CATEGORY_OVERTIME
                        && $item->reference_type === OvertimeRequest::class
                        && $item->reference_id) {
                        OvertimeRequest::query()
                            ->where('id', $item->reference_id)
                            ->whereNull('payroll_entry_id')
                            ->update(['payroll_entry_id' => $entry->id]);
                    }
                }
            }

            $period->update([
                'status' => PayrollPeriod::STATUS_FINALIZED,
                'finalized_by' => $user->id,
                'finalized_at' => now(),
            ]);
        });
    }

    public function delete(PayrollPeriod $period): void
    {
        if (! $period->isDraft()) {
            throw new \InvalidArgumentException('Hanya periode draft yang dapat dihapus.');
        }

        $period->delete();
    }
}
