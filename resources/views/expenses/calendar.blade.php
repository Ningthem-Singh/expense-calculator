@extends('layouts.layout_app')

@section('content')
    <h1>Expenses Calendar</h1>

    <a href="{{ route('expenses_index') }}" class="btn btn-secondary mb-3">
        <i class="fa-solid fa-left-long"></i>
        Back to Expenses
    </a>

    <div class="mb-3 d-flex justify-content-center">
        <!-- Month and Year Controls -->
        <label for="monthSelect" class="me-2 mt-2">Go to:</label>
        <select id="monthSelect" class="form-select d-inline-block w-auto me-2">
            @for ($month = 0; $month < 12; $month++)
                <option value="{{ $month }}" {{ $month == now()->month - 1 ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $month + 1)->format('F') }}
                </option>
            @endfor
        </select>

        <select id="yearSelect" class="form-select d-inline-block w-auto me-2">
            @for ($year = now()->year - 10; $year <= now()->year + 10; $year++)
                <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endfor
        </select>
    </div>

    <!-- FullCalendar Container -->
    <div id="calendar" style="margin-top: 20px;"></div>


    {{-- Onclick preview model to show up --}}
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Expense Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="eventModalBody">
                    <!-- Event details will be inserted here -->
                </div>
            </div>
        </div>
    </div>
    {{-- end Onclick preview model to show up --}}

    <!-- Expense Proof Preview Modal -->
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
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch expenses from the server
            fetch("{{ route('expenses_calendar_data') }}")
                .then(response => response.json())
                .then(data => {
                    // console.log('====================================');
                    // console.log(data);
                    // console.log('====================================');               

                    // Initialize FullCalendar
                    const calendarEl = document.getElementById('calendar');
                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth', // Show month view by default
                        headerToolbar: {
                            left: 'prev,next today', // Navigation buttons
                            center: 'title', // Title of the current view
                            right: 'dayGridMonth,multiMonthYear,list' // Views: Month, Year, List
                        },
                        views: {
                            dayGridMonth: {
                                type: 'dayGridMonth',
                                buttonText: 'Month'
                            },
                            multiMonthYear: {
                                type: 'multiMonth',
                                duration: {
                                    years: 1
                                },
                                buttonText: 'Multi-Month'
                            },
                            list: {
                                type: 'list',
                                buttonText: 'Show List'
                            }
                        },
                        events: data, // Pass the fetched expenses as events
                        eventContent: function(arg) {

                            // Return the HTML for the event
                            return {
                                html: `<strong>${arg.event.title}</strong><br>₹${arg.event.extendedProps
                                .formattedAmount}`
                            };
                        },
                        // dateClick: function(info) {
                        //     alert(`You clicked on: ${info.dateStr}`);
                        // }
                        // eventClick: function(info) {
                        // console.log('====================================');
                        // console.log("event_id:",info.event.id);
                        // console.log('====================================');
                        // }
                        eventClick: function(info) {
                            // console.log('====================================');
                            // console.log(info);
                            // console.log('====================================');
                            // Prevent the default browser navigation
                            info.jsEvent.preventDefault();

                            // Get all event data
                            const event = info.event;
                            const props = info.event.extendedProps;

                            // Build HTML for the modal body
                            let details = `
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Title:</strong> ${event.title}</li>
                                    <li class="list-group-item"><strong>Date:</strong> ${props.formattedDate}</li>
                                    <li class="list-group-item"><strong>Amount:</strong> ₹${props.formattedAmount}</li>
                                    <li class="list-group-item"><strong>Description:</strong> ${props.description}</li>
                                    <li class="list-group-item">
                                        <strong>Proof:</strong><br>
                                        ${renderProof(props.expenseProof)}
                                    </li>
                                </ul>
                            `;

                            // Insert details into the modal body
                            document.getElementById('eventModalBody').innerHTML = details;

                            // Ensure only one modal is shown at a time
                            const eventModal = bootstrap.Modal.getInstance(document.getElementById(
                                'eventModal'));
                            if (eventModal) {
                                eventModal.hide(); // Hide the existing modal if it's open
                            }

                            // Show the modal using Bootstrap's JS API (Bootstrap 5)
                            const newModal = new bootstrap.Modal(document.getElementById(
                                'eventModal'));
                            newModal.show();
                        },
                        eventMouseEnter: function(info) {
                            // Add a custom class for hover effects
                            info.el.classList.add('fc-event-hover');
                        },
                        eventMouseLeave: function(info) {
                            // Remove the custom class when the mouse leaves
                            info.el.classList.remove('fc-event-hover');
                        },
                        datesSet: function(info) {
                            // Update the dropdowns when the calendar's view changes
                            const currentDate = info.view
                                .currentStart; // Get the current start date of the view
                            updateDropdowns(currentDate);
                        }

                    });
                    calendar.render();

                    document.getElementById('monthSelect').addEventListener('change', navigateCalendar);
                    document.getElementById('yearSelect').addEventListener('change', navigateCalendar);

                    function navigateCalendar() {
                        const selectedMonth = parseInt(document.getElementById('monthSelect').value, 10);
                        const selectedYear = parseInt(document.getElementById('yearSelect').value, 10);

                        const newDate = new Date(selectedYear, selectedMonth, 1);
                        calendar.gotoDate(newDate);
                    }

                    // Function to update dropdowns based on the calendar's current date
                    function updateDropdowns(date) {
                        const monthSelect = document.getElementById('monthSelect');
                        const yearSelect = document.getElementById('yearSelect');

                        // Set the selected month
                        monthSelect.value = date.getMonth();

                        // Set the selected year
                        yearSelect.value = date.getFullYear();
                    }

                    // Initialize dropdowns with the current date
                    const today = new Date();
                    updateDropdowns(today);
                })
                .catch(error => {
                    console.error('Error fetching calendar data:', error);
                });
        });

        // render for expense proof
        function renderProof(expenseProof) {
            if (!expenseProof || expenseProof.trim() === '') {
                return 'No proof available';
            }

            if (expenseProof.endsWith('.pdf')) {
                return `
                <button class="btn btn-sm btn-danger" onclick="openPreview('${expenseProof}')">View PDF</button>
            `;
            }

            return `
            <img src="${expenseProof}" alt="Expense Proof" style="max-width: 100px; cursor: pointer;" onclick="openPreview('${expenseProof}')">
        `;
        }
        // end render for expense proof

        // openPreview in iframe
        function openPreview(proofUrl) {
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = ''; // Clear previous content

            // Extract file extension from the URL
            const fileExtension = proofUrl.split('.').pop().toLowerCase();

            if (fileExtension === 'pdf') {
                // Embed PDF in an iframe
                previewContent.innerHTML = `
            <iframe src="${proofUrl}" width="100%" height="500px" style="border: none;"></iframe>
            `;
            } else {
                // Display image
                previewContent.innerHTML = `
            <img src="${proofUrl}" alt="Preview" style="max-width: 100%; height: auto;">
            `;
            }

            // Show the preview modal
            const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            previewModal.show();
        }
        // end openPreview in iframe
    </script>
@endsection
