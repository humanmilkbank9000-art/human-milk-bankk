# Year Dropdown Implementation for Dashboard Chart

## Summary

Added a year dropdown selector to the admin dashboard's monthly overview chart, allowing administrators to view donation and request data for different years (2020 to current year).

## Changes Made

### 1. Frontend - Dashboard View (`resources/views/admin/dashboard.blade.php`)

#### Chart Header Update

-   Added a year dropdown selector next to the "Monthly Overview" title
-   Dropdown displays years from 2020 to the current year in descending order
-   The selected year is highlighted based on the current view
-   Integrated with the existing chart design

#### CSS Styling

-   Added `.year-selector` class with gradient styling matching the chart theme
-   Hover and focus effects for better user experience
-   Responsive design for mobile devices
-   Color scheme: Purple gradient (#667eea to #764ba2) matching the donations line

#### JavaScript Function

-   Added `changeYear(year)` function to handle year selection
-   Redirects to dashboard with year parameter when dropdown changes
-   Preserves all other dashboard functionality

### 2. Backend - Controller (`app/Http/Controllers/LoginController.php`)

#### Method Update

-   Modified `admin_dashboard()` method to accept `Request $request` parameter
-   Added year parameter handling: `$request->input('year', now()->year)`
-   Filters monthly donations and requests by the selected year
-   Maintains backward compatibility (defaults to current year if no parameter provided)

## Usage

### For Users

1. Navigate to Admin Dashboard
2. Locate the "Monthly Overview" chart
3. Click the year dropdown next to the title
4. Select desired year (2020 - current year)
5. Chart automatically updates with data for selected year

### Technical Details

**Route**: The dashboard route accepts GET parameters:

```
/admin/dashboard?year=2023
```

**Data Filtering**:

-   Donations: Filtered by `created_at` year and month
-   Requests: Filtered by `created_at` year and month with status='approved'

**Year Range**: 2020 to current year (configurable in blade template)

## Responsive Design

-   Desktop: Dropdown appears inline with title
-   Mobile: Dropdown stacks below title for better readability
-   Maintains touch-friendly sizing on all devices

## Browser Compatibility

-   Works with all modern browsers
-   Uses standard HTML5 select element
-   No external dependencies required

## Future Enhancements

Potential improvements could include:

-   AJAX-based chart updates (no page reload)
-   Date range selector (custom start/end dates)
-   Export chart data by year
-   Comparison view (multiple years side-by-side)
