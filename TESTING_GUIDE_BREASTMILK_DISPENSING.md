# Testing Guide: Breastmilk Request Dispensing Feature

## Prerequisites

Before testing, ensure:

1. You have admin access to the system
2. There are pending breastmilk requests in the database
3. There is available inventory (both pasteurized batches and unpasteurized donations)
4. SweetAlert2 is loaded (should be included via CDN in the blade template)

## Test Scenarios

### Scenario 1: Open Dispensing Modal

**Steps:**

1. Log in as admin
2. Navigate to Breastmilk Request Management page
3. Click on "Pending Requests" tab
4. Locate a pending request
5. Click the "View" button in the Action column

**Expected Result:**

-   Modal opens with title "Dispense Breastmilk Request #[ID]"
-   Guardian and Infant information displays correctly
-   Form fields are visible: Volume to Dispense, Breastmilk Type, Admin Notes
-   Inventory section is hidden initially
-   Modal has Close, Reject, and Dispense buttons

---

### Scenario 2: Load Pasteurized Inventory

**Steps:**

1. Open dispensing modal (see Scenario 1)
2. Select "Pasteurized Breastmilk" from the Breastmilk Type dropdown

**Expected Result:**

-   Inventory Container becomes visible
-   Loading indicator appears briefly
-   List of available pasteurized batches displays with:
    -   Batch numbers
    -   Available volumes
    -   Dates pasteurized
    -   Radio buttons for selection (only one can be selected)
-   Volume tracker appears showing "Selected: 0.00 ml / Required: 0.00 ml"
-   Info message: "For pasteurized milk, the entire batch volume will be used."

---

### Scenario 3: Select Pasteurized Batches and Enter Volumes

**Steps:**

1. Complete Scenario 2
2. Enter "250" in the "Volume to Dispense" field
3. Check the first batch
4. Enter "150" in the volume input that appears
5. Check the second batch
6. Enter "100" in its volume input

**Expected Result:**

-   Volume inputs appear only when checkboxes are checked
-   Volume tracker updates in real-time:
    -   "Selected: 250.00 ml / Required: 250.00 ml"
-   Volumes match the required amount

---

### Scenario 4: Dispense Pasteurized Breastmilk

**Steps:**

1. Complete Scenario 3 with matching volumes
2. Optionally add admin notes
3. Click "Dispense" button

**Expected Result:**

-   SweetAlert2 confirmation dialog appears IN FRONT of the modal
-   Dialog shows: "Are you sure you want to dispense 250.00 ml of pasteurized breastmilk?"
-   Click "Yes, Dispense"
-   Loading indicator shows during processing
-   Success message appears: "Successfully Dispensed!"
-   Page reloads automatically
-   Request moves from "Pending" to "Dispensed" tab
-   Batch volumes are reduced in database

**Database Verification:**

```sql
-- Check breastmilk_request table
SELECT status, volume_dispensed, dispensed_at FROM breastmilk_request WHERE breastmilk_request_id = [ID];
-- Should show: status='dispensed', volume_dispensed=250.00, dispensed_at=[timestamp]

-- Check dispensed_milk table
SELECT * FROM dispensed_milk WHERE breastmilk_request_id = [ID];
-- Should have a new record

-- Check dispensed_milk_sources table
SELECT * FROM dispensed_milk_sources WHERE dispensed_id = [dispensed_id];
-- Should have 2 records (one for each batch)

-- Check pasteurization_batches table
SELECT batch_id, available_volume FROM pasteurization_batches WHERE batch_id IN ([batch_ids]);
-- available_volume should be reduced by the amounts used
```

---

### Scenario 5: Load Unpasteurized Inventory

**Steps:**

1. Open dispensing modal
2. Select "Unpasteurized Breastmilk" from dropdown

**Expected Result:**

-   Inventory Container becomes visible
-   Loading indicator appears briefly
-   List of available unpasteurized donations displays with:
    -   Donation IDs
    -   Donor names
    -   Available volumes
    -   Donation dates
    -   Radio buttons (only one can be selected)
-   Info message: "For unpasteurized milk, the entire donation volume will be used."

---

### Scenario 6: Select Unpasteurized Donation

**Steps:**

1. Complete Scenario 5
2. Click radio button for a donation with available volume of 180ml

**Expected Result:**

-   Radio button selects
-   "Volume to Dispense" field auto-fills with 180.00
-   Volume tracker updates: "Selected: 180.00 ml / Required: 180.00 ml"

---

### Scenario 7: Dispense Unpasteurized Breastmilk

**Steps:**

1. Complete Scenario 6
2. Optionally add admin notes
3. Click "Dispense" button

**Expected Result:**

-   SweetAlert2 confirmation appears
-   Shows: "Are you sure you want to dispense 180.00 ml of unpasteurized breastmilk?"
-   Click "Yes, Dispense"
-   Success message appears
-   Page reloads
-   Request moves to "Dispensed" tab
-   Donation volume is fully depleted

**Database Verification:**

```sql
-- Check donation table
SELECT available_volume, inventory_status FROM breastmilk_donation WHERE breastmilk_donation_id = [ID];
-- Should show: available_volume=0, inventory_status='depleted'

-- Check dispensed_milk_sources table
SELECT * FROM dispensed_milk_sources WHERE source_type = 'unpasteurized' AND source_id = [donation_id];
-- Should have 1 record with volume_used=180.00
```

---

### Scenario 8: Validation - Missing Volume

**Steps:**

1. Open dispensing modal
2. Select a milk type
3. Don't enter volume to dispense
4. Click "Dispense" button

**Expected Result:**

-   SweetAlert2 warning appears IN FRONT of modal
-   Title: "Invalid Volume"
-   Text: "Please enter a valid volume to dispense."
-   No server request is made

---

### Scenario 9: Validation - Missing Milk Type

**Steps:**

1. Open dispensing modal
2. Enter volume to dispense
3. Don't select milk type
4. Click "Dispense" button

**Expected Result:**

-   SweetAlert2 warning appears
-   Title: "Missing Milk Type"
-   Text: "Please select a milk type."

---

### Scenario 10: Validation - No Sources Selected

**Steps:**

1. Open dispensing modal
2. Enter volume: 100
3. Select "Pasteurized Breastmilk"
4. Don't select any batches
5. Click "Dispense" button

**Expected Result:**

-   SweetAlert2 warning appears
-   Title: "No Batches Selected"
-   Text: "Please select at least one pasteurized batch and specify volume."

---

### Scenario 11: Validation - Volume Mismatch

**Steps:**

1. Open dispensing modal
2. Enter volume to dispense: 250
3. Select "Pasteurized Breastmilk"
4. Select one batch and enter volume: 150
5. Click "Dispense" button

**Expected Result:**

-   SweetAlert2 warning appears
-   Title: "Volume Mismatch"
-   Text: "Selected volume (150.00 ml) must equal the volume to dispense (250.00 ml)."

---

### Scenario 12: Reject Request

**Steps:**

1. Open dispensing modal
2. Click "Reject" button

**Expected Result:**

-   SweetAlert2 dialog appears IN FRONT of modal
-   Title: "Reject Request"
-   Contains textarea for reason
-   Has "Reject Request" (red) and "Cancel" buttons

---

### Scenario 13: Reject with Reason

**Steps:**

1. Complete Scenario 12
2. Enter rejection reason: "Insufficient documentation"
3. Click "Reject Request" button

**Expected Result:**

-   Loading indicator shows
-   Success message appears: "Request Rejected"
-   Page reloads
-   Request moves to "Declined" tab
-   Admin notes contain the rejection reason

**Database Verification:**

```sql
SELECT status, admin_notes, declined_at FROM breastmilk_request WHERE breastmilk_request_id = [ID];
-- Should show: status='declined', admin_notes='Insufficient documentation', declined_at=[timestamp]
```

---

### Scenario 14: Reject without Reason

**Steps:**

1. Open dispensing modal
2. Click "Reject" button
3. Leave reason textarea empty
4. Click "Reject Request" button

**Expected Result:**

-   Validation message appears: "Please provide a reason for rejection"
-   Form doesn't submit
-   User must enter a reason to proceed

---

### Scenario 15: View Prescription

**Steps:**

1. Open dispensing modal for a request with prescription
2. Click "View Prescription" button

**Expected Result:**

-   Prescription modal opens
-   Shows the uploaded prescription image/PDF
-   Can close prescription modal and return to dispensing modal

---

### Scenario 16: Close Modal without Action

**Steps:**

1. Open dispensing modal
2. Fill in some fields
3. Click "Close" button or click outside modal

**Expected Result:**

-   Modal closes
-   No data is saved
-   Request remains in pending status
-   Can reopen modal and fields are reset

---

### Scenario 17: Multiple Batches Selection

**Steps:**

1. Open dispensing modal
2. Enter volume to dispense: 500
3. Select "Pasteurized Breastmilk"
4. Select 3 different batches:
    - Batch 1: 200 ml
    - Batch 2: 150 ml
    - Batch 3: 150 ml
5. Click "Dispense"

**Expected Result:**

-   All 3 batches are validated
-   Dispensing succeeds
-   3 records created in dispensed_milk_sources
-   All 3 batches have reduced available_volume

---

### Scenario 18: SweetAlert Position

**Steps:**

1. Open dispensing modal
2. Trigger any validation warning or confirmation

**Expected Result:**

-   SweetAlert2 dialog appears IN FRONT of the dispensing modal
-   Modal backdrop is visible behind SweetAlert
-   SweetAlert is centered and clearly visible
-   z-index is properly set

---

### Scenario 19: Real-time Volume Updates

**Steps:**

1. Open dispensing modal
2. Enter volume to dispense: 200
3. Select "Pasteurized Breastmilk"
4. Select a batch and enter: 50
5. Observe volume tracker
6. Change volume to: 100
7. Observe volume tracker again

**Expected Result:**

-   Volume tracker updates immediately on each change
-   Shows: "Selected: 50.00 ml / Required: 200.00 ml"
-   After change: "Selected: 100.00 ml / Required: 200.00 ml"
-   Updates are smooth and instant

---

### Scenario 20: Notifications

**Steps:**

1. Note the user_id of the guardian making the request
2. Dispense or reject a request
3. Log in as that guardian user
4. Check notifications

**Expected Result:**

-   Guardian receives notification
-   For dispensed: "Request Approved & Dispensed" with details
-   For rejected: "Request Declined" with reason
-   Notification is marked as unread

---

## Performance Testing

### Load Testing

1. Create 50+ pending requests
2. Open dispensing modal for each
3. Check page load time and responsiveness

**Expected:** Should load within 2-3 seconds

### Inventory Loading

1. Create 100+ batches/donations
2. Select milk type in modal
3. Measure time to load inventory

**Expected:** Should load within 1-2 seconds

---

## Browser Compatibility

Test the feature in:

-   [ ] Chrome (latest)
-   [ ] Firefox (latest)
-   [ ] Edge (latest)
-   [ ] Safari (latest)
-   [ ] Mobile Chrome
-   [ ] Mobile Safari

---

## Common Issues and Solutions

### Issue: Modal doesn't open

**Solution:** Check browser console for JavaScript errors

### Issue: Inventory doesn't load

**Solution:**

-   Check network tab for failed requests
-   Verify route exists: `/admin/breastmilk-request/inventory`
-   Check admin authentication

### Issue: SweetAlert appears behind modal

**Solution:**

-   Verify SweetAlert2 is loaded
-   Check z-index values in CSS
-   Ensure SweetAlert CDN is accessible

### Issue: Volume deduction not working

**Solution:**

-   Check database transactions
-   Verify foreign keys exist
-   Check model methods: `reduceVolume()`

### Issue: Notifications not sent

**Solution:**

-   Verify notification class exists
-   Check queue workers are running
-   Verify user has valid notification preferences

---

## Cleanup After Testing

If testing with real data:

1. Delete test dispensed_milk records
2. Restore batch/donation volumes
3. Reset request statuses
4. Remove test notifications

SQL cleanup script:

```sql
-- Reset a test request
UPDATE breastmilk_request
SET status='pending', volume_dispensed=NULL, dispensed_at=NULL, dispensed_milk_id=NULL
WHERE breastmilk_request_id = [test_id];

-- Delete associated dispensed milk records
DELETE FROM dispensed_milk_sources WHERE dispensed_id = [test_dispensed_id];
DELETE FROM dispensed_milk WHERE dispensed_id = [test_dispensed_id];

-- Restore inventory volumes (adjust as needed)
UPDATE pasteurization_batches SET available_volume = total_volume WHERE batch_id = [test_batch_id];
UPDATE breastmilk_donation SET available_volume = total_volume, inventory_status='available' WHERE breastmilk_donation_id = [test_donation_id];
```

---

## Sign-off

-   [ ] All 20 scenarios tested successfully
-   [ ] All validations working correctly
-   [ ] Database records created properly
-   [ ] Inventory deductions accurate
-   [ ] Notifications sent successfully
-   [ ] SweetAlert positioning correct
-   [ ] No console errors
-   [ ] Mobile responsive
-   [ ] Cross-browser compatible
-   [ ] Performance acceptable

**Tested by:** ******\_\_\_******  
**Date:** ******\_\_\_******  
**Sign:** ******\_\_\_******
