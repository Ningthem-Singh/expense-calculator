@extends('layouts.layout_app')

@section('content')
    <h1>Add New Expense</h1>

    <form action="{{ route('expenses_store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="expense_proof" class="form-label">Upload Proof (Image or PDF, Max 10MB)</label>
            <input type="file" name="expense_proof" id="expense_proof" class="form-control"
                accept="image/jpeg, image/png, image/webp, image/gif, application/pdf">
            <div id="file-preview" class="mt-2"></div> <!-- Preview container -->
        </div>
        <button type="submit" class="btn btn-primary">Add Expense</button>
    </form>
@endsection

@section('scripts')
    <script>
        document.getElementById('expense_proof').addEventListener('change', function() {
            const file = this.files[0];
            const previewContainer = document.getElementById('file-preview');
            previewContainer.innerHTML = ''; // Clear previous preview

            if (file) {
                if (file.type.startsWith('image/')) {
                    // Display image preview
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = '200px';
                    img.classList.add('img-thumbnail');
                    previewContainer.appendChild(img);
                } else if (file.type === 'application/pdf') {
                    // Display PDF placeholder
                    previewContainer.innerHTML = '<p><strong>Selected PDF:</strong> ' + file.name + '</p>';
                }
            }
        });
    </script>
@endsection
