# Breastmilk Request Dispensing Implementation

## Overview

This document outlin6. **`updateSelectedVolume(requestId)`**

-   Calculates total selected vol5. Admin selects m9. Admin optionally adds notes

10. Admin clicks "Dispense"
11. SweetAlert2 confirmation dialog appears
12. On confirmation:
    -   System validates all inputs
    -   Creates dispensed_milk record
    -   Deducts the entire volume from the selected source
    -   Updates request status to 'dispensed'
    -   Sends notification to guardian
    -   Shows success message and reloads pagepasteurized/unpasteurized)
13. System fetches and displays available inventory
14. For **Pasteurized**:
    -   Admin selects one batch (radio button)
    -   Volume is auto-filled with batch's total available volume
    -   System will deduct the entire batch volume
15. For **Unpasteurized**:
    -   Admin selects one donation (radio button)
    -   Volume is auto-filled with donation's total available volume
    -   System will deduct the entire donation volume
16. Admin optionally adds notesUpdates volume tracker display

    -   Works for both radio button selections

17. **`handleDispense(requestId)`** implementation of the breastmilk request dispensing feature in the admin panel. The feature allows administrators to view, dispense, or reject pending breastmilk requests through a user-friendly modal interface.

## Features Implemented

### 1. Frontend Changes (Blade Template)

#### Updated Pending Requests Table

-   **File**: `resources/views/admin/breastmilk-request.blade.php`
-   **Changes**:
    -   Added "Action" column header to the pending requests table
    -   Added "View" button in the Action column for each pending request
    -   Button opens a dispensing modal for that specific request

#### New Dispensing Modal

For each pending request, a comprehensive modal has been created with:

**Information Displayed**:

-   Guardian information (name, contact)
-   Infant information (name, age, sex)
-   Prescription view button (if available)

**Form Fields**:

1. **Volume to Dispense** - Input field for the amount of milk to dispense (in ml)
2. **Breastmilk Type** - Dropdown to select:
    - Unpasteurized Breastmilk
    - Pasteurized Breastmilk
3. **Admin Notes** - Textarea for dispensing notes (optional)

**Dynamic Inventory Section**:

-   Appears after milk type is selected
-   For **Pasteurized**: Shows list of available batches with:
    -   Batch number
    -   Available volume
    -   Date pasteurized
    -   Radio button selection (only one batch can be selected)
    -   Uses the entire batch volume
-   For **Unpasteurized**: Shows list of available donations with:
    -   Donation ID
    -   Donor name
    -   Available volume
    -   Donation date
    -   Radio button selection (only one donation can be selected)
    -   Uses the entire donation volume

**Volume Tracker**:

-   Shows selected volume vs required volume
-   Real-time updates as user selects sources

**Action Buttons**:

-   **Close** - Closes the modal without changes
-   **Reject** - Opens rejection dialog with reason input
-   **Dispense** - Processes the dispensing with validation

### 2. JavaScript Functions

#### Core Functions Added:

1. **`handleMilkTypeChange(requestId)`**

    - Triggered when milk type is selected
    - Fetches available inventory from server
    - Displays appropriate inventory list

2. **`displayPasteurizedInventory(requestId, batches)`**

    - Renders pasteurized batch list
    - Creates radio buttons for selection
    - Auto-fills volume when batch selected

3. **`displayUnpasteurizedInventory(requestId, donations)`**

    - Renders unpasteurized donation list
    - Creates radio buttons
    - Auto-fills volume when donation selected

4. **`handleDonationSelection(requestId, donationId, availableVolume)`**

    - Handles unpasteurized donation selection
    - Auto-fills volume to dispense field

5. **`handleBatchSelection(requestId, batchId, availableVolume)`**

    - Handles pasteurized batch selection
    - Auto-fills volume to dispense field

6. **`updateSelectedVolume(requestId)`**

    - Calculates total selected volume
    - Updates volume tracker display
    - Validates volume matches

7. **`handleDispense(requestId)`**

    - Validates all inputs
    - Checks volume matching
    - Shows SweetAlert2 confirmation
    - Sends POST request to server
    - Handles success/error responses

8. **`handleReject(requestId)`**
    - Opens SweetAlert2 dialog with reason input
    - Validates reason is provided
    - Sends rejection request to server
    - Shows success/error messages

### 3. Backend Changes (Controller)

#### File: `app/Http/Controllers/BreastmilkRequestController.php`

**New Methods Added**:

1. **`dispense(Request $request, $requestId)`**

    - Validates dispensing data
    - Creates DispensedMilk record
    - Deducts inventory volumes:
        - For pasteurized: Reduces available_volume in pasteurization_batches
        - For unpasteurized: Reduces available_volume in donations, sets status to depleted if empty
    - Creates records in dispensed_milk_sources pivot table
    - Updates breastmilk_request status to 'dispensed'
    - Sends notification to the guardian
    - Returns JSON response

2. **`reject(Request $request, $requestId)`**
    - Validates rejection reason
    - Updates breastmilk_request status to 'declined'
    - Records admin notes
    - Sends notification to the guardian
    - Returns JSON response

**Updated Method**:

3. **`getAvailableInventory(Request $request)`**
    - Modified to support both 'type' and 'milk_type' query parameters
    - Returns appropriate inventory based on type:
        - **Pasteurized**: Active batches with available volume > 0
        - **Unpasteurized**: Donations with inventory_status='available' and available_volume > 0
    - Returns structured JSON with all necessary fields

### 4. Routes

#### File: `routes/web.php`

**New Routes Added**:

```php
Route::post('/admin/breastmilk-request/{id}/dispense', [BreastmilkRequestController::class, 'dispense'])
    ->name('admin.request.dispense');

Route::post('/admin/breastmilk-request/{id}/reject', [BreastmilkRequestController::class, 'reject'])
    ->name('admin.request.reject');
```

## Workflow

### Dispensing Process:

1. Admin clicks "View" button on a pending request
2. Modal opens showing request details
3. Admin enters volume to dispense
4. Admin selects milk type (pasteurized/unpasteurized)
5. System fetches and displays available inventory
6. For **Pasteurized**:
    - Admin selects one or more batches
    - Admin enters volume to use from each batch
    - System ensures total matches volume to dispense
7. For **Unpasteurized**:
    - Admin selects one donation
    - Volume is auto-filled with donation's total volume
8. Admin optionally adds notes
9. Admin clicks "Dispense"
10. SweetAlert2 confirmation dialog appears
11. On confirmation:
    - System validates all inputs
    - Creates dispensed_milk record
    - Deducts volumes from selected sources
    - Updates request status to 'dispensed'
    - Sends notification to guardian
    - Shows success message and reloads page

### Rejection Process:

1. Admin clicks "Reject" button in the modal
2. SweetAlert2 dialog appears with reason textarea
3. Admin enters rejection reason
4. On confirmation:
    - System updates request status to 'declined'
    - Records admin notes with reason
    - Sends notification to guardian
    - Shows success message and reloads page

## Database Operations

### Tables Involved:

1. **breastmilk_request**

    - Updated fields: status, admin_id, volume_requested, volume_dispensed, dispensing_notes, approved_at, dispensed_at, dispensed_milk_id, admin_notes, declined_at

2. **dispensed_milk**

    - New record created with: breastmilk_request_id, guardian_user_id, recipient_infant_id, volume_dispensed, date_dispensed, time_dispensed, admin_id, dispensing_notes

3. **dispensed_milk_sources** (pivot table)

    - New records for each source: dispensed_id, source_type, source_id, volume_used

4. **pasteurization_batches**

    - Updated field: available_volume (reduced by volume_used)

5. **breastmilk_donation**
    - Updated fields: available_volume (reduced by volume_used), inventory_status (set to 'depleted' if volume reaches 0)

## Validation Rules

### Dispense Validation:

-   `volume_dispensed`: required, numeric, minimum 0.01
-   `milk_type`: required, must be 'unpasteurized' or 'pasteurized'
-   `sources`: required, array, minimum 1 item
-   `sources.*.type`: required, must be 'unpasteurized' or 'pasteurized'
-   `sources.*.id`: required, integer
-   `sources.*.volume`: required, numeric, minimum 0.01
-   `dispensing_notes`: optional, string, max 1000 characters

### Reject Validation:

-   `admin_notes`: required, string, max 1000 characters

### Business Logic Validation:

-   Request must be in 'pending' status
-   At least one source (batch or donation) must be selected
-   Each source must have sufficient available volume
-   The selected source's full volume will be used for dispensing

## SweetAlert2 Integration

All alerts and confirmations use SweetAlert2 for a consistent and modern user experience:

-   **Warning dialogs**: Missing or invalid inputs
-   **Confirmation dialogs**: Before dispensing or rejecting
-   **Success messages**: After successful operations
-   **Error messages**: When operations fail
-   **Loading states**: During server requests
-   **Custom HTML**: For reason input in rejection dialog

## Notifications

System sends notifications to guardians via the `SystemAlert` notification class:

-   **Dispensed**: "Request Approved & Dispensed" with volume and milk type details
-   **Rejected**: "Request Declined" with the rejection reason

## Security Features

1. **Authentication Check**: All controller methods verify admin role
2. **Authorization**: Only admins can access dispensing/rejection endpoints
3. **CSRF Protection**: All POST requests include CSRF token
4. **Input Validation**: Comprehensive validation on all inputs
5. **Database Transactions**: All dispensing operations wrapped in transactions for data consistency
6. **Status Checks**: Prevents dispensing/rejecting non-pending requests

## Error Handling

-   Client-side validation before server request
-   Server-side validation with meaningful error messages
-   Database transaction rollback on errors
-   User-friendly error messages via SweetAlert2
-   Console logging for debugging

## Testing Checklist

-   [ ] Modal opens correctly when "View" is clicked
-   [ ] Milk type selection triggers inventory loading
-   [ ] Pasteurized batches display correctly with checkboxes
-   [ ] Unpasteurized donations display correctly with radio buttons
-   [ ] Volume tracker updates in real-time
-   [ ] Validation works for all required fields
-   [ ] Volume mismatch is detected and prevented
-   [ ] Insufficient inventory is detected and prevented
-   [ ] Dispense action creates proper database records
-   [ ] Inventory volumes are correctly deducted
-   [ ] Batch volumes update correctly (pasteurized)
-   [ ] Donation volumes update and status changes (unpasteurized)
-   [ ] Request status changes to 'dispensed'
-   [ ] Reject action updates status to 'declined'
-   [ ] Admin notes are saved correctly
-   [ ] Notifications are sent to guardians
-   [ ] SweetAlert messages appear in front of modal
-   [ ] Page reloads after successful operation
-   [ ] Error messages display correctly
-   [ ] Close button works without saving

## Future Enhancements

Potential improvements for future versions:

1. Print dispensing receipt
2. Export dispensing history to PDF/Excel
3. Batch dispensing for multiple requests
4. Inventory reservation system
5. Automated inventory suggestions based on FIFO
6. Mobile-responsive modal improvements
7. Real-time inventory updates via WebSockets
8. Dispensing analytics dashboard
9. Undo dispensing (within time limit)
10. Barcode scanning for batch/donation selection

## Notes

-   The implementation follows Laravel best practices
-   All foreign key relationships are properly maintained
-   The code is designed to be maintainable and extensible
-   SweetAlert2 ensures consistent UX across all interactions
-   Bootstrap modal and form styling maintain design consistency
-   JavaScript functions are modular and reusable
