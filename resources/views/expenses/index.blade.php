@extends('layouts.layout_app')

@section('content')
    <!-- Hidden Input for Next Cursor -->
    <input type="hidden" id="next-cursor" value="{{ $nextCursor }}">

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
    <a href="{{ route('expenses_calendar') }}" class="btn btn-primary mb-3">Show in Calendar style</a>

    <div id="expenses-container">
        @include('expenses.partials/expense_list', ['groupedExpenses' => $groupedExpenses])
    </div>

    <!-- Load More Button -->
    <div id="load-more-container" class="text-center mt-4">
        <button id="load-more" class="btn btn-secondary" data-next-cursor="{{ $nextCursor }}"
            {{ !$nextCursor ? 'disabled' : '' }}>
            {{ !$nextCursor ? 'No More Expenses' : 'Load More' }}
        </button>
    </div>

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

                // Show loading state
                loadMoreButton.disabled = true;
                loadMoreButton.textContent = 'Loading...';

                // Use the named route to generate the URL
                fetch(`{{ route('expenses_load_more') }}?cursor=${nextCursor}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
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
                        } else {
                            loadMoreButton.disabled = false;
                            loadMoreButton.textContent = 'Load More';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading more expenses:', error);
                        loadMoreButton.disabled = false;
                        loadMoreButton.textContent = 'Load More';
                    });
            });
        });
    </script>
@endsection
