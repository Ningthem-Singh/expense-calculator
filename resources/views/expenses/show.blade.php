@extends('layouts.layout_app')

@section('content')
    <h1>Expenses for {{ formatDate($date) }}</h1>

    <a href="{{ route('expenses_index') }}" class="btn btn-secondary mb-3">Back</a>

    <ul class="list-group" style="border: 1px solid black">
        @foreach ($expenses as $expense)
            <li class="list-group-item" style="border: 2px solid grey">
                <b>{{ $expense->title }} - ₹{{ number_format($expense->amount, 2) }}</b>
                @if ($expense->description)
                    <p class="mt-2"><em>{{ $expense->description }}</em></p>
                @endif
                @if ($expense->expense_proof)
                    <div class="mt-2 d-flex align-items-center">
                        <!-- Thumbnail -->
                        <div style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#previewModal"
                            onclick="setPreview('{{ asset('storage/' . $expense->expense_proof) }}', '{{ pathinfo($expense->expense_proof, PATHINFO_EXTENSION) }}')">
                            @if (pathinfo($expense->expense_proof, PATHINFO_EXTENSION) === 'pdf')
                                <!-- PDF Thumbnail -->
                                <i class="fas fa-file-pdf fa-3x text-danger" style="cursor: pointer;"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="PDF File"></i>
                            @else
                                <!-- Image Thumbnail -->
                                <img src="{{ asset('storage/' . $expense->expense_proof) }}" alt="Expense Proof"
                                    class="img-thumbnail" style="max-width: 200px;">
                            @endif
                        </div>
                    </div>
                @endif
            </li>
        @endforeach
    </ul>

    <!-- Modal for Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Preview Content -->
                    <div id="previewContent" class="d-flex justify-content-center"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Function to set the preview content in the modal
        function setPreview(fileUrl, fileType) {
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = ''; // Clear previous content

            fetch(fileUrl)
                .then(response => {
                    if (!response.ok) throw new Error('File not found');
                    if (fileType === 'pdf') {
                        // Embed PDF in an iframe
                        previewContent.innerHTML = `
                    <iframe src="${fileUrl}" width="100%" height="500px" style="border: none;"></iframe>
                `;
                    } else {
                        // Display image
                        previewContent.innerHTML = `
                    <img src="${fileUrl}" alt="Preview" style="max-width: 100%; height: auto;">
                `;
                    }
                })
                .catch(() => {
                    previewContent.innerHTML = '<p class="text-danger">File not found or inaccessible.</p>';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
