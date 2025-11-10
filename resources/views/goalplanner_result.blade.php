@extends('layouts.app')

@section('customCss')
    <style>
        .status-error {
            border: 2px solid #dc3545;
            background-color: #fff5f5;
        }

        .status-success {
            border: 2px solid #198754;
            background-color: #f0f9f5;
        }

        .status-warning {
            border: 2px solid #ffc107;
            background-color: #fffbf0;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="card sharp-card p-4 mb-4">
            <h2 class="fw-bold text-center mb-4">Your Goal Plan Summary</h2>

            @if (!empty($data['recommendations']) && $data['recommendations']['status'] === 'negative')
                <div class="alert status-error text-center mb-4 rounded-0">
                    <h4 class="fw-bold mb-2">🚫 Financial Health Alert</h4>
                    <p class="mb-0">{{ $data['recommendations']['message'] }}</p>
                    <hr>
                    <p class="mb-1"><strong>Monthly Income:</strong>
                        ₹{{ number_format($data['monthlySavings'] + $data['totalExpenses'], 0, '.', ',') }}</p>
                    <p class="mb-1"><strong>Monthly Expenses:</strong>
                        ₹{{ number_format($data['totalExpenses'], 0, '.', ',') }}</p>
                    <p class="mb-1"><strong>Shortfall:</strong>
                        ₹{{ number_format(abs($data['monthlySavings']), 0, '.', ',') }}</p>
                </div>
            @endif

            @if (empty($data['recommendations']) || $data['recommendations']['status'] !== 'negative')
                {{-- ================= Total Summary ================= --}}
                <div class="mb-4">
                    <h5 class="fw-semibold border-bottom pb-2">Financial Overview</h5>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <small class="text-muted">Total Goals</small>
                                <h5 class="fw-bold mb-0">₹{{ number_format($data['totalGoals'], 0, '.', ',') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <small class="text-muted">Monthly Expenses</small>
                                <h5 class="fw-bold mb-0">₹{{ number_format($data['totalExpenses'], 0, '.', ',') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <small class="text-muted">Yearly to Monthly</small>
                                <h5 class="fw-bold mb-0">₹{{ number_format($data['yearlyToMonthly'], 0, '.', ',') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-dark text-white rounded">
                                <small>Available Savings</small>
                                <h5 class="fw-bold mb-0">₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $status = $data['recommendations']['status'] ?? 'sufficient';
                @endphp

                {{-- ================= CASE 1: SUFFICIENT FUNDS ================= --}}
                @if ($status === 'sufficient')
                    <div class="alert status-success mb-4 rounded-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
                            <div>
                                <h5 class="fw-bold mb-1">✅ Your Plan is Achievable!</h5>
                                <p class="mb-0">Your current savings are sufficient to meet all goals as planned.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Original Goal Plan --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2">Goal-wise SIP Breakdown</h5>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Goal</th>
                                        <th>Amount (₹)</th>
                                        <th>Years</th>
                                        <th>SIP @8%</th>
                                        <th>SIP @10%</th>
                                        <th>SIP @12%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['goalSIPs'] as $goal)
                                        <tr>
                                            <td>{{ $goal['name'] }}</td>
                                            <td>{{ number_format($goal['amount'], 0, '.', ',') }}</td>
                                            <td>{{ $goal['years'] }}</td>
                                            <td>₹{{ number_format($goal['sip8'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($goal['sip10'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($goal['sip12'], 0, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light fw-bold">
                                        <td colspan="3">TOTAL REQUIRED</td>
                                        <td>₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }}</td>
                                        <td>₹{{ number_format($data['requiredSIP10'], 0, '.', ',') }}</td>
                                        <td>₹{{ number_format($data['requiredSIP12'], 0, '.', ',') }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="3">YOUR AVAILABLE SAVINGS</td>
                                        <td colspan="3">₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- ================= CASE 2: INSUFFICIENT FUNDS ================= --}}
                @if ($status === 'insufficient')
                    <div class="alert status-warning mb-4 rounded-0">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
                            <div>
                                <h5 class="fw-bold mb-1">⚠️ Adjustments Required</h5>
                                <p class="mb-0">Your current savings
                                    (₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}) are insufficient.
                                    Required:
                                    ₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }} @8%</p>
                            </div>
                        </div>
                    </div>

                    {{-- Original Plan Summary --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2">
                            <i class="bi bi-x-circle"></i> Original Plan (Not Achievable)
                        </h5>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Goal</th>
                                        <th>Amount (₹)</th>
                                        <th>Years</th>
                                        <th>SIP @8%</th>
                                        <th>SIP @10%</th>
                                        <th>SIP @12%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['goalSIPs'] as $goal)
                                        <tr>
                                            <td>{{ $goal['name'] }}</td>
                                            <td>{{ number_format($goal['amount'], 0, '.', ',') }}</td>
                                            <td>{{ $goal['years'] }}</td>
                                            <td>₹{{ number_format($goal['sip8'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($goal['sip10'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($goal['sip12'], 0, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light fw-bold">
                                        <td colspan="3">TOTAL REQUIRED</td>
                                        <td>₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }}</td>
                                        <td>₹{{ number_format($data['requiredSIP10'], 0, '.', ',') }}</td>
                                        <td>₹{{ number_format($data['requiredSIP12'], 0, '.', ',') }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td class="bg-danger-subtle" colspan="3">YOUR AVAILABLE SAVINGS</td>
                                        <td class="bg-danger-subtle" colspan="3">
                                            ₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Solution 1: Extended Timeline --}}
                    @if (!empty($data['recommendations']['extendedGoals']))
                        <div class="mb-4">
                            <h5 class="fw-semibold border-bottom pb-2">
                                <i class="bi bi-calendar-plus"></i> Solution 1: Extend Timelines
                                @if (!empty($data['recommendations']['yearsAdded']))
                                    <span class="badge bg-dark">+{{ $data['recommendations']['yearsAdded'] }}
                                        Year(s)</span>
                                @endif
                            </h5>

                            <p class="text-muted mb-3">
                                Extending your goal timelines reduces the monthly SIP required, helping make your plan more
                                achievable.
                            </p>

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Goal</th>
                                            <th>Amount (₹)</th>
                                            <th>Old Years</th>
                                            <th>New Years</th>
                                            <th>SIP @8%</th>
                                            <th>SIP @10%</th>
                                            <th>SIP @12%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data['recommendations']['extendedGoals'] as $g)
                                            <tr>
                                                <td>{{ $g['name'] }}</td>
                                                <td>₹{{ number_format($g['amount'], 0, '.', ',') }}</td>
                                                <td class="text-muted">{{ $g['oldYears'] }}y</td>
                                                <td><span class="fw-bold">{{ $g['newYears'] }}y</span></td>
                                                <td>₹{{ number_format($g['newSIP8'], 0, '.', ',') }}</td>
                                                <td>₹{{ number_format($g['newSIP10'], 0, '.', ',') }}</td>
                                                <td>₹{{ number_format($g['newSIP12'], 0, '.', ',') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-light fw-bold">
                                            <td colspan="4">Total Required (Extended)</td>
                                            <td>₹{{ number_format($data['recommendations']['adjustedTotals']['sip8'], 0, '.', ',') }}
                                            </td>
                                            <td>₹{{ number_format($data['recommendations']['adjustedTotals']['sip10'], 0, '.', ',') }}
                                            </td>
                                            <td>₹{{ number_format($data['recommendations']['adjustedTotals']['sip12'], 0, '.', ',') }}
                                            </td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td colspan="4">Your Available Savings</td>
                                            <td colspan="3">₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if ($data['recommendations']['timelineSuccess'] ?? false)
                                <div class="alert alert-light border mt-3 bg-success-subtle">
                                    <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                    <strong>✅ Achievable!</strong> Extending your goals by
                                    <strong>{{ $data['recommendations']['yearsAdded'] }}</strong> year(s) makes your plan
                                    feasible.
                                </div>
                            @else
                                <div class="alert alert-light border mt-3">
                                    <i class="bi bi-exclamation-circle-fill me-2 text-warning"></i>
                                    Timeline extension alone is not enough — consider reducing expenses too.
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Solution 2: Expense Reduction --}}
                    @if (!empty($data['recommendations']['expenseReduction']))
                        @php
                            $expRed = $data['recommendations']['expenseReduction'];
                        @endphp

                        <div class="mb-4">
                            <h5 class="fw-semibold border-bottom pb-2">
                                <i class="bi bi-scissors"></i> Solution 2: Reduce Expenses
                                @if (!empty($expRed['combinedExtension']))
                                    + Extended Timeline
                                @endif
                            </h5>

                            @if (!empty($expRed['noReducible']))
                                <div class="alert alert-light border">
                                    <i class="bi bi-x-circle-fill text-muted"></i> No reducible expenses available (Utility
                                    and Luxury are already zero).
                                </div>
                            @else
                                <div class="alert alert-light border">
                                    <strong>Strategy:</strong>
                                    @if ($expRed['originalTimeline'] ?? false)
                                        Reduce expenses with original timeline first (Priority: Luxury → Utility)
                                    @endif
                                </div>

                                {{-- Expense Comparison --}}
                                <div class="row mt-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-light fw-bold">
                                                <i class="bi bi-wallet2"></i> Current Expenses
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm mb-0">
                                                    <tr>
                                                        <td>Utility</td>
                                                        <td class="text-end fw-bold">
                                                            ₹{{ number_format($expRed['oldUtility'] ?? 0, 0, '.', ',') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Luxury</td>
                                                        <td class="text-end fw-bold">
                                                            ₹{{ number_format($expRed['oldLuxury'] ?? 0, 0, '.', ',') }}
                                                        </td>
                                                    </tr>
                                                    <tr class="table-light">
                                                        <td><strong>Total Reducible</strong></td>
                                                        <td class="text-end fw-bold">
                                                            ₹{{ number_format(($expRed['oldUtility'] ?? 0) + ($expRed['oldLuxury'] ?? 0), 0, '.', ',') }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="card-header bg-dark text-white fw-bold">
                                                <i class="bi bi-check-circle"></i> Reduced Expenses
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm mb-0">
                                                    <tr>
                                                        <td>Utility</td>
                                                        <td class="text-end fw-bold">
                                                            ₹{{ number_format($expRed['newUtility'], 0, '.', ',') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Luxury</td>
                                                        <td class="text-end fw-bold">
                                                            ₹{{ number_format($expRed['newLuxury'], 0, '.', ',') }}</td>
                                                    </tr>
                                                    <tr class="table-light">
                                                        <td><strong>New Savings</strong></td>
                                                        <td class="text-end fw-bold">
                                                            ₹{{ number_format($expRed['newSavings'], 0, '.', ',') }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- If expense reduction with original timeline works --}}
                                @if (($expRed['achievable'] ?? false) && ($expRed['originalTimeline'] ?? false) && empty($expRed['combinedExtension']))
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Goal</th>
                                                    <th>Amount</th>
                                                    <th>Years</th>
                                                    <th>SIP @8%</th>
                                                    <th>SIP @10%</th>
                                                    <th>SIP @12%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data['goalSIPs'] as $goal)
                                                    <tr>
                                                        <td>{{ $goal['name'] }}</td>
                                                        <td>₹{{ number_format($goal['amount'], 0, '.', ',') }}</td>
                                                        <td>{{ $goal['years'] }}</td>
                                                        <td>₹{{ number_format($goal['sip8'], 0, '.', ',') }}</td>
                                                        <td>₹{{ number_format($goal['sip10'], 0, '.', ',') }}</td>
                                                        <td>₹{{ number_format($goal['sip12'], 0, '.', ',') }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-light fw-bold">
                                                    <td colspan="3">TOTAL REQUIRED</td>
                                                    <td>₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }}</td>
                                                    <td>₹{{ number_format($data['requiredSIP10'], 0, '.', ',') }}</td>
                                                    <td>₹{{ number_format($data['requiredSIP12'], 0, '.', ',') }}</td>
                                                </tr>
                                                <tr class="fw-bold">
                                                    <td colspan="3">NEW AVAILABLE SAVINGS</td>
                                                    <td colspan="3">
                                                        ₹{{ number_format($expRed['newSavings'], 0, '.', ',') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="alert alert-light border bg-success-subtle">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <strong>✅ Achievable!</strong>
                                        By reducing expenses by
                                        ₹{{ number_format($expRed['reduceBy'], 0, '.', ',') }}/month, you can
                                        achieve all goals with the original timeline.
                                    </div>
                                @endif

                                {{-- If all expenses reduced but need extension too --}}
                                @if (!empty($expRed['allReduced']) && !empty($expRed['combinedExtension']))
                                    <div class="alert alert-light border">
                                        <i class="bi bi-info-circle-fill"></i> <strong>Combined Approach Needed:</strong>
                                        All reducible expenses eliminated. Also extending timeline by
                                        {{ $expRed['combinedExtension'] }} year(s).
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Goal</th>
                                                    <th>Amount</th>
                                                    <th>Extended Years</th>
                                                    <th>SIP @8%</th>
                                                    <th>SIP @10%</th>
                                                    <th>SIP @12%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($expRed['combinedGoals'] as $g)
                                                    <tr>
                                                        <td>{{ $g['name'] }}</td>
                                                        <td>₹{{ number_format($g['amount'], 0, '.', ',') }}</td>
                                                        <td><span class="badge bg-dark">{{ $g['newYears'] }}y</span></td>
                                                        <td>₹{{ number_format($g['newSIP8'], 0, '.', ',') }}</td>
                                                        <td>₹{{ number_format($g['newSIP10'], 0, '.', ',') }}</td>
                                                        <td>₹{{ number_format($g['newSIP12'], 0, '.', ',') }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-light fw-bold">
                                                    <td colspan="3">TOTAL REQUIRED</td>
                                                    <td>₹{{ number_format($expRed['combinedTotals']['sip8'], 0, '.', ',') }}
                                                    </td>
                                                    <td>₹{{ number_format($expRed['combinedTotals']['sip10'], 0, '.', ',') }}
                                                    </td>
                                                    <td>₹{{ number_format($expRed['combinedTotals']['sip12'], 0, '.', ',') }}
                                                    </td>
                                                </tr>
                                                <tr class="fw-bold">
                                                    <td colspan="3">NEW AVAILABLE SAVINGS</td>
                                                    <td colspan="3">
                                                        ₹{{ number_format($expRed['newSavings'], 0, '.', ',') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="alert alert-light border mt-3">
                                        <i class="bi bi-check-circle-fill text-success"></i> <strong>✅ Achievable!</strong>
                                        By eliminating all reducible expenses AND extending timeline by
                                        {{ $expRed['combinedExtension'] }} year(s), your goals become feasible.
                                    </div>
                                @endif

                                {{-- If still not achievable even with everything --}}
                                @if (!($expRed['achievable'] ?? false))
                                    <div class="alert alert-light border mt-3">
                                        <i class="bi bi-x-circle-fill text-danger"></i> <strong>Not Achievable:</strong>
                                        Even after
                                        reducing all expenses, the plan is not feasible.
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                @endif

                {{-- ================= CASE 3: IMPOSSIBLE ================= --}}
                @if ($status === 'impossible')
                    <div class="alert status-error mb-4 rounded-0">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
                            <h4 class="fw-bold mb-0">🚫 Goals Not Achievable</h4>
                        </div>
                        <p class="mb-3">{{ $data['recommendations']['message'] }}</p>
                        <div class="bg-white p-3 rounded">
                            <h6 class="fw-semibold mb-2">Recommended Actions:</h6>
                            <ul class="mb-0">
                                <li>Increase your monthly income</li>
                                <li>Reduce goal amounts</li>
                                <li>Remove lower priority goals</li>
                                <li>Consider alternative investment strategies with higher returns (with appropriate risk
                                    assessment)</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Show original plan for reference --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2">Original Plan (Reference)</h5>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Goal</th>
                                        <th>Amount (₹)</th>
                                        <th>Years</th>
                                        <th>SIP @8%</th>
                                        <th>SIP @10%</th>
                                        <th>SIP @12%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['goalSIPs'] as $goal)
                                        <tr>
                                            <td>{{ $goal['name'] }}</td>
                                            <td>{{ number_format($goal['amount'], 0, '.', ',') }}</td>
                                            <td>{{ $goal['years'] }}</td>
                                            <td>₹{{ number_format($goal['sip8'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($goal['sip10'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($goal['sip12'], 0, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
