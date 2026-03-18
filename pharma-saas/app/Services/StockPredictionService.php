<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\SalesHistory;
use Illuminate\Support\Facades\DB;

class StockPredictionService
{
    public function predictStockout(int $productId, ?int $tenantId = null): ?array
    {
        $product = Product::where('id', $productId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$product) {
            return null;
        }

        $avgDailySales = $this->calculateAverageDailySales($productId, $tenantId);
        
        if ($avgDailySales <= 0) {
            return null;
        }

        $currentStock = $this->getCurrentStock($productId);
        $daysUntilStockout = (int) floor($currentStock / $avgDailySales);

        $riskLevel = $this->calculateRiskLevel($daysUntilStockout, $currentStock, $product->min_stock);

        return [
            'product_id' => $productId,
            'product_name' => $product->name,
            'current_stock' => $currentStock,
            'avg_daily_sales' => $avgDailySales,
            'days_until_stockout' => $daysUntilStockout,
            'risk_level' => $riskLevel,
            'min_stock' => $product->min_stock,
        ];
    }

    public function calculateAverageDailySales(int $productId, ?int $tenantId = null, int $days = 30): float
    {
        $salesData = SaleItem::select('sale_items.*', 'sales.sale_date', 'sales.tenant_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sale_items.product_id', $productId)
            ->where('sales.tenant_id', $tenantId)
            ->whereDate('sales.sale_date', '>=', now()->subDays($days))
            ->groupBy('sales.sale_date')
            ->selectRaw('DATE(sales.sale_date) as date, SUM(sale_items.quantity) as total_quantity')
            ->get();

        if ($salesData->isEmpty()) {
            return 0;
        }

        $totalQuantity = $salesData->sum('total_quantity');
        return $totalQuantity / $days;
    }

    public function getCurrentStock(int $productId): float
    {
        return Stock::where('product_id', $productId)->sum('quantity');
    }

    public function calculateRiskLevel(int $daysUntilStockout, float $currentStock, float $minStock): string
    {
        if ($currentStock <= 0) {
            return 'critical';
        }

        if ($daysUntilStockout <= 7) {
            return 'critical';
        }

        if ($daysUntilStockout <= 14) {
            return 'high';
        }

        if ($currentStock <= $minStock) {
            return 'medium';
        }

        return 'low';
    }

    public function getHighRiskProducts(?int $tenantId = null, int $limit = 10): array
    {
        $products = Product::where('tenant_id', $tenantId)->get();
        
        $risks = [];
        
        foreach ($products as $product) {
            $prediction = $this->predictStockout($product->id, $tenantId);
            
            if ($prediction && in_array($prediction['risk_level'], ['high', 'critical'])) {
                $risks[] = $prediction;
            }
        }

        usort($risks, function ($a, $b) {
            return $a['days_until_stockout'] <=> $b['days_until_stockout'];
        });

        return array_slice($risks, 0, $limit);
    }

    public function updateSalesHistory(int $productId, ?int $tenantId = null): void
    {
        $avgDailySales = $this->calculateAverageDailySales($productId, $tenantId, 30);
        $currentStock = $this->getCurrentStock($productId);
        
        $daysUntilStockout = $avgDailySales > 0 ? (int) floor($currentStock / $avgDailySales) : null;
        $riskLevel = $this->calculateRiskLevel($daysUntilStockout ?? 999, $currentStock, Product::find($productId)?->min_stock ?? 0);

        SalesHistory::updateOrCreate(
            [
                'product_id' => $productId,
                'tenant_id' => $tenantId,
                'date' => today(),
            ],
            [
                'avg_daily_sales' => $avgDailySales,
                'days_until_stockout' => $daysUntilStockout,
                'risk_level' => $riskLevel,
            ]
        );
    }

    public function runBatchPrediction(?int $tenantId = null): void
    {
        $products = Product::where('tenant_id', $tenantId)->get();
        
        foreach ($products as $product) {
            $this->updateSalesHistory($product->id, $tenantId);
        }
    }
}
