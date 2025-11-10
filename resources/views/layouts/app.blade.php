<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Planner</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #fff;
            color: #000;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .navbar,
        footer {
            background-color: #000;
            color: #fff;
        }

        .navbar a,
        footer a {
            color: #fff !important;
        }

        .btn-primary {
            background-color: #000;
            border: none;
            color: #fff;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #222;
        }

        .container {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        footer {
            padding: 1rem 0;
            text-align: center;
            font-size: 0.9rem;
        }

        .sharp-card {
            border-radius: 0;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
        }
    </style>
    @yield('customCss')
    @stack('styles')
</head>

<body>
    @include('partials.header')

    <main class="flex-grow-1">
        @yield('content')
    </main>
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
