<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Activity;
use App\Models\PostView;
use Illuminate\Support\Facades\Cache;

class DashboardController
{
  public function index()
  {
    // Get total posts and posts this week
    $totalPosts = Post::count();
    $postsThisWeek = Post::whereBetween('created_at', [
      now()->startOfWeek(),
      now()->endOfWeek()
    ])->count();

    // Get view statistics
    $totalViews = PostView::count();
    $avgViewsPerPost = $totalPosts > 0 ? round($totalViews / $totalPosts, 1) : 0;

    // Get system metrics
    $systemMetrics = [
      'cpu' => 45,    // Example value
      'memory' => 60,  // Example value
      'disk' => 75,    // Example value
    ];

    // Get recent activities
    $recentActivities = Activity::latest()->take(5)->get();

    // Get posts data for chart (last 12 months)
    $postsData = Post::selectRaw('strftime("%Y-%m", created_at) as month, COUNT(*) as count')
      ->whereDate('created_at', '>=', now()->subMonths(12))
      ->groupBy('month')
      ->orderBy('month')
      ->pluck('count', 'month')
      ->toArray();

    // Prepare chart data
    $chartData = [
      'labels' => [],
      'data' => []
    ];

    // Fill in the data for the last 12 months
    for ($i = 11; $i >= 0; $i--) {
      $month = now()->subMonths($i)->format('Y-m');
      $chartData['labels'][] = now()->subMonths($i)->format('M Y');
      $chartData['data'][] = $postsData[$month] ?? 0;
    }

    return view('dashboard', compact(
      'totalPosts',
      'postsThisWeek',
      'totalViews',
      'avgViewsPerPost',
      'systemMetrics',
      'recentActivities',
      'chartData'
    ));
  }
}