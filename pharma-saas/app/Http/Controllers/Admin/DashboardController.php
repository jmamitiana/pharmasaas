<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Tenant;
use App\Models\SalesHistory;
use App\Services\StockPredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected StockPredictionService $predictionService;

    public function __construct(StockPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function index()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $todaySales = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', today())
            ->sum('total');

        $monthlySales = Sale::where('tenant_id', $tenantId)
            ->whereMonth('sale_date', now()->month)
            ->sum('total');

        $totalProducts = Product::where('tenant_id', $tenantId)->count();
        $lowStockProducts = Product::where('tenant_id', $tenantId)
            ->whereRaw('(SELECT SUM(quantity) FROM stocks WHERE stocks.product_id = products.id) <= min_stock')
            ->count();

        $pendingPurchases = Purchase::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        $recentSales = Sale::where('tenant_id', $tenantId)
            ->with(['user', 'items.product'])
            ->latest()
            ->limit(10)
            ->get();

        $stockRisks = $this->predictionService->getHighRiskProducts($tenantId);

        return view('dashboard', compact(
            'todaySales',
            'monthlySales',
            'totalProducts',
            'lowStockProducts',
            'pendingPurchases',
            'recentSales',
            'stockRisks'
        ));
    }
}
