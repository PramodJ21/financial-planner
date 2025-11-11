@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card sharp-card p-4">
            <h2 class="fw-bold text-center mb-4">Smart Goal Planner</h2>

            {{-- Progress Indicator --}}
            <div class="progress mb-4" style="height: 6px;">
                <div id="progressBar" class="progress-bar bg-dark" role="progressbar" style="width: 0%;"></div>
            </div>

            {{-- Step 1 — Goals --}}
            <div class="step" id="step1">
                <h5 class="fw-semibold border-bottom pb-2 d-flex justify-content-between align-items-center">
                    🎯 Your Financial Goals
                    <button type="button" class="btn btn-sm btn-dark" id="addGoalBtn">+ Add Goal</button>
                </h5>
                <p class="text-muted">List what you want to achieve — like buying a car, house, or vacation.</p>

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
                <p class="text-muted">Estimate how much you spend every month.</p>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">Fixed Costs (rent, EMIs, insurance)</label>
                        <input type="number" class="form-control" id="nonNegExp" placeholder="e.g. 20,000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Utilities (electricity, groceries)</label>
                        <input type="number" class="form-control" id="utilityExp" placeholder="e.g. 6,000">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Leisure (dining, movies)</label>
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
                    <button class="btn btn-dark" id="next4">Next →</button>
                </div>
            </div>

            {{-- Step 5 — Review --}}
            <div class="step d-none" id="step5">
                <h5 class="fw-semibold border-bottom pb-2">🧾 Review Your Information</h5>
                <p class="text-muted">Please check that everything below is correct before calculating your plan.</p>

                <div id="reviewContainer" class="mt-3"></div>

                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-outline-dark" id="back4">← Back</button>
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
                completed = step - 1;
                const percent = (completed / totalSteps) * 100;
                document.getElementById('progressBar').style.width = `${percent}%`;
            };

            const showStep = (s) => {
                document.querySelectorAll('.step').forEach(el => el.classList.add('d-none'));
                document.getElementById(`step${s}`).classList.remove('d-none');
                step = s;
                updateProgress();
            };

            // Navigation
            document.getElementById('next1').addEventListener('click', () => {
                if (validateGoals()) showStep(2);
            });
            document.getElementById('back1').addEventListener('click', () => showStep(1));
            document.getElementById('next2').addEventListener('click', () => {
                if (validateBasicInfo()) showStep(3);
            });
            document.getElementById('back2').addEventListener('click', () => showStep(2));
            document.getElementById('next3').addEventListener('click', () => {
                if (validateExpenses()) showStep(4);
            });
            document.getElementById('back3').addEventListener('click', () => showStep(3));
            document.getElementById('next4').addEventListener('click', () => {
                if (validateExpenses()) buildReview();
                showStep(5);
            });
            document.getElementById('back4').addEventListener('click', () => showStep(4));

            // Goals logic
            const container = document.getElementById('goalsContainer');
            const addGoal = () => {
                const idx = container.querySelectorAll('.goal-card').length + 1;
                container.insertAdjacentHTML('beforeend', `
            <div class="card sharp-card p-3 mb-3 goal-card" id="goal-${idx}">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0 goal-title">Goal ${idx}</h6>
                    <button type="button" class="btn btn-sm btn-outline-dark remove-goal-btn" onclick="removeGoal(${idx})">Remove</button>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">What do you want to achieve?</label>
                        <input type="text" class="form-control goal-name" placeholder="e.g. Buy a Car" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">How much would it cost today (₹)?</label>
                        <input type="number" class="form-control goal-amount" placeholder="e.g. 800000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">When do you want to achieve it (in years)?</label>
                        <input type="number" class="form-control goal-years" placeholder="e.g. 5" required>
                    </div>
                </div>
            </div>`);
            };

            document.getElementById('addGoalBtn').addEventListener('click', addGoal);
            window.removeGoal = idx => {
                document.getElementById(`goal-${idx}`)?.remove();
                reindexGoals();
            };

            function reindexGoals() {
                [...container.querySelectorAll('.goal-card')].forEach((card, i) => {
                    const n = i + 1;
                    card.id = `goal-${n}`;
                    card.querySelector('.goal-title').textContent = `Goal ${n}`;
                    card.querySelector('.remove-goal-btn').setAttribute('onclick', `removeGoal(${n})`);
                });
            }

            addGoal();

            // Validation
            function validateGoals() {
                const goals = [...container.querySelectorAll('.goal-card')];
                if (!goals.length) return Swal.fire('Please add at least one goal.'), false;
                for (const g of goals) {
                    const name = g.querySelector('.goal-name').value.trim();
                    const amount = parseFloat(g.querySelector('.goal-amount').value);
                    const years = parseInt(g.querySelector('.goal-years').value);
                    if (!name || !amount || !years || amount <= 0 || years <= 0)
                        return Swal.fire('Please fill all goal details correctly.'), false;
                }
                return true;
            }
            const validateBasicInfo = () => {
                const age = +document.getElementById('age').value;
                const income = +document.getElementById('income').value;
                if (!age || age <= 0 || !income || income <= 0)
                    return Swal.fire('Please provide valid Age and Income.'), false;
                return true;
            };
            const validateExpenses = () => {
                const fields = ['nonNegExp', 'utilityExp', 'luxuryExp', 'yearlyExp', 'yearlyOtherExp'];
                for (const id of fields)
                    if (isNaN(+document.getElementById(id).value))
                        return Swal.fire('Please fill all expense fields.'), false;
                return true;
            };

            // Review builder
            function buildReview() {
                const review = document.getElementById('reviewContainer');
                const goals = [...container.querySelectorAll('.goal-card')].map((g, i) => ({
                    name: g.querySelector('.goal-name').value,
                    amount: g.querySelector('.goal-amount').value,
                    years: g.querySelector('.goal-years').value
                }));

                const htmlGoals = goals.map((g, i) => `
            <li><strong>${g.name}</strong> — ₹${g.amount} in ${g.years} years</li>
        `).join('');

                review.innerHTML = `
            <h6 class="fw-semibold mb-2">🎯 Goals</h6>
            <ul>${htmlGoals}</ul>
            <h6 class="fw-semibold mt-3 mb-2">👤 Basic Info</h6>
            <p>Age: ${document.getElementById('age').value} years<br>
            Monthly Income: ₹${document.getElementById('income').value}</p>
            <h6 class="fw-semibold mt-3 mb-2">💸 Monthly Expenses</h6>
            <p>Fixed: ₹${document.getElementById('nonNegExp').value}, Utilities: ₹${document.getElementById('utilityExp').value}, Leisure: ₹${document.getElementById('luxuryExp').value}</p>
            <h6 class="fw-semibold mt-3 mb-2">📅 Yearly Expenses</h6>
            <p>Insurance/Travel/Tax: ₹${document.getElementById('yearlyExp').value}, Others: ₹${document.getElementById('yearlyOtherExp').value}</p>
        `;
            }

            // Final submission
            document.getElementById('calculateBtn').addEventListener('click', () => {
                const payload = {
                    age: +document.getElementById('age').value,
                    income: +document.getElementById('income').value,
                    goals: [...container.querySelectorAll('.goal-card')].map(g => ({
                        name: g.querySelector('.goal-name').value,
                        amount: +g.querySelector('.goal-amount').value,
                        years: +g.querySelector('.goal-years').value
                    })),
                    expenses: {
                        monthly: {
                            nonNeg: +document.getElementById('nonNegExp').value,
                            utility: +document.getElementById('utilityExp').value,
                            luxury: +document.getElementById('luxuryExp').value
                        },
                        yearly: {
                            main: +document.getElementById('yearlyExp').value,
                            other: +document.getElementById('yearlyOtherExp').value
                        }
                    }
                };

                fetch("{{ route('goalplanner.calculate', [], true) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'error') Swal.fire('Oops!', res.message, 'error');
                        else if (res.redirect) window.location.href = res.redirect;
                    })
                    .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
            });

            updateProgress();
        });
    </script>
@endpush
