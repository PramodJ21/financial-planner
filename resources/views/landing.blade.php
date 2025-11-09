@extends('layouts.app')

@section('content')
    <div class="container text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
        <h1 class="display-4 fw-bold mb-3">Smart Financial Planner</h1>
        <p class="lead mb-4">Plan your financial goals, optimize expenses, and reach your dreams with data-driven insights.
        </p>

        <a href="{{ route('goalplanner.index') }}" class="btn btn-primary px-4 py-2">
            Go to Goal Planner →
        </a>
    </div>
@endsection
