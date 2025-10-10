# Clickable Notifications - Implementation Guide

## ✅ Feature: Clickable Notifications with Smart Routing

Notifications are now **clickable** and automatically redirect users to the relevant section based on the notification type and user role.

---

## 🎯 How It Works

### User Clicks Notification →

1. **Notification is automatically marked as read**
2. **User is redirected to the appropriate page**
3. **Badge count updates**
4. **Dropdown closes**

---

## 📍 Redirect Mapping

### For Admin Notifications:

| Notification Title Contains    | Redirects To                | Route                    |
| ------------------------------ | --------------------------- | ------------------------ |
| "Health Screening"             | Health Screening Management | `admin.health-screening` |
| "Donation" + "Walk-in"         | Donation Management         | `admin.donation`         |
| "Donation" + "Home Collection" | Donation Management         | `admin.donation`         |
| "Breastmilk Request"           | Request Management          | `admin.request`          |

### For User Notifications:

| Notification Title Contains | Redirects To          | Route                   |
| --------------------------- | --------------------- | ----------------------- |
| "Health Screening"          | Health Screening Form | `user.health-screening` |
| "Donation"                  | Pending Donations     | `user.pending`          |
| "Pickup"                    | Pending Donations     | `user.pending`          |
| "Request"                   | My Requests           | `user.my-requests`      |

---

## 🔄 Notification Flow Examples

### Example 1: Admin - New Health Screening

```
1. User submits health screening
2. Admin receives notification: "New Health Screening"
3. Admin clicks notification
4. Notification marked as read
5. Redirected to: /admin/health-screening
6. Admin can review the submission
```

### Example 2: User - Health Screening Accepted

```
1. Admin accepts health screening
2. User receives notification: "Health Screening Accepted"
3. User clicks notification
4. Notification marked as read
5. Redirected to: /user/health-screening
6. User sees acceptance status
```

### Example 3: Admin - New Walk-in Donation

```
1. User books walk-in appointment
2. Admin receives notification: "New Donation (Walk-in)"
3. Admin clicks notification
4. Notification marked as read
5. Redirected to: /admin/donation
6. Admin can validate the donation
```

### Example 4: User - Donation Validated

```
1. Admin validates walk-in donation
2. User receives notification: "Donation Validated"
3. User clicks notification
4. Notification marked as read
5. Redirected to: /user/pending-donation
6. User sees donation status
```

### Example 5: Admin - New Breastmilk Request

```
1. User submits breastmilk request
2. Admin receives notification: "New Breastmilk Request"
3. Admin clicks notification
4. Notification marked as read
5. Redirected to: /admin/request
6. Admin can approve/decline request
```

### Example 6: User - Request Approved

```
1. Admin approves breastmilk request
2. User receives notification: "Request Approved"
3. User clicks notification
4. Notification marked as read
5. Redirected to: /user/my-requests
6. User sees approved request details
```

---

## 💻 Technical Implementation

### JavaScript Function - Smart Routing

```javascript
function getNotificationRedirectUrl(title, role) {
    const titleLower = title.toLowerCase();

    if (role === "admin") {
        // Admin notifications
        if (titleLower.includes("health screening")) {
            return "/admin/health-screening";
        } else if (titleLower.includes("donation")) {
            return "/admin/donation";
        } else if (titleLower.includes("breastmilk request")) {
            return "/admin/request";
        }
    } else {
        // User notifications
        if (titleLower.includes("health screening")) {
            return "/user/health-screening";
        } else if (
            titleLower.includes("donation") ||
            titleLower.includes("pickup")
        ) {
            return "/user/pending-donation";
        } else if (titleLower.includes("request")) {
            return "/user/my-requests";
        }
    }

    return null;
}
```

### Click Handler

```javascript
async function handleNotificationClick(notificationId, redirectUrl) {
    // Mark as read
    await fetch(`/notifications/${notificationId}/read`, {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrfToken },
    });

    // Redirect
    window.location.href = redirectUrl;
}
```

---

## 🎨 UI/UX Features

### Visual Feedback:

1. **Cursor Changes**

    - Pointer cursor on hover (indicates clickable)
    - Normal cursor for non-clickable items

2. **Hover Effect**

    - Light gray background on hover
    - Smooth transition (0.2s)

3. **Active State**

    - Slightly darker background when clicked
    - Provides tactile feedback

4. **Mark Button**
    - Still available for manual marking
    - Prevents event propagation (won't trigger redirect)

### CSS Styling:

```css
.notification-item {
    transition: background-color 0.2s ease;
}

.notification-item[style*="cursor: pointer"]:hover {
    background-color: #f8f9fa !important;
}

.notification-item[style*="cursor: pointer"]:active {
    background-color: #e9ecef !important;
}
```

---

## 📊 Complete Notification Redirect Matrix

### Admin Notifications:

| Notification                   | User Action            | Admin Sees   | Click Goes To         |
| ------------------------------ | ---------------------- | ------------ | --------------------- |
| New Health Screening           | User submits form      | Notification | Health Screening page |
| New Donation (Walk-in)         | User books appointment | Notification | Donation Management   |
| New Donation (Home Collection) | User requests pickup   | Notification | Donation Management   |
| New Breastmilk Request         | User submits request   | Notification | Request Management    |

### User Notifications:

| Notification              | Admin Action            | User Sees    | Click Goes To         |
| ------------------------- | ----------------------- | ------------ | --------------------- |
| Health Screening Accepted | Admin approves          | Notification | Health Screening page |
| Health Screening Declined | Admin declines          | Notification | Health Screening page |
| Donation Validated        | Admin validates walk-in | Notification | Pending Donations     |
| Pickup Scheduled          | Admin schedules pickup  | Notification | Pending Donations     |
| Pickup Validated          | Admin validates pickup  | Notification | Pending Donations     |
| Request Approved          | Admin approves request  | Notification | My Requests           |
| Request Declined          | Admin declines request  | Notification | My Requests           |

---

## 🔍 Smart Detection Logic

The system intelligently detects notification type by analyzing the **title** text:

### Keywords Used:

-   **"health screening"** → Routes to health screening pages
-   **"donation"** → Routes to donation management
-   **"walk-in"** → Routes to donation management
-   **"home collection"** → Routes to donation management
-   **"pickup"** → Routes to pending donations (user)
-   **"breastmilk request"** → Routes to request pages
-   **"request"** → Routes to my requests (user)

### Case Insensitive:

-   All comparisons are case-insensitive
-   "Health Screening" = "health screening" = "HEALTH SCREENING"

---

## ✅ Benefits

### 1. **Improved User Experience**

-   One-click navigation to relevant section
-   No need to search for the item
-   Faster workflow

### 2. **Automatic Read Marking**

-   Clicking marks as read automatically
-   No manual action needed
-   Cleaner notification list

### 3. **Smart Routing**

-   Role-aware redirects
-   Context-aware navigation
-   Reduces clicks and time

### 4. **Visual Feedback**

-   Clear hover states
-   Pointer cursor indicates clickability
-   Professional UI/UX

### 5. **Maintains Manual Control**

-   "Mark" button still available
-   Can mark without redirecting
-   Flexible user choice

---

## 🧪 Testing Scenarios

### Test 1: Admin - Health Screening Notification

1. ✅ Log in as user
2. ✅ Submit health screening
3. ✅ Log in as admin
4. ✅ Click notification bell
5. ✅ See "New Health Screening" notification
6. ✅ Hover over notification (cursor changes, background gray)
7. ✅ Click notification
8. ✅ Redirected to `/admin/health-screening`
9. ✅ Notification marked as read
10. ✅ Badge count decreased

### Test 2: User - Request Approved Notification

1. ✅ Log in as user
2. ✅ Submit breastmilk request
3. ✅ Log in as admin, approve request
4. ✅ Log in as user
5. ✅ Click notification bell
6. ✅ See "Request Approved" notification
7. ✅ Click notification
8. ✅ Redirected to `/user/my-requests`
9. ✅ See approved request details
10. ✅ Notification marked as read

### Test 3: Mark Button (No Redirect)

1. ✅ Click notification bell
2. ✅ Click "Mark" button on notification
3. ✅ Notification marked as read
4. ✅ Stay on current page (no redirect)
5. ✅ Badge count decreased

---

## 📝 Code Files Modified

### 1. `resources/views/partials/notification-bell.blade.php`

-   Added `getNotificationRedirectUrl()` function
-   Added `handleNotificationClick()` function
-   Modified notification HTML to include click handlers
-   Added CSS for hover effects
-   Added cursor pointer for clickable items
-   Added event.stopPropagation() for mark button

---

## 🎯 Summary

| Feature                    | Status             |
| -------------------------- | ------------------ |
| Clickable notifications    | ✅ Implemented     |
| Smart role-based routing   | ✅ Implemented     |
| Auto mark as read on click | ✅ Implemented     |
| Hover effects              | ✅ Implemented     |
| Cursor changes             | ✅ Implemented     |
| Manual mark button         | ✅ Still available |
| Admin redirects            | ✅ Working         |
| User redirects             | ✅ Working         |
| Case-insensitive detection | ✅ Working         |
| Event propagation control  | ✅ Working         |

---

## 🚀 Ready to Use!

The clickable notification feature is **fully implemented** and ready for production use. Users and admins can now:

-   ✅ Click any notification to navigate to the relevant section
-   ✅ Have notifications automatically marked as read
-   ✅ Experience smooth, intuitive navigation
-   ✅ Benefit from role-aware intelligent routing

**No additional configuration needed!** 🎉
