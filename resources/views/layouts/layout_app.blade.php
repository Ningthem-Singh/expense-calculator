<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Calculator</title>
    <link href="{{ asset('assets/css/bootstrap5.min.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        @yield('content')
    </div>
</body>


<script src="{{ asset('assets/js/bootstrap5.min.js') }}"></script>
@yield('scripts')

</html>
