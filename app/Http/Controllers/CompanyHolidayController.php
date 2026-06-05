<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyHolidayRequest;
use App\Http\Requests\UpdateCompanyHolidayRequest;
use App\Models\CompanyHoliday;
use Illuminate\Http\JsonResponse;

class CompanyHolidayController extends Controller
{
    public function index(): JsonResponse
    {
        $holidays = CompanyHoliday::query()
            ->orderByDesc('date')
            ->get()
            ->map(fn (CompanyHoliday $h) => [
                'id' => $h->id,
                'date' => $h->date->format('Y-m-d'),
                'date_display' => $h->date->format('d/m/Y'),
                'name' => $h->name,
                'notes' => $h->notes,
                'is_active' => $h->is_active,
            ]);

        return response()->json(['data' => $holidays]);
    }

    public function store(StoreCompanyHolidayRequest $request): JsonResponse
    {
        $holiday = CompanyHoliday::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Libur perusahaan berhasil ditambahkan.',
            'data' => $holiday,
        ], 201);
    }

    public function update(UpdateCompanyHolidayRequest $request, CompanyHoliday $companyHoliday): JsonResponse
    {
        $companyHoliday->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', $companyHoliday->is_active),
        ]);

        return response()->json([
            'message' => 'Libur perusahaan berhasil diperbarui.',
            'data' => $companyHoliday->fresh(),
        ]);
    }

    public function destroy(CompanyHoliday $companyHoliday): JsonResponse
    {
        $companyHoliday->delete();

        return response()->json(['message' => 'Libur perusahaan berhasil dihapus.']);
    }
}
