# System Validation Analysis - UOC Sports E-Portal

This document outlines the validation mechanisms implemented across the UOC Sports E-Portal system, covering both client-side and server-side strategies.

## 1. Overview
The system employs a multi-layered validation approach to ensure data integrity and provide a seamless user experience. Validation is handled differently depending on the complexity and context of the form (e.g., core authentication vs. administrative management).

---

## 2. Client-Side Validation

### A. Native HTML5 Validation
Many forms utilize standard HTML5 attributes to provide immediate feedback before data is even sent to the server.
- **Attributes used:** `required`, `pattern`, `maxheight`, `type="email"`, `type="tel"`.
- **Example:** In `reserve-facility.php`, fields are marked as `required` to prevent empty submissions.
- **Custom Patterns:** Telephone fields often use regex patterns: `pattern="^\+94[0-9]{9}$"`.

### B. Manual JavaScript Validation
For forms handled via AJAX/Fetch, JavaScript is used to perform more complex checks or to aggregate data.
- **Mechanism:** Intercepting the `onsubmit` event or using a custom button click listener.
- **Logic:** Manual checks such as `if (!formData.fname || !formData.email)` are common.
- **User Feedback:** Custom UI components (toast notifications, dynamic message boxes) are updated in real-time based on the JS logic.
- **Example:** The "Add User" form in `admin/users.php` manually gathers data and checks for required fields before sending a JSON payload.

### C. Pattern: `novalidate`
In several admin forms, the `novalidate` attribute is added to the `<form>` tag. This disables default browser validation bubbles, allowing the custom JavaScript logic to fully control the error presentation and UX.

---

## 3. Server-Side Validation

### A. Controller-Level Checks
The primary gatekeeper for data integrity is the Controller layer.
- **Methodology:** Manual verification of `$_POST` or JSON input arrays.
- **Functions used:** `empty()`, `trim()`, `isset()`, `in_array()`.
- **Flow:**
  1. Receive input.
  2. Check for missing required fields.
  3. Validate formats (e.g., password matching, email existence).
  4. Perform business logic checks (e.g., budget limits, schedule conflicts).
  5. Store errors in `$_SESSION['message']` and `$_SESSION['color']`.
  6. Redirect back to the original form.

### B. File Upload Validation
Specialized validation is implemented for files (e.g., Student ID cards, Sport Expense receipts).
- **Status Checks:** Verifying `UPLOAD_ERR_OK`.
- **Type Whitelisting:** Checking MIME types (e.g., `['image/jpeg', 'image/png']`).
- **Size Limits:** Enforcing maximum file sizes (typically 5MB).
- **Path Sanitization:** Generating unique filenames (e.g., using `time()` and `student_id`) to prevent collisions.

### C. Database-Level Validation
While less frequent in PHP logic, the database schema provides the final layer of protection.
- **Unique Constraints:** `email` and `user_id` are unique at the SQL level.
- **Foreign Keys:** Ensures referential integrity (e.g., `faculty_id` must exist in the `faculty` table).

---

## 4. Error Feedback Mechanisms

### A. Session-Based (Standard Forms)
For traditional post-back forms (like Sign In/Sign Up):
1. Controller detects an error.
2. Error message and status color are saved to `$_SESSION`.
3. User is redirected back to the form page.
4. The View checks for these session variables and renders a notification bar.

### B. JSON Responses (AJAX Forms)
For modern interfaces (like Faculty Reservation or Admin User Management):
1. Controller performs validation and returns a JSON object: `{"status": "error", "message": "..."}`.
2. JavaScript parses the response and displays success or error messages using:
    - `showNotification('...', 'error')` (Custom Toast)
    - `reservationMessage.innerHTML = '...'` (In-place message box)

---

## 5. Summary Table of Patterns

| Feature | Validation Type | Primary Mechanism | Feedback Method |
| :--- | :--- | :--- | :--- |
| **Authentication** | Server-side | PHP `empty()`, `password_verify()` | Session Redirect |
| **User Management** | Client + Server | JS manual check + Controller JSON | Toast Notifications |
| **Reservations** | Client + Server | HTML5 `required` + Controller checks | Dynamic Message Box |
| **File Uploads** | Server-side | PHP `$_FILES` checks | Session Redirect / JSON |

---

## 6. Recommendations for Strengthening Validation
1. **Centralized Validator:** Implement a dedicated `Validator` class in `core/` to standardize error messages and logic across all controllers.
2. **Consistent CSRF Protection:** Ensure all POST forms include a CSRF token to prevent cross-site request forgery.
3. **Frontend-Backend Sync:** Use the same regex patterns in both JS and PHP to ensure consistency.
