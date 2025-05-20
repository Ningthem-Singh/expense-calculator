@extends('layouts.layout_app')

@section('content')
    <h1>Expenses Calendar</h1>

    <a href="{{ route('expenses_index') }}" class="btn btn-secondary mb-3">
        <i class="fa-solid fa-left-long"></i>
        Back to Expenses
    </a>

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
                                type: 'multiMonth', // Use the dayGrid plugin for Year View
                                duration: {
                                    years: 1
                                }, // Show one year at a time
                                buttonText: 'Multi-Month' // Button text for Year View
                            }
                        },
                        events: data, // Pass the fetched expenses as events
                        eventContent: function(arg) {
                            // console.log('====================================');
                            // console.log(arg);
                            // console.log('====================================');
                            // Customize how events are displayed
                            return {
                                html: `<strong>${arg.event.title}</strong><br>₹${arg.event.extendedProps.amount}`
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

                            // Build HTML for the modal body
                            let details = `
                                <ul class="list-group" style="text-decoration: none;">
                                    <li class="list-group-item"><strong>Title:</strong> ${event.title}</li>
                                    <li class="list-group-item"><strong>Date:</strong> ${event.start.toLocaleDateString()}</li>
                                    <li class="list-group-item"><strong>Amount:</strong> ₹${props.amount}</li>
                                    <li class="list-group-item"><strong>Description:</strong> ${props.description}</li>
                                </ul>
                            `;

                            // Insert details into the modal body
                            document.getElementById('eventModalBody').innerHTML = details;

                            // Show the modal using Bootstrap's JS API (Bootstrap 5)
                            var eventModal = new bootstrap.Modal(document.getElementById(
                                'eventModal'));
                            eventModal.show();
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
                })
                .catch(error => console.error('Error fetching calendar data:', error));
        });
    </script>
@endsection
