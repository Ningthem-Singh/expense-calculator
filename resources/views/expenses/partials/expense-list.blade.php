@foreach ($groupedExpenses as $date => $dailyExpenses)
    <div class="card mb-3">
        <div class="card-header">
            <strong>{{ $date }}</strong>
            <a href="{{ route('expenses_showByDate', $date) }}" class="float-end">View Details</a>
        </div>
        <ul class="list-group list-group-flush">
            @foreach ($dailyExpenses as $expense)
                <li class="list-group-item">
                    {{ $expense->title }} - ₹{{ number_format($expense->amount, 2) }}
                    <div class="mt-2">
                        <a href="{{ route('expenses_edit', $expense->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('expenses_destroy', $expense->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this expense?')">Delete</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endforeach

<!-- Hidden Input for Next Cursor -->
<input type="hidden" id="next-cursor" value="{{ $expenses->nextCursor() }}">
