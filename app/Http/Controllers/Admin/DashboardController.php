<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Price;

class DashboardController extends Controller
{
    public function index()
    {
        // If you have only one row in prices table, this is fine:
        $price = Price::query()->latest('id')->first();

        // Total surveys
        $surveyCount = Survey::count();

        // Step distribution (0..2) based on current_step — step 3 removed
        $stepRows = Survey::query()
            ->selectRaw('COALESCE(current_step, 0) as step, COUNT(*) as total')
            ->groupBy('step')
            ->pluck('total', 'step'); // [step => total]

        $steps = [0, 1, 2];
        $stepCounts = [];
        foreach ($steps as $s) {
            $stepCounts[$s] = (int) ($stepRows[$s] ?? 0);
        }

        // Submitted count (if you use is_submitted = 1/0)
        $submittedCount = Survey::where('is_submitted', 1)->count();

        // Optional: completion rate (submitted / total)
        $completionRate = $surveyCount > 0
            ? round(($submittedCount / $surveyCount) * 100, 1)
            : 0;

        // Failure rate (did not complete)
        $failureRate = $surveyCount > 0
            ? round((($surveyCount - $submittedCount) / $surveyCount) * 100, 1)
            : 0;

        // Optional: revenue-like stat (only if you store level_total)
        // If level_total is not reliable yet, you can remove this.
        $totalLevelRevenue = (float) Survey::where('is_submitted', 1)
            ->whereNotNull('level_total')
            ->sum('level_total');

        $avgRevenuePerSurvey = $surveyCount > 0
            ? round($totalLevelRevenue / $surveyCount, 2)
            : 0;

        return view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'active' => 'dashboard',

            'survey_count' => $surveyCount,

            // Prices
            'price' => $price,

            // Steps
            'step_counts' => $stepCounts,
            'submitted_count' => $submittedCount,
            'completion_rate' => $completionRate,
            'failure_rate' => $failureRate,

            // Optional money stats
            'total_level_revenue' => $totalLevelRevenue,
            'avg_revenue_per_survey' => $avgRevenuePerSurvey,
        ]);
    }
}
