@extends('layouts.layout_app')

@section('content')
    <h1>Expenses</h1>

    <a href="{{ route('expenses_create') }}" class="btn btn-primary mb-3">Add Expense</a>

    @foreach ($expenses as $date => $dailyExpenses)
        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ $date }}</strong>
                <a href="{{ route('expenses_showByDate', $date) }}" class="float-end">View Details</a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($dailyExpenses as $expense)
                    <li class="list-group-item">
                        {{ $expense->title }} - ₹{{ number_format($expense->amount, 2) }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
@endsection
