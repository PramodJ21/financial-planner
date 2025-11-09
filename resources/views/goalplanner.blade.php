@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card sharp-card p-4">
                    <h2 class="fw-bold mb-4 text-center">Goal Planner</h2>

                    {{-- Basic Info --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2">Basic Information</h5>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Current Age</label>
                                <input type="number" class="form-control" id="age" placeholder="e.g. 28">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Current Monthly Income (₹)</label>
                                <input type="number" class="form-control" id="income" placeholder="e.g. 50000">
                            </div>
                        </div>
                    </div>

                    {{-- Goals --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2 d-flex justify-content-between align-items-center">
                            Goals
                            <button type="button" class="btn btn-sm btn-primary" id="addGoalBtn">+ Add Goal</button>
                        </h5>
                        <div id="goalsContainer"></div>
                    </div>

                    {{-- Monthly Expenses --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2">Monthly Expenses</h5>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Non-Negotiable (₹)</label>
                                <input type="number" class="form-control" id="nonNegExp" placeholder="e.g. 15000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Utility (₹)</label>
                                <input type="number" class="form-control" id="utilityExp" placeholder="e.g. 5000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Luxury (₹)</label>
                                <input type="number" class="form-control" id="luxuryExp" placeholder="e.g. 3000">
                            </div>
                        </div>
                    </div>

                    {{-- Yearly Expenses --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold border-bottom pb-2">Yearly Expenses</h5>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Insurance / Tax / Travel (₹ per year)</label>
                                <input type="number" class="form-control" id="yearlyExp" placeholder="e.g. 20000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Other Yearly Costs (₹ per year)</label>
                                <input type="number" class="form-control" id="yearlyOtherExp" placeholder="e.g. 10000">
                            </div>
                        </div>
                    </div>

                    {{-- Calculate Button --}}
                    <div class="text-center">
                        <button id="calculateBtn" class="btn btn-primary px-5 py-2 mt-3">Calculate Plan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let goalCount = 0;

            // Add Goal dynamically
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
                        <label class="form-label">Goal Name</label>
                        <input type="text" class="form-control goal-name" placeholder="e.g. Buy a Car">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount Required (₹)</label>
                        <input type="number" class="form-control goal-amount" placeholder="e.g. 800000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Years to Goal</label>
                        <input type="number" class="form-control goal-years" placeholder="e.g. 5">
                    </div>
                </div>
            </div>
        `);
            });

            // Handle Calculate click → send to backend
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

                fetch("{{ route('goalplanner.calculate') }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json()) // ✅ Convert to JSON first
                    .then(res => {
                        if (res.status === 'error') {
                            Swal.fire('Oops!', res.message, 'error');
                        } else if (res.redirect) {
                            window.location.href = res.redirect; // ✅ redirect to result page
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Something went wrong, please try again.', 'error');
                        console.error(err);
                    });
            });
        });

        function removeGoal(id) {
            document.getElementById(`goal-${id}`).remove();
        }
    </script>
@endpush
