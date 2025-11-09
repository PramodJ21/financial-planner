<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="{{ route('landing') }}">FinancePlanner</a>
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon text-white"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a href="{{ route('landing') }}" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="{{ route('goalplanner.index') }}" class="nav-link">Goal Planner</a></li>
            </ul>
        </div>
    </div>
</nav>
