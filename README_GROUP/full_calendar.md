## Full Calendar (https://fullcalendar.io/)

First we see that the calendar blade is called

```
public function expenses_calendar()
{
return view('expenses.calendar');
}
```

---

To add FullCalendar we need to add a div container

```
<!-- FullCalendar Container -->
    <div id="calendar" style="margin-top: 20px;"></div>
```

---

than we need to fetch the api so for that the controller is made

```
    public function expenses_calendar_data()
    {
        // Fetch all expenses and format them for FullCalendar
        $expenses = Expense::all();

        $events = $expenses->map(function ($expense) {
            return [
                'id' => $expense->id,
                'title' => $expense->title,
                'start' => $expense->date, // FullCalendar expects dates in 'YYYY-MM-DD' format
                'formattedDate' => formatDate($expense->date), // Custom property to display the date
                'formattedAmount' => formatAmount($expense->amount), // Custom property to display the amount
                'description' => $expense->description,
            ];
        });

        return response()->json($events);
    }
```

---

Note: the date has to be in YYYY-MM-DD as thats what FullCalendar expects and key must be <kbd>start</kbd>.

than in the script section we fetch the expenses_calendar_data

```
document.addEventListener('DOMContentLoaded', function() {
// Fetch expenses from the server
fetch("{{ route('expenses_calendar_data') }}")
    .then(response => response.json())
    .then(data => {
    //-------initialize FullCalendar-----------
    })
    .catch(error => {
        console.error('Error fetching calendar data:', error);
    });
});
```

---

than we initialize the FullCalendar

```
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
        list:{
            type:'list',
            buttonText:'Show List'
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
    eventClick: function(info) {
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
});
calendar.render();
```

we than added select box to change the month and year as we wished so that we can goto that month/ year. <br>
For that we added this ontop of the fullcalendar div container

```
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
```

---

and called in script

```
document.getElementById('monthSelect').addEventListener('change', navigateCalendar);
document.getElementById('yearSelect').addEventListener('change', navigateCalendar);

function navigateCalendar() {
    const selectedMonth = parseInt(document.getElementById('monthSelect').value, 10);
    const selectedYear = parseInt(document.getElementById('yearSelect').value, 10);

    const newDate = new Date(selectedYear, selectedMonth, 1);
    calendar.gotoDate(newDate);
}
```

---

but than later on when clicking on <kbd>Today</kbd> of FullCalendar the month and year dont change so we added

```
datesSet: function(info) {
    // Update the dropdowns when the calendar's view changes
    const currentDate = info.view
        .currentStart; // Get the current start date of the view
    updateDropdowns(currentDate);
}
```

inside the calendar and than rendered it and outside the <kbd>calendar.render();</kbd> we called the updateDropdowns

```
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
.catch(error => console.error('Error fetching calendar data:', error));
    });
```
