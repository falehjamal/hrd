<?php

namespace App\DataTables;

use App\Models\Central\TenantUser;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class TenantDataTable
{
    public function json(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->addColumn('company_name', function (Tenant $tenant) {
                $html = '<strong>'.$tenant->displayName().'</strong>';
                if ($tenant->app_title) {
                    $html .= '<br><small class="text-muted">'.$tenant->name.'</small>';
                }

                return $html;
            })
            ->addColumn('database_name', fn (Tenant $tenant) => '<code>'.config('tenancy.database.prefix').$tenant->id.'</code>')
            ->addColumn('status_badge', function (Tenant $tenant) {
                if ($tenant->isActive()) {
                    return '<span class="badge badge-pill badge-pill--success">Aktif</span>';
                }

                return '<span class="badge badge-pill badge-pill--danger">Nonaktif</span>';
            })
            ->addColumn('users_count_display', fn (Tenant $tenant) => (string) ($tenant->tenant_users_count ?? 0))
            ->addColumn('last_login_display', function (Tenant $tenant) {
                if ($tenant->last_login_at) {
                    return Carbon::parse($tenant->last_login_at)->diffForHumans();
                }

                return '<span class="text-muted">—</span>';
            })
            ->addColumn('action', function (Tenant $tenant) {
                return view('partials.datatables.tenant-actions', compact('tenant'))->render();
            })
            ->rawColumns(['company_name', 'database_name', 'status_badge', 'last_login_display', 'action'])
            ->toJson();
    }

    protected function query(): Builder
    {
        return Tenant::query()
            ->withCount('tenantUsers')
            ->select('tenants.*')
            ->selectSub(
                TenantUser::query()
                    ->selectRaw('MAX(last_login_at)')
                    ->whereColumn('tenant_users.tenant_id', 'tenants.id'),
                'last_login_at'
            );
    }
}
