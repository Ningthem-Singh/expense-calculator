@extends('layouts.layout_app')

@section('content')
    <h1>Expenses Calendar</h1>

    <a href="{{ route('expenses_index') }}" class="btn btn-secondary mb-3">Back to Expenses</a>

    <!-- FullCalendar Container -->
    <div id="calendar" style="margin-top: 20px;"></div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch expenses from the server
            fetch("{{ route('expenses_calendar_data') }}")
                .then(response => response.json())
                .then(data => {
                    // Initialize FullCalendar
                    const calendarEl = document.getElementById('calendar');
                    const calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth', // Show month view by default
                        events: data, // Pass the fetched expenses as events
                        eventContent: function(arg) {
                            // Customize how events are displayed
                            return {
                                html: `<strong>${arg.event.title}</strong><br>₹${arg.event.extendedProps.amount}`
                            };
                        },
                        dateClick: function(info) {
                            // Handle date clicks if needed (e.g., add new expense)
                            alert(`You clicked on: ${info.dateStr}`);
                            // window.location.href =
                            //     `{{ route('expenses_create') }}?date=${info.dateStr}`;
                        }
                    });
                    calendar.render();
                })
                .catch(error => console.error('Error fetching calendar data:', error));
        });
    </script>
@endsection
