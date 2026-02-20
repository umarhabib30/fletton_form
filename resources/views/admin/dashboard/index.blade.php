@extends('layouts.admin')

@section('content')
<style>
    :root{
        --bg:#eef0f5;
        --card:#151b26;
        --lime:#c1ec4a;
        --muted:#6b7280;
        --axis:rgba(17,24,39,.70);
        --grid:rgba(17,24,39,.10);
        --shadow:0 10px 24px rgba(16,24,40,.18);
        --radius:10px;
    }

    .ecommerce-widget{
        background: var(--bg);
        padding: 22px;
        border-radius: 12px;
    }

    /* Metric cards */
    .metric-card{
        background: var(--card);
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        min-height: 92px;
    }
    .metric-card .card-body{ padding: 18px; }

    .metric-title{
        color: var(--lime);
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 10px;
    }
    .metric-value{
        color: var(--lime);
        font-weight: 700;
        font-size: 40px;
        line-height: 1;
        margin: 0;
    }
    .metric-sub{
        color: rgba(255,255,255,.65);
        font-weight: 600;
        font-size: 12px;
    }

    /* Chart cards */
    .chart-card{
        background: transparent;
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }
    .chart-card .card-body{ padding: 18px; }

    .chart-title{
        color: var(--muted);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    /* Light chart background like screenshot */
    .chart-wrap{
        background: #f4f6fb;
        border-radius: 10px;
        padding: 14px 14px 8px;
        border: 1px solid rgba(17,24,39,.06);
    }

    /* Date filter bar – same as metric cards */
    .dashboard-filter-card{
        background: var(--card);
        border: 0;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 18px 22px;
        margin-bottom: 1rem;
    }
    .dashboard-filter-card .filter-label{
        color: var(--lime);
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 6px;
    }
    .dashboard-filter-card .form-control-date{
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: var(--radius);
        color: #fff;
        padding: 8px 12px;
        font-size: 14px;
        min-width: 160px;
    }
    .dashboard-filter-card .form-control-date:focus{
        background: rgba(255,255,255,.12);
        border-color: var(--lime);
        color: #fff;
        box-shadow: 0 0 0 2px rgba(193,236,74,.25);
        outline: 0;
    }
    .dashboard-filter-card .form-control-date::-webkit-calendar-picker-indicator{
        filter: invert(1);
        opacity: .7;
    }
    .btn-dashboard-apply{
        background: var(--lime) !important;
        color: var(--card) !important;
        border: 0;
        border-radius: var(--radius);
        font-weight: 600;
        padding: 8px 18px;
        font-size: 14px;
    }
    .btn-dashboard-apply:hover{
        background: #b5e040 !important;
        color: var(--card) !important;
    }
    .btn-dashboard-clear{
        background: transparent;
        color: var(--lime);
        border: 1px solid rgba(193,236,74,.5);
        border-radius: var(--radius);
        font-weight: 500;
        padding: 8px 18px;
        font-size: 14px;
    }
    .btn-dashboard-clear:hover{
        background: rgba(193,236,74,.15);
        color: var(--lime);
        border-color: var(--lime);
    }
</style>

<div class="ecommerce-widget">
    {{-- DATE FILTER --}}
    <form method="get" action="{{ route('admin.dashboard') }}" class="dashboard-filter-card">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="from_date" class="filter-label d-block">From date</label>
                <input type="date" id="from_date" name="from_date" class="form-control form-control-date" value="{{ $filter_from_date ?? '' }}">
            </div>
            <div class="col-auto">
                <label for="to_date" class="filter-label d-block">To date</label>
                <input type="date" id="to_date" name="to_date" class="form-control form-control-date" value="{{ $filter_to_date ?? '' }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dashboard-apply">Apply</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-dashboard-clear">Clear filter</a>
            </div>
        </div>
    </form>

    {{-- TOP METRICS --}}
    <div class="row g-3">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-title">Total Surveys</div>
                    <h2 class="metric-value">{{ $survey_count }}</h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-title">Submitted Surveys</div>
                    <h2 class="metric-value">{{ $submitted_count }}</h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-title">Completion Rate</div>
                    <h2 class="metric-value">{{ $completion_rate }}%</h2>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-title">Conversion Rate</div>
                    <h2 class="metric-value">{{ $conversion_rate }}%</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="row g-3 mt-3">
        {{-- Base Prices --}}
        <div class="col-md-6 col-12">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="chart-title">Base Prices (L1–L3)</div>
                    <div class="chart-wrap">
                        <canvas id="basePricesBar" height="140"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Survey Status (NOW BAR CHART) --}}
        <div class="col-md-6 col-12">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="chart-title">Survey Status</div>
                    <div class="chart-wrap">
                        <canvas id="surveyStatusBar" height="140"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const NAVY   = '#151b26';   // --card
    const DARK   = '#151b26';   // Step 0 - filled sign up forms (--card)
    const GRAY   = '#9ca3af';   // Step 1 - selected survey level
    const GREEN  = '#c1ec4a';   // Step 2 - completed the form
    const LIME   = '#c1ec4a';   // Base Prices L3

    const GRID   = 'rgba(17,24,39,.10)';
    const AXIS   = 'rgba(17,24,39,.70)';

    Chart.defaults.font.family = "'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif";

    // -----------------------------
    // BASE PRICE BAR
    // -----------------------------
    const basePrices = @json([
        (float)($price->level1_base ?? 0),
        (float)($price->level2_base ?? 0),
        (float)($price->level3_base ?? 0)
    ]);

    new Chart(document.getElementById('basePricesBar'), {
        type: 'bar',
        data: {
            labels: ['Level 1', 'Level 2', 'Level 3'],
            datasets: [{
                data: basePrices,
                backgroundColor: [NAVY, GRAY, LIME],
                borderRadius: 10,
                borderSkipped: false,
                barThickness: 44
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: AXIS }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: GRID },
                    ticks: { color: AXIS }
                }
            }
        }
    });

    // -----------------------------
    // SURVEY STATUS BAR (steps 0–2 only)
    // -----------------------------
    const stepCounts = @json(array_values($step_counts));

    new Chart(document.getElementById('surveyStatusBar'), {
        type: 'bar',
        data: {
            labels: ['Filled sign up forms', 'Selected survey level', 'Completed the form'],
            datasets: [{
                data: stepCounts,
                backgroundColor: [DARK, GRAY, GREEN],
                borderRadius: 10,
                borderSkipped: false,
                barThickness: 44
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: AXIS }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: GRID },
                    ticks: { color: AXIS }
                }
            }
        }
    });
</script>
@endsection
