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
    {{-- Onclick preview model to show up --}}
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
                            multiMonthYear: {
                                type: 'multiMonth',
                                duration: {
                                    years: 1
                                },
                                buttonText: 'Multi-Month'
                            }
                        },
                        events: data, // Pass the fetched expenses as events
                        eventContent: function(arg) {
                            // Format the amount using Intl.NumberFormat
                            const formattedAmount = formatAmount(arg.event.extendedProps
                                .amount);

                            // Return the HTML for the event
                            return {
                                html: `<strong>${arg.event.title}</strong><br>₹${formattedAmount}`
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
                            // Prevent the default browser navigation
                            info.jsEvent.preventDefault();

                            // Get all event data
                            const event = info.event;
                            const props = event.extendedProps;

                            // Format the amount using the global function
                            const formattedAmount = formatAmount(props.amount);
                            const formattedDate = formatDate(event.start);

                            // Build HTML for the modal body
                            let details = `
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Title:</strong> ${event.title}</li>
                                    <li class="list-group-item"><strong>Date:</strong> ${formattedDate}</li>
                                    <li class="list-group-item"><strong>Amount:</strong> ₹${formattedAmount}</li>
                                    <li class="list-group-item"><strong>Description:</strong> ${props.description}</li>
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
                })
                .catch(error => console.error('Error fetching calendar data:', error));
        });
    </script>
@endsection
