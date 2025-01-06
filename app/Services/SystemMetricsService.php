<?php

namespace App\Services;

class SystemMetricsService
{
  public function getCpuUsage()
  {
    if (PHP_OS_FAMILY === 'Linux') {
      $load = sys_getloadavg();
      $cores = (int) shell_exec('nproc');
      return min(round(($load[0] / $cores) * 100, 1), 100);
    }
    return 0;
  }

  public function getMemoryUsage()
  {
    if (PHP_OS_FAMILY === 'Linux') {
      $free = shell_exec('free');
      $free = (string) trim($free);
      $free_arr = explode("\n", $free);
      $mem = explode(" ", $free_arr[1]);
      $mem = array_filter($mem);
      $mem = array_merge($mem);
      $memory_usage = round($mem[2] / $mem[1] * 100, 1);
      return $memory_usage;
    }
    return memory_get_usage(true) / memory_get_peak_usage(true) * 100;
  }

  public function getDiskUsage()
  {
    $disk_total = disk_total_space('/');
    $disk_free = disk_free_space('/');
    return round(($disk_total - $disk_free) / $disk_total * 100, 1);
  }

  public function getResponseTime()
  {
    try {
      $ch = curl_init();
      curl_setopt_array($ch, [
        CURLOPT_URL => config('app.url'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 2,
        CURLOPT_CONNECTTIMEOUT => 2
      ]);

      $start = microtime(true);
      curl_exec($ch);
      $end = microtime(true);

      if (curl_errno($ch)) {
        return 0; // Return 0 if request fails
      }

      curl_close($ch);
      return round(($end - $start) * 1000); // Convert to milliseconds
    } catch (\Exception $e) {
      return 0; // Return 0 if any other error occurs
    }
  }

  public function getAllMetrics()
  {
    return [
      'cpu' => $this->getCpuUsage(),
      'memory' => $this->getMemoryUsage(),
      'disk' => $this->getDiskUsage(),
      'response_time' => $this->getResponseTime()
    ];
  }
}