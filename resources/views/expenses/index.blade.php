@extends('layouts.layout_app')

@section('content')
    <h1>Expenses</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <a href="{{ route('expenses_create') }}" class="btn btn-primary mb-3">Add Expense</a>

    <div id="expenses-container">
        @include('expenses.partials/expense-list', ['groupedExpenses' => $groupedExpenses])
    </div>

    <!-- Load More Button -->
    <div id="load-more-container" class="text-center mt-4">
        <button id="load-more" class="btn btn-secondary" data-next-cursor="{{ $expenses->nextCursor() }}">
            Load More
        </button>
    </div>

    <!-- Hidden Input for Next Cursor -->
    <input type="hidden" id="next-cursor" value="{{ $expenses->nextCursor() }}">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreButton = document.getElementById('load-more');
            const expensesContainer = document.getElementById('expenses-container');
            const nextCursorInput = document.getElementById('next-cursor');

            loadMoreButton.addEventListener('click', function() {
                const nextCursor = nextCursorInput.value;

                if (!nextCursor) {
                    loadMoreButton.disabled = true;
                    loadMoreButton.textContent = 'No More Expenses';
                    return;
                }

                // Make an AJAX request to fetch the next set of expenses
                fetch(`/expenses/load-more?cursor=${nextCursor}`)
                    .then(response => response.text())
                    .then(html => {
                        // Append the new expenses to the container
                        expensesContainer.insertAdjacentHTML('beforeend', html);

                        // Update the next cursor
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newNextCursor = doc.querySelector('#next-cursor').value;
                        nextCursorInput.value = newNextCursor;

                        // Disable the button if there are no more expenses
                        if (!newNextCursor) {
                            loadMoreButton.disabled = true;
                            loadMoreButton.textContent = 'No More Expenses';
                        }
                    })
                    .catch(error => console.error('Error loading more expenses:', error));
            });
        });
    </script>
@endsection
