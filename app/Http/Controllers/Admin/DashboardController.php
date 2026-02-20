<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Price;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $price = Price::query()->latest('id')->first();

        // Date filter (optional): from_date, to_date as Y-m-d
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->to_date)->endOfDay() : null;

        $surveyQuery = Survey::query();
        if ($fromDate) {
            $surveyQuery->where('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $surveyQuery->where('created_at', '<=', $toDate);
        }

        $surveyCount = (clone $surveyQuery)->count();

        $stepQuery = (clone $surveyQuery);
        $stepRows = $stepQuery
            ->selectRaw('COALESCE(current_step, 0) as step, COUNT(*) as total')
            ->groupBy('step')
            ->pluck('total', 'step');

        $steps = [0, 1, 2];
        $stepCounts = [];
        foreach ($steps as $s) {
            $stepCounts[$s] = (int) ($stepRows[$s] ?? 0);
        }

        $submittedCount = (clone $surveyQuery)->where('is_submitted', 1)->count();

        $completionRate = $surveyCount > 0
            ? round(($submittedCount / $surveyCount) * 100, 1)
            : 0;

        // Conversion rate = % who completed (same as completion rate, reversed from failure rate)
        $conversionRate = $completionRate;

        $totalLevelRevenue = (float) (clone $surveyQuery)
            ->where('is_submitted', 1)
            ->whereNotNull('level_total')
            ->sum('level_total');

        $avgRevenuePerSurvey = $surveyCount > 0
            ? round($totalLevelRevenue / $surveyCount, 2)
            : 0;

        return view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'active' => 'dashboard',

            'survey_count' => $surveyCount,
            'price' => $price,
            'step_counts' => $stepCounts,
            'submitted_count' => $submittedCount,
            'completion_rate' => $completionRate,
            'conversion_rate' => $conversionRate,

            'total_level_revenue' => $totalLevelRevenue,
            'avg_revenue_per_survey' => $avgRevenuePerSurvey,

            'filter_from_date' => $request->get('from_date'),
            'filter_to_date' => $request->get('to_date'),
        ]);
    }
}
