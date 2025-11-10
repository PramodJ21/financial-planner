@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card sharp-card p-4">
            <h2 class="fw-bold text-center mb-4">Smart Goal Planner</h2>

            {{-- Progress Indicator --}}
            <div class="progress mb-4" style="height: 6px;">
                <div id="progressBar" class="progress-bar bg-dark" role="progressbar" style="width: 25%;"></div>
            </div>

            {{-- Step 1 — Goals --}}
            <div class="step" id="step1">
                <h5 class="fw-semibold border-bottom pb-2 d-flex justify-content-between align-items-center">
                    🎯 Your Financial Goals
                    <button type="button" class="btn btn-sm btn-dark" id="addGoalBtn">+ Add Goal</button>
                </h5>
                <p class="text-muted">Let’s start by listing what you want to achieve — like buying a car, house, or
                    vacation.</p>

                <div id="goalsContainer" class="mt-3"></div>

                <div class="text-end mt-4">
                    <button class="btn btn-dark" id="next1">Next →</button>
                </div>
            </div>

            {{-- Step 2 — Basic Info --}}
            <div class="step d-none" id="step2">
                <h5 class="fw-semibold border-bottom pb-2">👤 About You</h5>
                <p class="text-muted">Tell us a bit about yourself so we can calculate what’s realistic for you.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Your Current Age (in years)</label>
                        <input type="number" class="form-control" id="age" placeholder="e.g. 28">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Your Monthly Take-Home Income (₹)</label>
                        <input type="number" class="form-control" id="income" placeholder="e.g. 50,000">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-outline-dark" id="back1">← Back</button>
                    <button class="btn btn-dark" id="next2">Next →</button>
                </div>
            </div>

            {{-- Step 3 — Monthly Expenses --}}
            <div class="step d-none" id="step3">
                <h5 class="fw-semibold border-bottom pb-2">💸 Your Monthly Expenses</h5>
                <p class="text-muted">We’ll estimate how much you can save based on your current lifestyle.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Fixed Costs (like rent, EMIs, insurance)</label>
                        <input type="number" class="form-control" id="nonNegExp" placeholder="e.g. 20,000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Utilities (like electricity, groceries)</label>
                        <input type="number" class="form-control" id="utilityExp" placeholder="e.g. 6,000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Leisure (eating out, movies, etc.)</label>
                        <input type="number" class="form-control" id="luxuryExp" placeholder="e.g. 3,000">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-outline-dark" id="back2">← Back</button>
                    <button class="btn btn-dark" id="next3">Next →</button>
                </div>
            </div>

            {{-- Step 4 — Yearly Expenses --}}
            <div class="step d-none" id="step4">
                <h5 class="fw-semibold border-bottom pb-2">📅 Yearly Expenses</h5>
                <p class="text-muted">Include big yearly costs like insurance, travel, or taxes.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Insurance, Travel, or Tax (₹/year)</label>
                        <input type="number" class="form-control" id="yearlyExp" placeholder="e.g. 20,000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Other Yearly Costs (₹/year)</label>
                        <input type="number" class="form-control" id="yearlyOtherExp" placeholder="e.g. 10,000">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-outline-dark" id="back3">← Back</button>
                    <button class="btn btn-success px-5 py-2" id="calculateBtn">Calculate My Plan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let step = 1;
            const totalSteps = 4;

            const updateProgress = () => {
                document.getElementById('progressBar').style.width = `${(step / totalSteps) * 100}%`;
            };

            const showStep = (s) => {
                document.querySelectorAll('.step').forEach(el => el.classList.add('d-none'));
                document.getElementById(`step${s}`).classList.remove('d-none');
                step = s;
                updateProgress();
            };

            // Navigation buttons
            document.getElementById('next1').addEventListener('click', () => showStep(2));
            document.getElementById('back1').addEventListener('click', () => showStep(1));
            document.getElementById('next2').addEventListener('click', () => showStep(3));
            document.getElementById('back2').addEventListener('click', () => showStep(2));
            document.getElementById('next3').addEventListener('click', () => showStep(4));
            document.getElementById('back3').addEventListener('click', () => showStep(3));

            // Dynamic Goal Adding
            let goalCount = 0;
            document.getElementById('addGoalBtn').addEventListener('click', () => {
                goalCount++;
                const container = document.getElementById('goalsContainer');
                container.insertAdjacentHTML('beforeend', `
                <div class="card sharp-card p-3 mb-3" id="goal-${goalCount}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">Goal ${goalCount}</h6>
                        <button class="btn btn-sm btn-outline-dark" onclick="removeGoal(${goalCount})">Remove</button>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label">What do you want to achieve?</label>
                            <input type="text" class="form-control goal-name" placeholder="e.g. Buy a Car">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">How much would it cost today (₹)?</label>
                            <input type="number" class="form-control goal-amount" placeholder="e.g. 8,00,000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">When do you want to achieve it (in years)?</label>
                            <input type="number" class="form-control goal-years" placeholder="e.g. 5">
                        </div>
                    </div>
                </div>
            `);
            });

            // Remove Goal
            window.removeGoal = function(id) {
                document.getElementById(`goal-${id}`).remove();
            };

            // Submit to backend
            document.getElementById('calculateBtn').addEventListener('click', () => {
                const goals = Array.from(document.querySelectorAll('#goalsContainer .card')).map(g => ({
                    name: g.querySelector('.goal-name').value,
                    amount: parseFloat(g.querySelector('.goal-amount').value) || 0,
                    years: parseInt(g.querySelector('.goal-years').value) || 0
                }));

                const expenses = {
                    monthly: {
                        nonNeg: parseFloat(document.getElementById('nonNegExp').value) || 0,
                        utility: parseFloat(document.getElementById('utilityExp').value) || 0,
                        luxury: parseFloat(document.getElementById('luxuryExp').value) || 0,
                    },
                    yearly: {
                        main: parseFloat(document.getElementById('yearlyExp').value) || 0,
                        other: parseFloat(document.getElementById('yearlyOtherExp').value) || 0
                    }
                };

                const payload = {
                    age: parseInt(document.getElementById('age').value),
                    income: parseFloat(document.getElementById('income').value),
                    goals: goals,
                    expenses: expenses
                };

                fetch("{{ route('goalplanner.calculate', [], true) }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'error') {
                            Swal.fire('Oops!', res.message, 'error');
                        } else if (res.redirect) {
                            window.location.href = res.redirect;
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Something went wrong, please try again.', 'error');
                        console.error(err);
                    });
            });
        });
    </script>
@endpush
