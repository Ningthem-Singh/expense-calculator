## Use of custom format date and amount can be done in two ways:

1. <b>(Client-Side)</b> Using JS to call the Intl.NumberFormat and Intl.DateTimeFormat. Just add the below in the baseURL and extend to the blade file you want to call this.

```
// Define a global utility function for formatting amount
window.formatAmount = function (amount) {
    const formatter = new Intl.NumberFormat('en-IN', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    return formatter.format(amount);
};

// Define a global utility function for formatting dates
window.formatDate = function (dateString) {
    const date = new Date(dateString); // Convert the ISO date string to a Date object
    const formatter = new Intl.DateTimeFormat('en-IN', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    return formatter.format(date); // Returns "10 May, 1994"
};
```

---

---

2. <b>(Server-Side)</b> Using Helper function to call the Intl.NumberFormat and Intl.DateTimeFormat.
   Adding Helper folder by creating it in <kbd>app->Helpers->FormatHelper.php</kbd>

```
<?php
// Define a global utility function for formatting amount
if (!function_exists('formatAmount')) {
    function formatAmount($amount)
    {
        $formatter = new NumberFormatter('en-IN', NumberFormatter::DECIMAL);
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 2);
        return $formatter->format($amount);
    }
}
// Define a global utility function for formatting dates
if (!function_exists('formatDate')) {
    function formatDate($dateString)
    {
        $formatter = new IntlDateFormatter(
            'en-IN', // Locale for Indian date format
            IntlDateFormatter::LONG, // Date type (e.g., "10 May, 1994")
            IntlDateFormatter::NONE, // No time formatting
            null, // Default timezone
            null, // Default calendar
            'dd MMMM, yyyy' // Custom pattern
        );
        return $formatter->format(new DateTime($dateString));
    }
}

```

and calling the helper in <kbd>composer.json</kbd> inside the autoload

```
"autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        },
        "files": [
            "app/Helpers/FormatHelper.php"
        ]
    },
```

than typing the <kbd>composer dump-autoload</kbd>

---
