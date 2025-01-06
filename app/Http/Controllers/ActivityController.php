<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
  public function latest()
  {
    $latestActivity = Activity::latest()->first();

    return response()->json([
      'description' => $latestActivity ? $latestActivity->description : 'No recent activity',
      'created_at' => $latestActivity ? $latestActivity->created_at->diffForHumans() : null
    ]);
  }
}