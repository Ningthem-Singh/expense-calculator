@extends('layouts.layout_app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h1>Edit Expense</h1>

            <form action="{{ route('expenses_update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control"
                        value="{{ old('title', $expense->title) }}" required>
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
                    @if ($expense->expense_proof)
                        <p class="mt-2"><strong>Current Proof:</strong></p>
                        <div class="d-flex align-items-center mb-2">
                            @if (pathinfo($expense->expense_proof, PATHINFO_EXTENSION) === 'pdf')
                                <!-- PDF Preview -->
                                <a href="{{ asset('storage/' . $expense->expense_proof) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                    View Current PDF
                                </a>
                            @else
                                <!-- Image Preview -->
                                <img src="{{ asset('storage/' . $expense->expense_proof) }}" alt="Current Proof"
                                    class="img-thumbnail me-2" style="max-width: 100px;">
                            @endif

                            <!-- Checkbox to Remove Proof -->
                            <div class="form-check">
                                <input type="checkbox" name="remove_proof" id="remove_proof" value="1"
                                    class="form-check-input">
                                <label for="remove_proof" class="form-check-label">Remove Proof</label>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary">Update Expense</button>
            </form>
        </div>
    </div>
@endsection
