@extends('layouts.layout_app')

@section('content')
    <h1>Edit Expense</h1>

    <form action="{{ route('expenses_update', $expense->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $expense->title) }}"
                required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $expense->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                value="{{ old('amount', $expense->amount) }}" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control"
                value="{{ old('date', $expense->date) }}" required>
        </div>
        <div class="mb-3">
            <label for="expense_proof" class="form-label">Upload Proof (Image or PDF, Max 10MB)</label>
            <input type="file" name="expense_proof" id="expense_proof" class="form-control">
            @if ($expense->proof)
                <p class="mt-2"><strong>Current Proof:</strong></p>
                @if (pathinfo($expense->expense_proof, PATHINFO_EXTENSION) === 'pdf')
                    <a href="{{ asset('storage/' . $expense->expense_proof) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">View Current PDF</a>
                @else
                    <img src="{{ asset('storage/' . $expense->expense_proof) }}" alt="Current Proof"
                        class="img-thumbnail mt-2" style="max-width: 200px;">
                @endif
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Update Expense</button>
    </form>
@endsection
