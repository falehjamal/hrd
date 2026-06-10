<?php

namespace App\Http\Controllers;

use App\Services\OrganizationStructureService;
use Illuminate\View\View;

class OrganizationStructureController extends Controller
{
    public function index(OrganizationStructureService $organizationStructureService): View
    {
        return view('organization-structure.index', [
            'companyRoot' => $organizationStructureService->getCompanyRoot(),
            'reportingTree' => $organizationStructureService->buildReportingTree(),
            'stats' => $organizationStructureService->getStats(),
        ]);
    }
}
