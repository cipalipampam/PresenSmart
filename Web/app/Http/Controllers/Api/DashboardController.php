<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->dashboardService->forUser($request->user()),
        ]);
    }
}
