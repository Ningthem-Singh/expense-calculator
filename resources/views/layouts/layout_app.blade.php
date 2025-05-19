<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Calculator</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome6.css') }}">
</head>

<body>
    <div class="container my-5">
        @yield('content')
    </div>
</body>


<script src="{{ asset('assets/js/bootstrap5.min.js') }}"></script>
@yield('scripts')

</html>
