<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Post;

class CachedMetricsService
{
  protected $systemMetrics;

  public function __construct(SystemMetricsService $systemMetrics)
  {
    $this->systemMetrics = $systemMetrics;
  }

  public function getMetrics()
  {
    return Cache::remember('system_metrics', 300, function () {
      return $this->systemMetrics->getAllMetrics();
    });
  }

  public function getDashboardStats()
  {
    return Cache::remember('dashboard_stats', 300, function () {
      return [
        'totalPosts' => Post::count(),
        'postsThisWeek' => Post::where('created_at', '>=', Carbon::now()->subWeek())->count(),
        'totalViews' => Post::sum('views'),
        'avgViewsPerPost' => Post::avg('views') ?? 0,
      ];
    });
  }
}