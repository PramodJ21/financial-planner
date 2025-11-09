@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card sharp-card p-4 mb-4">
            <h2 class="fw-bold text-center mb-4">Your Goal Plan Summary</h2>

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
                        <div class="text-center p-3 bg-primary text-white rounded">
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
                <div class="alert alert-success mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold text-success mb-1">✅ Your Plan is Achievable!</h5>
                            <p class="mb-0">Your current savings are sufficient to meet all goals as planned.</p>
                        </div>
                    </div>
                </div>

                {{-- Original Goal Plan --}}
                <div class="mb-4">
                    <h5 class="fw-semibold border-bottom pb-2">Goal-wise SIP Breakdown</h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered text-center">
                            <thead class="table-success">
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
                                <tr class="table-success fw-bold">
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
                <div class="alert alert-warning mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">⚠️ Adjustments Required</h5>
                            <p class="mb-0">Your current savings
                                (₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}) are insufficient. Required:
                                ₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }} @8%</p>
                        </div>
                    </div>
                </div>

                {{-- Original Plan Summary --}}
                <div class="mb-4">
                    <h5 class="fw-semibold border-bottom pb-2 text-danger">
                        <i class="bi bi-x-circle"></i> Original Plan (Not Achievable)
                    </h5>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered text-center">
                            <thead class="table-danger">
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
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3">TOTAL REQUIRED</td>
                                    <td>₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }}</td>
                                    <td>₹{{ number_format($data['requiredSIP10'], 0, '.', ',') }}</td>
                                    <td>₹{{ number_format($data['requiredSIP12'], 0, '.', ',') }}</td>
                                </tr>
                                <tr class="table-light fw-bold">
                                    <td colspan="3">YOUR AVAILABLE SAVINGS</td>
                                    <td colspan="3" class="text-danger">
                                        ₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Solution 1: Extended Timeline --}}
                @if (!empty($data['recommendations']['extendedGoals']))
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2 text-primary">
                            <i class="bi bi-calendar-plus"></i> Solution 1: Extended Timeline
                            @if (!empty($data['recommendations']['yearsAdded']))
                                <span class="badge bg-primary">+{{ $data['recommendations']['yearsAdded'] }} Year(s)</span>
                            @endif
                        </h5>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th rowspan="2">Goal</th>
                                        <th rowspan="2">Amount</th>
                                        <th colspan="2">Timeline</th>
                                        <th colspan="3">Required SIP (₹/month)</th>
                                    </tr>
                                    <tr>
                                        <th>Old</th>
                                        <th>New</th>
                                        <th>@8%</th>
                                        <th>@10%</th>
                                        <th>@12%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data['recommendations']['extendedGoals'] as $g)
                                        <tr>
                                            <td>{{ $g['name'] }}</td>
                                            <td>₹{{ number_format($g['amount'], 0, '.', ',') }}</td>
                                            <td><span class="text-muted">{{ $g['oldYears'] }}y</span></td>
                                            <td><span class="badge bg-primary">{{ $g['newYears'] }}y</span></td>
                                            <td>₹{{ number_format($g['newSIP8'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($g['newSIP10'], 0, '.', ',') }}</td>
                                            <td>₹{{ number_format($g['newSIP12'], 0, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="4">TOTAL REQUIRED (Extended)</td>
                                        <td>₹{{ number_format($data['recommendations']['adjustedTotals']['sip8'], 0, '.', ',') }}
                                        </td>
                                        <td>₹{{ number_format($data['recommendations']['adjustedTotals']['sip10'], 0, '.', ',') }}
                                        </td>
                                        <td>₹{{ number_format($data['recommendations']['adjustedTotals']['sip12'], 0, '.', ',') }}
                                        </td>
                                    </tr>
                                    <tr
                                        class="fw-bold
                                        @if ($data['recommendations']['timelineSuccess'] ?? false) table-success
                                        @else
                                            table-warning @endif
                                    ">
                                        <td colspan="4">YOUR AVAILABLE SAVINGS</td>
                                        <td colspan="3">₹{{ number_format($data['monthlySavings'], 0, '.', ',') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if ($data['recommendations']['timelineSuccess'] ?? false)
                            <div class="alert alert-success mt-3">
                                <i class="bi bi-check-circle-fill"></i> <strong>✅ Achievable!</strong> By extending your
                                goals by {{ $data['recommendations']['yearsAdded'] }} year(s), your plan becomes feasible
                                across all return scenarios.
                            </div>
                        @else
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-circle-fill"></i> <strong>Still Insufficient:</strong> Timeline
                                extension alone is not enough. See expense reduction solution below.
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
                        <h5 class="fw-semibold border-bottom pb-2 text-success">
                            <i class="bi bi-scissors"></i> Solution 2: Reduce Expenses
                            @if (!empty($expRed['combinedExtension']))
                                + Extended Timeline
                            @endif
                        </h5>

                        @if (!empty($expRed['noReducible']))
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle-fill"></i> No reducible expenses available (Utility and Luxury are
                                already zero).
                            </div>
                        @else
                            <div class="alert alert-info">
                                <strong>Strategy:</strong>
                                @if ($expRed['originalTimeline'] ?? false)
                                    Reduce expenses with original timeline first (Priority: Luxury → Utility)
                                @endif
                            </div>

                            {{-- Expense Comparison --}}
                            <div class="row mt-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark fw-bold">
                                            <i class="bi bi-wallet2"></i> Current Expenses
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm mb-0">
                                                <tr>
                                                    <td>Utility</td>
                                                    <td class="text-end fw-bold">
                                                        ₹{{ number_format($expRed['oldUtility'] ?? 0, 0, '.', ',') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Luxury</td>
                                                    <td class="text-end fw-bold">
                                                        ₹{{ number_format($expRed['oldLuxury'] ?? 0, 0, '.', ',') }}</td>
                                                </tr>
                                                <tr class="table-secondary">
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
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white fw-bold">
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
                                                <tr class="table-success">
                                                    <td><strong>New Savings</strong></td>
                                                    <td class="text-end fw-bold text-success">
                                                        ₹{{ number_format($expRed['newSavings'], 0, '.', ',') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- If expense reduction with original timeline works --}}
                            @if (($expRed['achievable'] ?? false) && ($expRed['originalTimeline'] ?? false) && empty($expRed['combinedExtension']))
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill"></i> <strong>✅ Achievable!</strong> By reducing
                                    expenses by ₹{{ number_format($expRed['reduceBy'], 0, '.', ',') }}/month, you can
                                    achieve all goals with the original timeline.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="table-success">
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
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="3">TOTAL REQUIRED</td>
                                                <td>₹{{ number_format($data['requiredSIP8'], 0, '.', ',') }}</td>
                                                <td>₹{{ number_format($data['requiredSIP10'], 0, '.', ',') }}</td>
                                                <td>₹{{ number_format($data['requiredSIP12'], 0, '.', ',') }}</td>
                                            </tr>
                                            <tr class="table-success fw-bold">
                                                <td colspan="3">NEW AVAILABLE SAVINGS</td>
                                                <td colspan="3">
                                                    ₹{{ number_format($expRed['newSavings'], 0, '.', ',') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            {{-- If all expenses reduced but need extension too --}}
                            @if (!empty($expRed['allReduced']) && !empty($expRed['combinedExtension']))
                                <div class="alert alert-warning">
                                    <i class="bi bi-info-circle-fill"></i> <strong>Combined Approach Needed:</strong> All
                                    reducible expenses eliminated. Also extending timeline by
                                    {{ $expRed['combinedExtension'] }} year(s).
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="table-success">
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
                                                    <td><span class="badge bg-primary">{{ $g['newYears'] }}y</span></td>
                                                    <td>₹{{ number_format($g['newSIP8'], 0, '.', ',') }}</td>
                                                    <td>₹{{ number_format($g['newSIP10'], 0, '.', ',') }}</td>
                                                    <td>₹{{ number_format($g['newSIP12'], 0, '.', ',') }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="3">TOTAL REQUIRED</td>
                                                <td>₹{{ number_format($expRed['combinedTotals']['sip8'], 0, '.', ',') }}
                                                </td>
                                                <td>₹{{ number_format($expRed['combinedTotals']['sip10'], 0, '.', ',') }}
                                                </td>
                                                <td>₹{{ number_format($expRed['combinedTotals']['sip12'], 0, '.', ',') }}
                                                </td>
                                            </tr>
                                            <tr class="table-success fw-bold">
                                                <td colspan="3">NEW AVAILABLE SAVINGS</td>
                                                <td colspan="3">
                                                    ₹{{ number_format($expRed['newSavings'], 0, '.', ',') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-success mt-3">
                                    <i class="bi bi-check-circle-fill"></i> <strong>✅ Achievable!</strong> By eliminating
                                    all reducible expenses AND extending timeline by {{ $expRed['combinedExtension'] }}
                                    year(s), your goals become feasible.
                                </div>
                            @endif

                            {{-- If still not achievable even with everything --}}
                            @if (!($expRed['achievable'] ?? false))
                                <div class="alert alert-danger mt-3">
                                    <i class="bi bi-x-circle-fill"></i> <strong>Not Achievable:</strong> Even after
                                    reducing all expenses, the plan is not feasible.
                                </div>
                            @endif
                        @endif
                    </div>
                @endif

            @endif

            {{-- ================= CASE 3: IMPOSSIBLE ================= --}}
            @if ($status === 'impossible')
                <div class="alert alert-danger border-danger mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                        <h4 class="fw-bold text-danger mb-0">🚫 Goals Not Achievable</h4>
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
                            <thead class="table-light">
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

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
