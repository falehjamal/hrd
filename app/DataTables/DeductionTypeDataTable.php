<?php

namespace App\DataTables;

use App\Models\DeductionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class DeductionTypeDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('status_badge', function (DeductionType $type) {
                if ($type->is_active) {
                    return '<span class="badge badge-pill badge-pill--success">Aktif</span>';
                }

                return '<span class="badge badge-pill badge-pill--secondary">Nonaktif</span>';
            })
            ->addColumn('action', function (DeductionType $type) {
                return view('partials.datatables.deduction-type-actions', compact('type'))->render();
            })
            ->rawColumns(['status_badge', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return DeductionType::query()->orderBy('code');
    }
}
