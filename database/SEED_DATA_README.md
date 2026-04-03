# UOC Sports System - Database Seed Data Guide

## Overview

The `seed_data.sql` file in `/database/` contains comprehensive test data to populate your UOC Sports database with realistic data demonstrating full system functionality.

## What's Included

### Data Categories (210+ records)

**Users (24 total)**
- 5 Coaches (assigned to different sports)
- 5 Sport Managers
- 5 Captains
- 10 Students/Athletes  
- 2 Executives (for dashboard testing)

**Sports & Organization (5 sports)**
- Cricket (Faculty 1)
- Badminton (Faculty 1)
- Football (Faculty 2)
- Volleyball (Faculty 1)
- Tennis (Faculty 2)

**Financial Data (50+ records)**
- 10 Budget records (2025-2026, allocated & spent)
- 40 Sport Expense records with receipts
- 10 Transaction records
- Budget categories: Equipment, Travel, Training, Maintenance, etc.

**Facility & Equipment (40+ records)**
- 5 Facility types with pricing
- 8 Equipment types
- Equipment inventory (quantities by sport)
- 10 Good Received Notes (procurement history)

**Events & Activities (40+ records)**
- 5 Tournaments (2025-2026)
- 10 Tournament matches with results
- 10 Practice sessions
- 19 Attendance records

**Additional Features (30+ records)**
- 12 Sports achievements
- 8 Newsfeed posts with comments
- 7 Inquiries (resolved/pending)
- 6 Equipment booking requests
- 15 Facility bookings
- 5 Lost items with claims

## How to Import

### Option 1: MySQL Command Line

```bash
mysql -u your_username -p uoc-sports < /path/to/seed_data.sql
```

Then enter your MySQL password when prompted.

### Option 2: phpMyAdmin

1. Open phpMyAdmin in your browser
2. Select the `uoc-sports` database
3. Click the **Import** tab
4. Choose the file: `/database/seed_data.sql`
5. Click **Go** to import

### Option 3: Inside PHP Script

```php
$file = file_get_contents('/database/seed_data.sql');
$queries = explode(';', $file);

foreach ($queries as $query) {
    if (trim($query)) {
        try {
            $db->exec($query);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
```

## What This Enables for Testing

✅ **Executive Dashboard Phase 1 - Faculty Scoping**
- View data filtered by Faculty 1 (Science) and Faculty 2 (Arts)
- See 5 different sports with metrics
- Budget data shows proper allocation/spending across faculties

✅ **Executive Dashboard Phase 2 - Exports**
- Export CSV with 50+ expense records showing structured breakdown
- Export PDF with professional formatted reports
- All budget and tournament data included

✅ **Executive Dashboard Phase 3 - Drill-down Analytics**
- Click on individual sports to see:
  - Budget breakdown
  - Completed tournaments and matches
  - Achievements (12 total across sports)
  - Equipment inventory status
- Budget trends show spending patterns from consecutive months
- Facility utilization shows 15 bookings across multiple facilities

✅ **Complete System Demonstration**
- Users can login with various roles (Coach, Manager, Captain, Student, Executive)
- Posts and comments show community engagement
- Equipment requests and facility bookings show workflow
- Lost/found items show complete lifecycle
- Inquiries show support system usage

## Key Data Points for Dashboard

### Budget Summary (Phase 1-3)
- **Total University Budget 2025-2026**: Rs. 3,070,000
- **Total Spent 2025**: Rs. 1,280,000 (42% utilization)
- **By Sport** (2025):
  - Cricket: Rs. 500,000 allocated | Rs. 285,000 spent (57%)
  - Football: Rs. 400,000 allocated | Rs. 220,000 spent (55%)
  - Badminton: Rs. 250,000 allocated | Rs. 145,000 spent (58%)
  - Volleyball: Rs. 200,000 allocated | Rs. 95,000 spent (47%)
  - Tennis: Rs. 180,000 allocated | Rs. 60,000 spent (33%)

### Events & Activities
- 3 Completed tournaments (2025)
- 7 Completed matches
- 3 Upcoming tournaments (2026)
- 10 Scheduled matches

### Facilities & Equipment
- 15 Facility bookings showing utilization
- 8 Equipment types across 5 sports
- 8 GRN (Good Received Notes) showing procurement
- 6 Active equipment booking requests

## Data Integrity

✅ All foreign key constraints respected
✅ Faculty assignments properly linked
✅ User role assignments are consistent
✅ Budget-Expense relationships are valid
✅ Tournament-Match hierarchy maintained
✅ All dates are logically ordered

## Customization

If you need to modify data:

1. **Change dates**: Replace all occurrences of 2025/2026 with desired year
2. **Add more students**: Duplicate STU101-STU110 blocks with new IDs
3. **Increase budget amounts**: Edit the `allocated_amount` fields
4. **Add tournaments**: Insert new records in tournament and tournament-match sections

## Troubleshooting

**"Duplicate entry" errors?**
- The script includes DELETE statements to clear old seed data first
- If errors persist, check if the database exists (should already exist from schema)

**"Foreign key constraint fails"?**
- Ensure Main.sql (schema) was loaded first
- Data is loaded in correct order: Faculty → Users → Sports → Budget → etc.

**Want to reset?**
- Run: `mysql -u username -p uoc-sports < /database/Main.sql` (resets schema)
- Then run seed_data.sql again

## Test Logins

After importing, you can login with these credentials:

| User ID | Password | Role | Faculty |
|---------|----------|------|---------|
| COACH01 | password | Coach | 1 (Science) |
| MGR001 | password | Sport Manager | 1 (Science) |
| CAP001 | password | Captain | 1 (Science) |
| STU101 | password | Student | 1 (Science) |
| EXE001 | password | Executive | 1 (Science) |
| EXE002 | password | Executive | 2 (Arts) |

*Note: All passwords are the bcrypt hash of "password". Change them in production!*

## Support

For database schema questions, refer to Main.sql
For application questions, check the README.md in the project root
