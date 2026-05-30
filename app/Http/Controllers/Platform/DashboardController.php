<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total' => Tenant::query()->count(),
            'active' => Tenant::query()->where('status', Tenant::STATUS_ACTIVE)->count(),
            'suspended' => Tenant::query()->where('status', Tenant::STATUS_SUSPENDED)->count(),
        ];

        return view('platform.dashboard', compact('stats'));
    }
}
