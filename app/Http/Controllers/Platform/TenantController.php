<?php

namespace App\Http\Controllers\Platform;

use App\DataTables\TenantDataTable;
use App\DataTables\TenantUserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\StoreTenantRequest;
use App\Http\Requests\Platform\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\TenantMetricsService;
use App\Services\TenantProvisionerService;
use Illuminate\Http\JsonResponse;
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
        return view('platform.tenants.index');
    }

    public function data(TenantDataTable $dataTable): JsonResponse
    {
        return $dataTable->json();
    }

    public function usersData(Tenant $tenant): JsonResponse
    {
        return (new TenantUserDataTable($tenant->id))->json();
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

        return view('platform.tenants.show', compact('tenant', 'metrics'));
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
