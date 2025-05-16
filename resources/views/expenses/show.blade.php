@extends('layouts.layout_app')

@section('content')
    <h1>Expenses for {{ $date }}</h1>

    <a href="{{ route('expenses_index') }}" class="btn btn-secondary mb-3">Back</a>

    <ul class="list-group">
        @foreach ($expenses as $expense)
            <li class="list-group-item">
                {{ $expense->title }} - ₹{{ number_format($expense->amount, 2) }}
            </li>
        @endforeach
    </ul>
@endsection
