<?php

namespace App\DataTables;

use App\Models\Central\TenantUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class TenantUserDataTable
{
    public function __construct(
        protected string $tenantId,
    ) {}

    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('last_login_display', function (TenantUser $tenantUser) {
                return $tenantUser->last_login_at?->format('d/m/Y H:i') ?? '—';
            })
            ->toJson();
    }

    protected function query(): Builder
    {
        return TenantUser::query()
            ->where('tenant_id', $this->tenantId)
            ->orderByDesc('last_login_at');
    }
}
