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