@extends('layouts.layout_app')

@section('content')
    <h1>Expenses for {{ $date }}</h1>

    <a href="{{ route('expenses_index') }}" class="btn btn-secondary mb-3">Back</a>

    <ul class="list-group" style="border: 1px solid black">
        @foreach ($expenses as $expense)
            <li class="list-group-item" style="border: 2px solid grey">
                <b>{{ $expense->title }} - ₹{{ number_format($expense->amount, 2) }}</b>
                @if ($expense->description)
                    <p class="mt-2"><em>{{ $expense->description }}</em></p>
                @endif
                @if ($expense->expense_proof)
                    <div class="mt-2">
                        @if (pathinfo($expense->expense_proof, PATHINFO_EXTENSION) === 'pdf')
                            <!-- PDF File -->
                            <a href="{{ asset('storage/' . $expense->expense_proof) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                View PDF Proof
                            </a>
                        @else
                            <!-- Image File -->
                            <img src="{{ asset('storage/' . $expense->expense_proof) }}" alt="Expense Proof"
                                class="img-thumbnail" style="max-width: 200px;">
                        @endif
                    </div>
                @endif
            </li>
        @endforeach
    </ul>
@endsection
