<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GoalPlannerController extends Controller
{
    public function index()
    {
        return view('goalplanner');
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'age' => 'required|numeric|min:1',
            'income' => 'required|numeric|min:1',
            'goals' => 'required|array|min:1',
            'expenses' => 'nullable|array',
        ]);

        $income = $data['income'];
        $goals = $data['goals'];
        $expenses = $data['expenses'] ?? [];

        // 🧾 Monthly expense breakdown
        $monthlyExp = $expenses['monthly'] ?? [];
        $yearlyExp = $expenses['yearly'] ?? [];

        $nonNeg = $monthlyExp['nonNeg'] ?? 0;  // Non-negotiable
        $utility = $monthlyExp['utility'] ?? 0;
        $luxury = $monthlyExp['luxury'] ?? 0;

        $totalMonthlyExpenses = $nonNeg + $utility + $luxury;
        $totalYearlyExpenses = array_sum($yearlyExp);
        $yearlyToMonthly = round($totalYearlyExpenses / 12, 2);
        $totalEffectiveExpenses = $totalMonthlyExpenses + $yearlyToMonthly;

        $monthlySavings = $income - $totalEffectiveExpenses;

        // 🎯 SIP formula
        $calcSIP = function ($fv, $rate, $years) {
            $r = $rate / 12;
            $n = $years * 12;
            return $fv / (((pow(1 + $r, $n) - 1) / $r) * (1 + $r));
        };

        // 🧮 Per-goal SIPs (original)
        $goalSIPs = [];
        $totalGoals = 0;
        $totalSIP8 = $totalSIP10 = $totalSIP12 = 0;

        foreach ($goals as $g) {
            $totalGoals += $g['amount'];
            $sip8 = $calcSIP($g['amount'], 0.08, $g['years']);
            $sip10 = $calcSIP($g['amount'], 0.10, $g['years']);
            $sip12 = $calcSIP($g['amount'], 0.12, $g['years']);

            $goalSIPs[] = [
                'name' => $g['name'],
                'amount' => $g['amount'],
                'years' => $g['years'],
                'sip8' => round($sip8),
                'sip10' => round($sip10),
                'sip12' => round($sip12),
            ];

            $totalSIP8 += $sip8;
            $totalSIP10 += $sip10;
            $totalSIP12 += $sip12;
        }

        $recommendations = [];

        // ========================= CASE 1: Enough funds =========================
        if ($monthlySavings >= $totalSIP8) {
            $recommendations['status'] = 'sufficient';
            $recommendations['message'] = '✅ Your savings are sufficient to meet all goals as planned.';
        }

        // ========================= CASE 2 & 3: Not enough funds =========================
        else {
            $recommendations['status'] = 'insufficient';
            $recommendations['message'] =
                '⚠️ Your current savings are not sufficient. Calculating optimal adjustments...';

            // ==================== SOLUTION 1: EXTEND TIMELINE ====================
            // Keep extending years until SIP @8% <= available savings or max 20 years added
            $maxExtension = 20;
            $extendedGoals = [];
            $yearsAdded = 0;
            $newTotalSIP8 = $newTotalSIP10 = $newTotalSIP12 = 0;

            // Start extending
            for ($extension = 1; $extension <= $maxExtension; $extension++) {
                $tempSIP8 = $tempSIP10 = $tempSIP12 = 0;
                $tempExtendedGoals = [];

                foreach ($goals as $g) {
                    $newYears = $g['years'] + $extension;

                    $oldSIP8 = $calcSIP($g['amount'], 0.08, $g['years']);
                    $oldSIP10 = $calcSIP($g['amount'], 0.10, $g['years']);
                    $oldSIP12 = $calcSIP($g['amount'], 0.12, $g['years']);

                    $newSIP8 = $calcSIP($g['amount'], 0.08, $newYears);
                    $newSIP10 = $calcSIP($g['amount'], 0.10, $newYears);
                    $newSIP12 = $calcSIP($g['amount'], 0.12, $newYears);

                    $tempSIP8 += $newSIP8;
                    $tempSIP10 += $newSIP10;
                    $tempSIP12 += $newSIP12;

                    $tempExtendedGoals[] = [
                        'name' => $g['name'],
                        'amount' => $g['amount'],
                        'oldYears' => $g['years'],
                        'newYears' => $newYears,
                        'oldSIP8' => round($oldSIP8),
                        'oldSIP10' => round($oldSIP10),
                        'oldSIP12' => round($oldSIP12),
                        'newSIP8' => round($newSIP8),
                        'newSIP10' => round($newSIP10),
                        'newSIP12' => round($newSIP12),
                    ];
                }

                // Check if this extension makes it achievable
                if ($tempSIP8 <= $monthlySavings) {
                    $yearsAdded = $extension;
                    $extendedGoals = $tempExtendedGoals;
                    $newTotalSIP8 = $tempSIP8;
                    $newTotalSIP10 = $tempSIP10;
                    $newTotalSIP12 = $tempSIP12;
                    break;
                }
            }

            // Store extended goals result
            if ($yearsAdded > 0) {
                $recommendations['extendedGoals'] = $extendedGoals;
                $recommendations['yearsAdded'] = $yearsAdded;
                $recommendations['adjustedTotals'] = [
                    'sip8' => round($newTotalSIP8),
                    'sip10' => round($newTotalSIP10),
                    'sip12' => round($newTotalSIP12),
                ];

                if ($newTotalSIP8 <= $monthlySavings) {
                    $recommendations['timelineSuccess'] = true;
                    $recommendations['message'] = "✅ By extending your goals by {$yearsAdded} year(s), your plan becomes achievable.";
                } else {
                    $recommendations['timelineSuccess'] = false;
                }
            } else {
                // Could not achieve even with max extension
                $recommendations['timelineSuccess'] = false;
                $recommendations['yearsAdded'] = $maxExtension;

                // Use the last calculated values (max extension)
                $recommendations['extendedGoals'] = $tempExtendedGoals ?? [];
                $recommendations['adjustedTotals'] = [
                    'sip8' => round($tempSIP8),
                    'sip10' => round($tempSIP10),
                    'sip12' => round($tempSIP12),
                ];
            }

            // ==================== SOLUTION 2: REDUCE EXPENSES ====================
            // Try reducing expenses with ORIGINAL timeline first
            $totalReducible = $utility + $luxury;
            $requiredAdditionalSavings = $totalSIP8 - $monthlySavings;

            if ($totalReducible > 0 && $requiredAdditionalSavings > 0) {
                if ($requiredAdditionalSavings <= $totalReducible) {
                    // Can achieve with expense reduction alone (original timeline)
                    $reduceBy = $requiredAdditionalSavings;

                    // Priority: Luxury first, then utility
                    $luxuryReduction = min($luxury, $reduceBy);
                    $utilityReduction = min($utility, $reduceBy - $luxuryReduction);

                    $luxuryReduced = $luxury - $luxuryReduction;
                    $utilityReduced = $utility - $utilityReduction;
                    $newSavings = $monthlySavings + $reduceBy;

                    $recommendations['expenseReduction'] = [
                        'achievable' => true,
                        'originalTimeline' => true,
                        'reduceBy' => round($reduceBy),
                        'oldUtility' => round($utility),
                        'oldLuxury' => round($luxury),
                        'newUtility' => round($utilityReduced),
                        'newLuxury' => round($luxuryReduced),
                        'newSavings' => round($newSavings),
                    ];
                } else {
                    // Reduce all expenses but still not enough - need extension too
                    $reduceBy = $totalReducible;
                    $newSavings = $monthlySavings + $reduceBy;

                    // Priority: Luxury first, then utility (reduce all)
                    $luxuryReduction = $luxury;
                    $utilityReduction = $utility;
                    $luxuryReduced = 0;
                    $utilityReduced = 0;

                    $recommendations['expenseReduction'] = [
                        'achievable' => false,
                        'allReduced' => true,
                        'originalTimeline' => true,
                        'reduceBy' => round($reduceBy),
                        'oldUtility' => round($utility),
                        'oldLuxury' => round($luxury),
                        'newUtility' => round($utilityReduced),
                        'newLuxury' => round($luxuryReduced),
                        'newSavings' => round($newSavings),
                    ];

                    // Now try with extended timeline + full expense reduction
                    $maxExtension2 = 20;
                    $combinedSuccess = false;

                    for ($extension = 1; $extension <= $maxExtension2; $extension++) {
                        $tempSIP8 = 0;
                        $tempExtendedGoals2 = [];

                        foreach ($goals as $g) {
                            $newYears = $g['years'] + $extension;
                            $newSIP8 = $calcSIP($g['amount'], 0.08, $newYears);
                            $newSIP10 = $calcSIP($g['amount'], 0.10, $newYears);
                            $newSIP12 = $calcSIP($g['amount'], 0.12, $newYears);

                            $tempSIP8 += $newSIP8;

                            $tempExtendedGoals2[] = [
                                'name' => $g['name'],
                                'amount' => $g['amount'],
                                'oldYears' => $g['years'],
                                'newYears' => $newYears,
                                'newSIP8' => round($newSIP8),
                                'newSIP10' => round($newSIP10),
                                'newSIP12' => round($newSIP12),
                            ];
                        }

                        if ($tempSIP8 <= $newSavings) {
                            $combinedSuccess = true;
                            $recommendations['expenseReduction']['combinedExtension'] = $extension;
                            $recommendations['expenseReduction']['combinedGoals'] = $tempExtendedGoals2;
                            $recommendations['expenseReduction']['combinedTotals'] = [
                                'sip8' => round($tempSIP8),
                                'sip10' => round(array_sum(array_column($tempExtendedGoals2, 'newSIP10'))),
                                'sip12' => round(array_sum(array_column($tempExtendedGoals2, 'newSIP12'))),
                            ];
                            $recommendations['expenseReduction']['achievable'] = true;
                            break;
                        }
                    }

                    if (!$combinedSuccess) {
                        $recommendations['status'] = 'impossible';
                    }
                }
            } elseif ($totalReducible <= 0) {
                // No reducible expenses
                $recommendations['expenseReduction'] = [
                    'achievable' => false,
                    'noReducible' => true,
                ];
            }

            // Final status determination
            if ($recommendations['timelineSuccess'] ?? false) {
                $recommendations['status'] = 'insufficient';
            } elseif (($recommendations['expenseReduction']['achievable'] ?? false)) {
                $recommendations['status'] = 'insufficient';
            } else {
                $recommendations['status'] = 'impossible';
                $recommendations['message'] = '❌ Your goals cannot be achieved even with maximum timeline extensions and expense reductions.';
            }
        }

        // ========================= Store results =========================
        Session::put('planner_result', [
            'totalGoals' => round($totalGoals),
            'totalExpenses' => round($totalMonthlyExpenses),
            'yearlyToMonthly' => round($yearlyToMonthly),
            'monthlySavings' => round($monthlySavings),
            'requiredSIP8' => round($totalSIP8),
            'requiredSIP10' => round($totalSIP10),
            'requiredSIP12' => round($totalSIP12),
            'goalSIPs' => $goalSIPs,
            'recommendations' => $recommendations,
        ]);

        return response()->json(['redirect' => route('goalplanner.result')]);
    }

    public function result()
    {
        $data = Session::get('planner_result');
        if (!$data) {
            return redirect()->route('goalplanner.index')
                ->with('error', 'Session expired. Please re-enter your data.');
        }

        return view('goalplanner_result', compact('data'));
    }
}
