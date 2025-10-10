# 🧪 Testing Guide: Admin Breastmilk Request Modal Updates

## Quick Test Scenarios

### Test 1: View Request Modal - Pending Request

**Steps:**

1. Navigate to Admin Breastmilk Request page
2. Locate a pending request in the table
3. Click the "View" (eye icon) button

**Expected Results:**

-   ✅ Modal opens showing Guardian and Infant information
-   ✅ "Admin Notes" section is visible
-   ✅ Textarea labeled "Notes / Remarks (Optional)" is present
-   ✅ "Accept Request" button is visible (green)
-   ✅ "Decline Request" button is visible (red)
-   ✅ Help text below textarea explains notes usage

---

### Test 2: Accept Request Flow

**Steps:**

1. Open View modal for a pending request
2. Type some notes in the textarea (e.g., "Approved for urgent need")
3. Click "Accept Request" button

**Expected Results:**

-   ✅ SweetAlert confirmation dialog appears
-   ✅ Dialog shows info icon and explanation message
-   ✅ "Continue to Dispense Form" button is visible
-   ✅ Clicking "Continue" closes View modal
-   ✅ Approve/Dispense modal opens
-   ✅ Notes are pre-filled in the Approve form's admin notes field

**Alternative Test:**

1. Click "Accept Request" without typing notes
2. Should still open dispense modal (notes are optional)

---

### Test 3: Decline Request Flow - With SweetAlert

**Steps:**

1. Open View modal for a pending request
2. Type some notes in the textarea (e.g., "Checking eligibility")
3. Click "Decline Request" button

**Expected Results:**

-   ✅ SweetAlert confirmation dialog appears with warning icon
-   ✅ Dialog shows "Decline Request" title
-   ✅ Textarea labeled "Reason for Declining \*" is visible
-   ✅ Pre-filled with notes from View modal (if provided)
-   ✅ Help text: "This reason will be sent to the guardian"
-   ✅ Two buttons: "Cancel" and "Yes, Decline Request"

**Continue:** 4. Clear the textarea (empty) 5. Click "Yes, Decline Request"

**Expected Results:**

-   ✅ Validation error appears: "Please provide a reason for declining"
-   ✅ Dialog stays open
-   ✅ Cannot submit without text

**Continue:** 6. Type a reason (e.g., "Insufficient inventory") 7. Click "Yes, Decline Request"

**Expected Results:**

-   ✅ Loading dialog appears with spinner
-   ✅ Dialog shows "Processing..." / "Declining request..."
-   ✅ After success: Success dialog with checkmark icon
-   ✅ Message: "Request has been declined successfully."
-   ✅ Page reloads automatically after 2 seconds
-   ✅ Request now appears in "Declined Requests" tab

---

### Test 4: Decline Without Pre-filling Notes

**Steps:**

1. Open View modal for a pending request
2. Leave textarea empty
3. Click "Decline Request" button
4. Enter reason in SweetAlert dialog
5. Submit

**Expected Results:**

-   ✅ Works the same as Test 3
-   ✅ Reason is required in SweetAlert dialog

---

### Test 5: View Non-Pending Request

**Steps:**

1. Navigate to "Dispensed Requests" or "Declined Requests" tab
2. Click "View" button on any request

**Expected Results:**

-   ✅ Modal opens with Guardian and Infant info
-   ✅ "Admin Notes" section is visible (if notes exist)
-   ✅ Notes are displayed as read-only alert box (info style)
-   ✅ NO textarea field shown
-   ✅ NO Accept/Decline buttons shown

---

### Test 6: Cancel Operations

**Steps:**

**Test 6a - Cancel Accept:**

1. Open View modal
2. Click "Accept Request"
3. Click "Cancel" in SweetAlert

**Expected:** Dialog closes, View modal stays open

**Test 6b - Cancel Decline:**

1. Open View modal
2. Click "Decline Request"
3. Enter reason
4. Click "Cancel"

**Expected:** Dialog closes, View modal stays open, request unchanged

---

### Test 7: Error Handling

**Steps:**

1. Open browser DevTools Console
2. Disable network (offline mode)
3. Open View modal
4. Click "Decline Request"
5. Enter reason and submit

**Expected Results:**

-   ✅ Error dialog appears with error icon
-   ✅ Message: "Failed to decline request. Please try again."
-   ✅ Console shows network error
-   ✅ Page does not reload

**Re-enable network and retry:**

-   ✅ Should work normally

---

### Test 8: SweetAlert Not Loaded (Fallback)

**Steps:**

1. Temporarily block SweetAlert CDN in browser
2. Open View modal
3. Click "Decline Request"

**Expected Results:**

-   ✅ Browser's native `prompt()` appears
-   ✅ Pre-filled with notes if provided
-   ✅ After entering reason, native `confirm()` appears
-   ✅ After confirm, AJAX request sent
-   ✅ Native `alert()` shows success/error
-   ✅ Page reloads

---

### Test 9: Mobile Responsiveness

**Steps:**

1. Open browser DevTools
2. Switch to mobile view (iPhone/Android)
3. Navigate to Breastmilk Request page
4. Open View modal for pending request

**Expected Results:**

-   ✅ Modal is responsive and fits screen
-   ✅ Cards stack vertically on mobile
-   ✅ Buttons are accessible and tapable
-   ✅ Textarea is usable on mobile keyboard
-   ✅ SweetAlert dialogs are mobile-friendly

---

### Test 10: Keyboard Navigation

**Steps:**

1. Open View modal
2. Use Tab key to navigate
3. Use Enter/Space to activate buttons

**Expected Results:**

-   ✅ Tab order: Textarea → Accept Button → Decline Button
-   ✅ Focus visible on all elements
-   ✅ Enter/Space activates buttons
-   ✅ Esc closes modal
-   ✅ SweetAlert supports keyboard (Tab, Enter, Esc)

---

### Test 11: Notes Character Limit

**Steps:**

1. Open View modal
2. Type or paste very long text (>1000 characters) in textarea
3. Try to submit decline

**Expected Results:**

-   ✅ Server validation prevents >1000 characters
-   ✅ Error message displays if limit exceeded
-   ✅ Consider adding client-side character counter (enhancement)

---

### Test 12: Multiple Requests

**Steps:**

1. Open View modal for Request #1
2. Enter notes
3. Close modal (X button)
4. Open View modal for Request #2
5. Check if notes from #1 appear

**Expected Results:**

-   ✅ Each request has isolated textarea
-   ✅ Notes do not carry over between requests
-   ✅ Each modal functions independently

---

### Test 13: Complete Workflow Integration

**Steps:**

1. Start with pending request
2. View details
3. Add notes: "Test notes for workflow"
4. Accept request
5. Complete dispense form:
    - Volume: 100ml
    - Milk Type: Pasteurized
    - Select inventory
6. Submit approval

**Expected Results:**

-   ✅ Request approved successfully
-   ✅ Notes saved in database
-   ✅ Request moves to Dispensed tab
-   ✅ Notes visible when viewing dispensed request

---

## 🐛 Common Issues & Solutions

### Issue 1: SweetAlert Not Showing

**Symptom:** Only native alerts appear
**Solution:** Check browser console for CDN loading errors, verify internet connection

### Issue 2: Notes Not Transferring to Approve Modal

**Symptom:** Notes field empty in dispense form
**Solution:** Verify form field exists in approve modal with correct `name="admin_notes"`

### Issue 3: Decline Fails Silently

**Symptom:** No error message after decline attempt
**Solution:** Check browser console for AJAX errors, verify CSRF token is set

### Issue 4: Page Not Reloading After Decline

**Symptom:** Success message shows but request still pending
**Solution:** Check if `location.reload()` is blocked by browser, try manual refresh

### Issue 5: Buttons Not Responsive on Mobile

**Symptom:** Buttons too small or not clickable
**Solution:** Verify `.btn-sm` class and touch target size (min 44x44px recommended)

---

## ✅ Validation Checklist

Print and check off during testing:

-   [ ] View modal opens correctly
-   [ ] Notes field visible for pending requests
-   [ ] Notes field hidden for completed requests
-   [ ] Accept button opens dispense modal
-   [ ] Accept button transfers notes
-   [ ] Decline button shows SweetAlert
-   [ ] Decline requires reason validation
-   [ ] Empty reason shows validation error
-   [ ] Decline success shows confirmation
-   [ ] Page reloads after decline
-   [ ] Cancel buttons work correctly
-   [ ] Error handling works
-   [ ] Fallback without SweetAlert works
-   [ ] Mobile responsive
-   [ ] Keyboard accessible
-   [ ] Multiple requests isolated
-   [ ] Notes save to database
-   [ ] Notifications sent to users

---

## 📊 Test Results Template

```
Test Date: ______________
Tester: _________________
Environment: ____________

| Test # | Feature              | Status | Notes |
|--------|----------------------|--------|-------|
| 1      | View Modal           | □ Pass | _____ |
| 2      | Accept Flow          | □ Pass | _____ |
| 3      | Decline Flow         | □ Pass | _____ |
| 4      | No Pre-fill          | □ Pass | _____ |
| 5      | Non-Pending View     | □ Pass | _____ |
| 6      | Cancel Operations    | □ Pass | _____ |
| 7      | Error Handling       | □ Pass | _____ |
| 8      | Fallback Mode        | □ Pass | _____ |
| 9      | Mobile Responsive    | □ Pass | _____ |
| 10     | Keyboard Navigation  | □ Pass | _____ |
| 11     | Character Limit      | □ Pass | _____ |
| 12     | Multiple Requests    | □ Pass | _____ |
| 13     | Complete Workflow    | □ Pass | _____ |

Overall Status: □ All Pass  □ Minor Issues  □ Major Issues
```

---

**Happy Testing! 🧪**
