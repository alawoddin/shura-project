<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\FinancialService;

class DashboardController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function index()
    {
        $stats = $this->financialService->getDashboardStats();

        return view('admin.index', $stats);
    }
}
