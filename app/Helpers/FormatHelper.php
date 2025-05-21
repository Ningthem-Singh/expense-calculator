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
