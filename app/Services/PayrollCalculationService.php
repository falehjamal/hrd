<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeLoan;
use App\Models\EmployeeLoanInstallment;
use App\Models\EmployeeSalary;
use App\Models\OvertimeRequest;
use App\Models\PayrollEntry;
use App\Models\PayrollEntryItem;
use App\Models\PayrollPeriod;
use App\Models\Setting;

class PayrollCalculationService
{
    public function calculateForEmployee(Employee $employee, PayrollPeriod $period): array
    {
        $periodEnd = $period->periodEndDate();
        $periodStart = $period->periodStartDate();

        $salary = EmployeeSalary::query()
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->whereDate('effective_date', '<=', $periodEnd)
            ->orderByDesc('effective_date')
            ->first();

        if (! $salary) {
            return [
                'is_skipped' => true,
                'skip_reason' => 'Tidak ada gaji aktif.',
                'basic_salary' => 0,
                'fixed_allowance' => 0,
                'overtime_minutes' => 0,
                'overtime_pay' => 0,
                'total_earnings' => 0,
                'total_deductions' => 0,
                'net_salary' => 0,
                'items' => [],
            ];
        }

        $items = [];
        $basicSalary = (float) $salary->basic_salary;
        $fixedAllowance = (float) $salary->fixed_allowance;

        $items[] = [
            'type' => PayrollEntryItem::TYPE_EARNING,
            'category' => PayrollEntryItem::CATEGORY_BASIC_SALARY,
            'label' => 'Gaji Pokok',
            'amount' => $basicSalary,
            'reference_type' => EmployeeSalary::class,
            'reference_id' => $salary->id,
        ];

        if ($fixedAllowance > 0) {
            $items[] = [
                'type' => PayrollEntryItem::TYPE_EARNING,
                'category' => PayrollEntryItem::CATEGORY_FIXED_ALLOWANCE,
                'label' => 'Tunjangan Tetap',
                'amount' => $fixedAllowance,
                'reference_type' => EmployeeSalary::class,
                'reference_id' => $salary->id,
            ];
        }

        $deductions = EmployeeDeduction::query()
            ->with('deductionType')
            ->where('employee_id', $employee->id)
            ->where('is_active', true)
            ->whereDate('effective_date', '<=', $periodEnd)
            ->get();

        foreach ($deductions as $deduction) {
            $items[] = [
                'type' => PayrollEntryItem::TYPE_DEDUCTION,
                'category' => PayrollEntryItem::CATEGORY_EMPLOYEE_DEDUCTION,
                'label' => $deduction->deductionType->code.' — '.$deduction->deductionType->name,
                'amount' => (float) $deduction->amount,
                'reference_type' => EmployeeDeduction::class,
                'reference_id' => $deduction->id,
            ];
        }

        $loanInstallments = EmployeeLoanInstallment::query()
            ->with('loan')
            ->whereHas('loan', function ($q) use ($employee) {
                $q->where('employee_id', $employee->id)
                    ->where('status', EmployeeLoan::STATUS_ACTIVE);
            })
            ->where('status', EmployeeLoanInstallment::STATUS_PENDING)
            ->whereDate('due_date', '<=', $periodEnd)
            ->orderBy('due_date')
            ->get();

        foreach ($loanInstallments as $installment) {
            $items[] = [
                'type' => PayrollEntryItem::TYPE_DEDUCTION,
                'category' => PayrollEntryItem::CATEGORY_LOAN_INSTALLMENT,
                'label' => 'Cicilan Pinjaman #'.$installment->installment_number.' (jatuh tempo '.$installment->due_date->format('d/m/Y').')',
                'amount' => (float) $installment->amount,
                'reference_type' => EmployeeLoanInstallment::class,
                'reference_id' => $installment->id,
            ];
        }

        $overtimeRequests = OvertimeRequest::query()
            ->where('employee_id', $employee->id)
            ->where('status', OvertimeRequest::STATUS_APPROVED)
            ->whereNull('payroll_entry_id')
            ->whereDate('date', '>=', $periodStart)
            ->whereDate('date', '<=', $periodEnd)
            ->orderBy('date')
            ->get();

        $overtimeMinutes = (int) $overtimeRequests->sum('duration_minutes');
        $hourlyRate = (float) Setting::get('payroll_overtime_hourly_rate', 50000);
        $overtimePay = round(($overtimeMinutes / 60) * $hourlyRate, 2);

        foreach ($overtimeRequests as $overtime) {
            $hours = round($overtime->duration_minutes / 60, 2);
            $amount = round($hours * $hourlyRate, 2);

            $items[] = [
                'type' => PayrollEntryItem::TYPE_EARNING,
                'category' => PayrollEntryItem::CATEGORY_OVERTIME,
                'label' => 'Lembur '.$overtime->date->format('d/m/Y').' ('.$hours.' jam)',
                'amount' => $amount,
                'reference_type' => OvertimeRequest::class,
                'reference_id' => $overtime->id,
            ];
        }

        $totalEarnings = collect($items)
            ->where('type', PayrollEntryItem::TYPE_EARNING)
            ->sum('amount');

        $totalDeductions = collect($items)
            ->where('type', PayrollEntryItem::TYPE_DEDUCTION)
            ->sum('amount');

        return [
            'is_skipped' => false,
            'skip_reason' => null,
            'basic_salary' => $basicSalary,
            'fixed_allowance' => $fixedAllowance,
            'overtime_minutes' => $overtimeMinutes,
            'overtime_pay' => $overtimePay,
            'total_earnings' => round($totalEarnings, 2),
            'total_deductions' => round($totalDeductions, 2),
            'net_salary' => round($totalEarnings - $totalDeductions, 2),
            'items' => $items,
        ];
    }

    public function persistEntry(PayrollPeriod $period, Employee $employee, array $result): PayrollEntry
    {
        $entry = PayrollEntry::query()->create([
            'payroll_period_id' => $period->id,
            'employee_id' => $employee->id,
            'basic_salary' => $result['basic_salary'],
            'fixed_allowance' => $result['fixed_allowance'],
            'overtime_minutes' => $result['overtime_minutes'],
            'overtime_pay' => $result['overtime_pay'],
            'total_earnings' => $result['total_earnings'],
            'total_deductions' => $result['total_deductions'],
            'net_salary' => $result['net_salary'],
            'is_skipped' => $result['is_skipped'],
            'skip_reason' => $result['skip_reason'],
        ]);

        foreach ($result['items'] as $item) {
            PayrollEntryItem::query()->create([
                'payroll_entry_id' => $entry->id,
                'type' => $item['type'],
                'category' => $item['category'],
                'label' => $item['label'],
                'amount' => $item['amount'],
                'reference_type' => $item['reference_type'] ?? null,
                'reference_id' => $item['reference_id'] ?? null,
            ]);
        }

        return $entry->fresh(['items', 'employee']);
    }
}
