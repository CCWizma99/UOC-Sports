# System Business Logic Analysis

This document tracks identified logic gaps, inconsistencies, and business rule violations in the UOC Sports system that require future attention.

## 1. Resource Access & Conflicts

### ⚠️ Match Scheduling Isolation
- **Status**: Critical
- **Finding**: The `SportCompetition` module operates in a vacuum. Matches are scheduled without checking if the required **Facility** is free or if a **Team Practice** is already occurring at that time.
- **Risk**: Double-booked venues and conflicting team schedules.

### ⚠️ Athlete Sport-Locking
- **Status**: Moderate
- **Finding**: Users are mapped to a single `sport_id` in the `user` table.
- **Risk**: Prevents multi-disciplinary students from participating in or being managed under multiple sport categories.

### ⚠️ Time-Slot Fragmentation
- **Status**: Partially Resolved (Facilities/Equipment)
- **Finding**: Lack of standardized 30-minute intervals led to "dead zones" in facility availability.
- **Logic Rule**: All reservations and practices should enforce 30-minute precision (`:00` or `:30`).

---

## 2. Inventory & Equipment Management

### ⚠️ Non-Audited Inventory Changes
- **Status**: Moderate
- **Finding**: Equipment `usable` counts are edited manually in the database/inventory view.
- **Missing Logic**: There is no "Incident/Damage Report" linked to practice sessions.
- **Risk**: Loss of accountability for damaged or lost gear.

### ⚠️ Student Request Over-Reach
- **Status**: In-Progress (Currently being implemented)
- **Finding**: Students could previously request any equipment in any quantity.
- **Logic Rule**: Items must be flagged as `st_req=YES` and capped by `st_max_qty`.

---

## 3. Financial & Administrative Workflows

### ⚠️ Budget Over-Expenditure
- **Status**: Moderate
- **Finding**: Managers can submit expenses that exceed the remaining allocated budget for a sport.
- **Logic Rule**: Expense total must be validated against `budget.remaining_amount`.

### ⚠️ Pending Payment Deadlocks
- **Status**: Low
- **Finding**: No "Auto-Release" timer for pending facility reservations.
- **Risk**: Users can block slots by submitting pending requests/slips without completing payment, effectively locking out other paying users.

### ⚠️ Authorization Hierarchy
- **Status**: Low
- **Finding**: Practice sessions and equipment requests are "Self-Approved."
- **Logic Rule**: Coach/Captain requests should require a "Manager Approval" status transition.

---

## 4. Technical Logic Inconsistencies

### ⚠️ The "Touch" Conflict Bug
- **Status**: Resolved (in Facility/Practice models)
- **Finding**: Using `<=` instead of `<` in overlap queries prevented back-to-back bookings.
- **Standard**: Always use `(new_start < existing_end AND new_end > existing_start)`.

### ⚠️ Hardcoded Location Strings
- **Status**: Partially Resolved
- **Finding**: Locations stored as strings ("Indoor Court") instead of Foreign Keys (`FAC_001`).
- **Risk**: Duplicate locations due to typos and inability to filter schedules by physical venue.
