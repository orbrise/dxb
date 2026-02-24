<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Package;
use App\Models\UsersProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Wallet Reports
     */
    public function walletReports(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $transactionType = $request->get('transaction_type', 'all');
        $status = $request->get('status', 'all');

        // Base query for wallet transactions
        $query = WalletTransaction::with(['wallet.user'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($transactionType !== 'all') {
            $query->where('type', $transactionType);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);

        // Summary statistics
        $totalTransactions = $query->count();
        $totalAmount = $query->sum('amount');
        $completedTransactions = $query->where('status', 'completed')->count();
        $failedTransactions = $query->where('status', 'failed')->count();
        $stats = [
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
            'completed_transactions' => $completedTransactions,
            'failed_transactions' => $failedTransactions,
            'completion_rate' => $totalTransactions > 0 ? ($completedTransactions / $totalTransactions) * 100 : 0,
            'average_amount' => $totalTransactions > 0 ? $totalAmount / $totalTransactions : 0,
        ];

        // Transaction types breakdown
        $typeBreakdown = WalletTransaction::select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total_amount'))
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('type')
            ->get();

        // Daily transaction chart data
        $dailyStats = WalletTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return view('admin.reports.wallet', compact(
            'transactions', 'stats', 'typeBreakdown', 'dailyStats',
            'dateFrom', 'dateTo', 'transactionType', 'status'
        ));
    }

    /**
     * Balance Transfer Reports
     */
    public function balanceTransfers(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $status = $request->get('status', 'all');

        // Get balance transfer transactions
        $query = WalletTransaction::with(['wallet.user'])
            ->where('type', 'transfer')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $transfers = $query->orderBy('created_at', 'desc')->paginate(50);

        // Summary statistics
        $totalTransfers = $query->count();
        $totalAmount = $query->where('status', 'completed')->sum('amount');
        $successfulTransfers = $query->where('status', 'completed')->count();
        $failedTransfers = $query->where('status', 'failed')->count();
        $averageAmount = $query->where('status', 'completed')->avg('amount');
        $stats = [
            'total_transfers' => $totalTransfers,
            'total_amount' => $totalAmount,
            'successful_transfers' => $successfulTransfers,
            'failed_transfers' => $failedTransfers,
            'success_rate' => $totalTransfers > 0 ? ($successfulTransfers / $totalTransfers) * 100 : 0,
            'average_amount' => $averageAmount ?? 0,
        ];

        // Daily transfer stats for charts
        $dailyTransfers = WalletTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('type', 'transfer')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $dailyLabels = [];
        $dailyCounts = [];
        $dailyAmounts = [];
        
        foreach ($dailyTransfers as $day) {
            $dailyLabels[] = Carbon::parse($day->date)->format('M d');
            $dailyCounts[] = $day->count;
            $dailyAmounts[] = $day->total_amount;
        }
        
        $stats['daily_labels'] = $dailyLabels;
        $stats['daily_counts'] = $dailyCounts;
        $stats['daily_amounts'] = $dailyAmounts;

        return view('admin.reports.balance-transfers', compact(
            'transfers', 'stats',
            'dateFrom', 'dateTo', 'status'
        ));
    }

    /**
     * Paid Ads Reports
     */
    public function paidAds(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $packageId = $request->get('package_id', 'all');
        $status = $request->get('status', 'all');

        // Get all packages for the filter
        $packages = Package::where('status', 'active')->get();

        // Get package purchase transactions
        $query = WalletTransaction::with(['wallet.user'])
            ->where('type', 'package_purchase')
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);

        if ($packageId !== 'all') {
            $query->where('package_id', $packageId);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(50);

        // Add promotion status to each purchase
        foreach ($purchases as $purchase) {
            if ($purchase->wallet && $purchase->wallet->user) {
                $userProfile = UsersProfile::where('user_id', $purchase->wallet->user->id)->first();
                if ($userProfile && $userProfile->promoted_until) {
                    $purchase->promotion_status = [
                        'is_active' => Carbon::now()->lt($userProfile->promoted_until),
                        'expires_at' => Carbon::parse($userProfile->promoted_until)->format('M d, Y H:i')
                    ];
                } else {
                    $purchase->promotion_status = null;
                }
            } else {
                $purchase->promotion_status = null;
            }
            
            // Add package information
            if ($purchase->package_id) {
                $purchase->package = Package::find($purchase->package_id);
            }
        }

        // Summary statistics
        $totalPurchases = $query->count();
        $totalRevenue = $query->where('status', 'completed')->sum('amount');
        $activePromotions = UsersProfile::where('promoted_until', '>', Carbon::now())->count();
        $averagePurchase = $query->where('status', 'completed')->avg('amount');

        $stats = [
            'total_purchases' => $totalPurchases,
            'total_revenue' => $totalRevenue,
            'active_promotions' => $activePromotions,
            'average_purchase' => $averagePurchase ?? 0,
        ];

        // Package statistics
        $packageStats = [];
        $packageNames = [];
        $packageCounts = [];
        $packageRevenues = [];

        foreach ($packages as $package) {
            $packageSales = WalletTransaction::where('type', 'package_purchase')
                ->where('package_id', $package->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->count();
                
            $packageRevenue = WalletTransaction::where('type', 'package_purchase')
                ->where('package_id', $package->id)
                ->where('status', 'completed')
                ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->sum('amount');

            $packageStats[] = [
                'name' => $package->name,
                'price' => $package->price,
                'duration' => $package->duration,
                'total_sales' => $packageSales,
                'revenue' => $packageRevenue,
                'avg_monthly_sales' => $packageSales * 30 / max(1, Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)))
            ];

            $packageNames[] = $package->name;
            $packageCounts[] = $packageSales;
            $packageRevenues[] = $packageRevenue;
        }

        $stats['package_stats'] = $packageStats;
        $stats['package_names'] = $packageNames;
        $stats['package_counts'] = $packageCounts;
        $stats['package_revenues'] = $packageRevenues;

        return view('admin.reports.paid-ads', compact(
            'purchases', 'packages', 'stats',
            'dateFrom', 'dateTo', 'packageId', 'status'
        ));
    }

    /**
     * Overall Dashboard
     */
    public function dashboard(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        // Calculate previous period for trends
        $daysDiff = Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo));
        $prevDateFrom = Carbon::parse($dateFrom)->subDays($daysDiff)->format('Y-m-d');
        $prevDateTo = $dateFrom;

        // Current period stats
        $currentTransactions = WalletTransaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count();
        $currentRevenue = WalletTransaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->where('status', 'completed')->sum('amount');
        $activeUsers = WalletTransaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->distinct('wallet_id')->count();
        $successRate = $currentTransactions > 0 ? 
            (WalletTransaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                ->where('status', 'completed')->count() / $currentTransactions) * 100 : 0;

        // Previous period stats for trends
        $prevTransactions = WalletTransaction::whereBetween('created_at', [$prevDateFrom . ' 00:00:00', $prevDateTo . ' 23:59:59'])->count();
        $prevRevenue = WalletTransaction::whereBetween('created_at', [$prevDateFrom . ' 00:00:00', $prevDateTo . ' 23:59:59'])
            ->where('status', 'completed')->sum('amount');

        // Calculate trends
        $transactionTrend = $prevTransactions > 0 ? (($currentTransactions - $prevTransactions) / $prevTransactions) * 100 : 0;
        $revenueTrend = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;

        $summary = [
            'total_transactions' => $currentTransactions,
            'total_revenue' => $currentRevenue,
            'active_users' => $activeUsers,
            'success_rate' => $successRate,
            'transaction_trend' => round($transactionTrend, 1),
            'revenue_trend' => round($revenueTrend, 1),
        ];

        // Detailed breakdowns
        $breakdown = [
            'wallet' => [
                'total_wallets' => Wallet::count(),
                'active_wallets' => Wallet::whereHas('transactions', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59']);
                })->count(),
                'total_transactions' => WalletTransaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count(),
                'total_volume' => WalletTransaction::whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                    ->where('status', 'completed')->sum('amount'),
            ],
            'transfers' => [
                'total_transfers' => WalletTransaction::where('type', 'transfer')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count(),
                'successful' => WalletTransaction::where('type', 'transfer')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count(),
                'success_rate' => 0,
                'total_amount' => WalletTransaction::where('type', 'transfer')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->sum('amount'),
            ],
            'ads' => [
                'total_purchases' => WalletTransaction::where('type', 'package_purchase')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->count(),
                'active_promotions' => UsersProfile::where('promoted_until', '>', Carbon::now())->count(),
                'total_revenue' => WalletTransaction::where('type', 'package_purchase')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->sum('amount'),
                'avg_purchase' => WalletTransaction::where('type', 'package_purchase')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])->avg('amount') ?? 0,
            ]
        ];

        // Calculate transfer success rate
        if ($breakdown['transfers']['total_transfers'] > 0) {
            $breakdown['transfers']['success_rate'] = ($breakdown['transfers']['successful'] / $breakdown['transfers']['total_transfers']) * 100;
        }

        // Chart data for activity
        $activityData = WalletTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as revenue')
            )
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $activityLabels = [];
        $activityCounts = [];
        $activityRevenue = [];

        foreach ($activityData as $day) {
            $activityLabels[] = Carbon::parse($day->date)->format('M d');
            $activityCounts[] = $day->count;
            $activityRevenue[] = $day->revenue;
        }

        // Transaction type breakdown
        $typeData = WalletTransaction::select('type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('type')
            ->get();

        $typeLabels = [];
        $typeCounts = [];

        foreach ($typeData as $type) {
            $typeLabels[] = ucfirst(str_replace('_', ' ', $type->type));
            $typeCounts[] = $type->count;
        }

        $charts = [
            'activity_labels' => $activityLabels,
            'activity_counts' => $activityCounts,
            'activity_revenue' => $activityRevenue,
            'type_labels' => $typeLabels,
            'type_counts' => $typeCounts,
        ];

        // Recent transactions
        $recent_transactions = WalletTransaction::with(['wallet.user'])
            ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.dashboard', compact(
            'summary', 'breakdown', 'charts', 'recent_transactions',
            'dateFrom', 'dateTo'
        ));
    }

    /**
     * Export data to CSV
     */
    public function exportCsv(Request $request)
    {
        $type = $request->get('type', 'wallet');
        $dateFrom = $request->get('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $filename = "reports_{$type}_" . $dateFrom . "_to_" . $dateTo . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($type, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');

            switch ($type) {
                case 'wallet':
                    fputcsv($file, ['Date', 'User', 'Type', 'Amount', 'Status', 'Description']);
                    
                    WalletTransaction::with(['wallet.user'])
                        ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                        ->chunk(1000, function($transactions) use ($file) {
                            foreach ($transactions as $transaction) {
                                fputcsv($file, [
                                    $transaction->created_at->format('Y-m-d H:i:s'),
                                    $transaction->wallet->user->name ?? 'N/A',
                                    $transaction->type,
                                    $transaction->amount,
                                    $transaction->status,
                                    $transaction->description
                                ]);
                            }
                        });
                    break;

                case 'transfers':
                    fputcsv($file, ['Date', 'User', 'Amount', 'Status', 'Description']);
                    
                    WalletTransaction::with(['wallet.user'])
                        ->where('type', 'transfer')
                        ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                        ->chunk(1000, function($transactions) use ($file) {
                            foreach ($transactions as $transaction) {
                                fputcsv($file, [
                                    $transaction->created_at->format('Y-m-d H:i:s'),
                                    $transaction->wallet->user->name ?? 'N/A',
                                    $transaction->amount,
                                    $transaction->status,
                                    $transaction->description
                                ]);
                            }
                        });
                    break;

                case 'ads':
                    fputcsv($file, ['Date', 'User', 'Package', 'Amount', 'Status', 'Promoted Until']);
                    
                    UsersProfile::with(['user', 'package'])
                        ->whereNotNull('promoted_until')
                        ->whereBetween('created_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
                        ->chunk(1000, function($profiles) use ($file) {
                            foreach ($profiles as $profile) {
                                fputcsv($file, [
                                    $profile->created_at->format('Y-m-d H:i:s'),
                                    $profile->user->name ?? 'N/A',
                                    $profile->package->name ?? 'N/A',
                                    $profile->package->price ?? 0,
                                    $profile->promoted_until > Carbon::now() ? 'Active' : 'Expired',
                                    $profile->promoted_until ? Carbon::parse($profile->promoted_until)->format('Y-m-d H:i:s') : 'N/A'
                                ]);
                            }
                        });
                    break;
            }

            fclose($file);
        }, 200, $headers);
    }
}
