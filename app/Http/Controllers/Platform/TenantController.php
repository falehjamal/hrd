<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StoreTenantRequest;
use App\Http\Requests\Platform\UpdateTenantRequest;
use App\Models\Central\TenantUser;
use App\Models\Tenant;
use App\Services\TenantMetricsService;
use App\Services\TenantProvisionerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use InvalidArgumentException;

class TenantController extends Controller
{
    public function __construct(
        protected TenantMetricsService $metrics,
        protected TenantProvisionerService $provisioner,
    ) {}

    public function index(): View
    {
        $tenants = Tenant::query()
            ->withCount('tenantUsers')
            ->orderBy('name')
            ->get()
            ->map(function (Tenant $tenant) {
                $metrics = $this->metrics->forTenant($tenant);

                return (object) [
                    'tenant' => $tenant,
                    'metrics' => $metrics,
                ];
            });

        return view('platform.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('platform.tenants.create');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        try {
            $tenant = $this->provisioner->create(
                [
                    'id' => $request->string('id')->toString(),
                    'name' => $request->string('name')->toString(),
                    'slug' => $request->string('slug')->toString(),
                    'app_title' => $request->input('app_title'),
                ],
                [
                    'name' => $request->string('admin_name')->toString(),
                    'email' => $request->string('admin_email')->toString(),
                    'username' => $request->input('admin_username'),
                    'password' => $request->string('admin_password')->toString(),
                ],
            );
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['id' => $e->getMessage()]);
        }

        return redirect()
            ->route('platform.tenants.show', $tenant)
            ->with('success', 'Tenant berhasil dibuat.');
    }

    public function show(Tenant $tenant): View
    {
        $metrics = $this->metrics->forTenant($tenant);

        $tenantUsers = TenantUser::query()
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('last_login_at')
            ->get();

        return view('platform.tenants.show', compact('tenant', 'metrics', 'tenantUsers'));
    }

    public function edit(Tenant $tenant): View
    {
        return view('platform.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        return redirect()
            ->route('platform.tenants.show', $tenant)
            ->with('success', 'Data tenant berhasil diperbarui.');
    }

    public function suspend(Tenant $tenant): RedirectResponse
    {
        if ($tenant->isActive()) {
            $tenant->suspend();
        }

        return back()->with('success', 'Tenant telah dinonaktifkan.');
    }

    public function activate(Tenant $tenant): RedirectResponse
    {
        if (! $tenant->isActive()) {
            $tenant->activate();
        }

        return back()->with('success', 'Tenant telah diaktifkan kembali.');
    }
}
