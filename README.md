# UOC Sports Management System

A comprehensive web-based sports management system for the University of Colombo, designed to streamline sports administration, facility reservations, equipment management, attendance tracking, and budget management.

## 🎯 Overview

The UOC Sports Management System is a full-featured platform that enables efficient management of university sports activities. It provides role-based access for different user types including administrators, students, captains, coaches, sport managers, equipment managers, and registrars.

## ✨ Key Features

### For Students
- **Sport Enrollment**: Browse and enroll in available sports
- **Facility Reservation**: Book sports facilities for practice or tournaments
- **Equipment Reservation**: Reserve sports equipment with time slot management
- **News Feed**: Stay updated with latest sports news and announcements
- **Profile Management**: Update personal information and view enrolled sports

### For Captains
- **Attendance Tracking**: Mark and monitor team attendance for practice sessions
- **Team Management**: Add and manage team members
- **Practice Scheduling**: Schedule practice sessions with facility booking
- **Team Communication**: Communicate with team members
- **Attendance Analytics**: View attendance percentages and history

### For Coaches
- **Team Schedules**: View and manage team practice schedules
- **Injury Reporting**: Report and track player injuries
- **Team Communication**: Communicate with players and staff

### For Sport Managers
- **Budget Management**: Track allocated budgets and expenses
- **Transaction Management**: Add and update financial transactions
- **Expense Tracking**: Monitor spending against allocated budgets
- **Schedule Management**: View and manage sport schedules
- **Remaining Budget Analytics**: Real-time budget tracking

### For Equipment Managers
- **Equipment Inventory**: Manage equipment stock and availability
- **Reservation Management**: View and manage equipment reservations
- **Equipment Tracking**: Track usable vs. total equipment quantities
- **Schedule Viewing**: Monitor equipment usage schedules

### For Registrars
- **Student Verification**: Verify and approve student registrations
- **Staff Verification**: Verify and approve staff accounts
- **Booking Verification**: Review and approve facility bookings

### For Administrators
- **User Management**: Create and manage user accounts
- **Post Management**: Create, edit, and manage news feed posts
- **System Overview**: Dashboard with system-wide analytics
- **Role Assignment**: Assign roles to users (Captain, Coach, Manager, etc.)

## 🏗️ System Architecture

### MVC Architecture
The system follows the Model-View-Controller (MVC) architectural pattern:

```
uoc-sports/
├── app/
│   ├── controllers/          # Application controllers
│   │   ├── api/             # API controllers for AJAX requests
│   │   ├── AuthController.php
│   │   ├── StudentController.php
│   │   ├── CaptainController.php
│   │   ├── CoachController.php
│   │   ├── SportManagerController.php
│   │   ├── EquipmentManagerController.php
│   │   └── ...
│   ├── models/              # Data models
│   │   ├── User.php
│   │   ├── Sport.php
│   │   ├── Equipment.php
│   │   ├── Facility.php
│   │   ├── Attendance.php
│   │   ├── Budget.php
│   │   └── ...
│   └── views/               # View templates
│       ├── admin/
│       ├── student/
│       ├── captain/
│       ├── coach/
│       ├── sports-manager/
│       ├── equipment-manager/
│       ├── registrar/
│       ├── general/
│       └── templates/
├── core/                    # Core framework files
│   ├── Router.php          # Custom routing system
│   ├── Database.php        # Database connection
│   ├── Model.php           # Base model class
│   ├── autoload.php        # Class autoloader
│   └── helpers.php         # Helper functions
├── config/                  # Configuration files
│   └── config.php          # Database and app configuration
├── public/                  # Public assets
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   ├── images/             # Image assets
│   ├── index.php           # Application entry point
│   └── .htaccess           # Apache configuration
└── SQL_FILES/              # Database schema
    └── Main.sql            # Database structure and sample data
```

### Database Schema

The system uses MySQL with 25+ tables including:

**Core Tables:**
- `user` - User accounts with role-based access
- `sport` - Sports information and assignments
- `faculty` - Faculty/department information

**Facility Management:**
- `facility` - Sports facilities
- `facility_rates` - Pricing for facilities
- `facility-booking` - Facility reservations
- `payment` - Payment records

**Equipment Management:**
- `equipment` - Equipment catalog
- `equipment_inventory` - Stock tracking
- `equipment-requests` - Equipment reservations

**Attendance & Practice:**
- `attendance` - Attendance records
- `practice-session` - Practice session scheduling
- `practice_sessions` - Session details

**Budget Management:**
- `budget` - Budget allocations
- `transaction` - Financial transactions

**News & Communication:**
- `newsfeed_post` - News posts
- `newsfeed_post_image` - Post images
- `comment` - Post comments
- `inquiry` - User inquiries

**Tournament Management:**
- `tournament` - Tournament information
- `tournament_match` - Match details
- `tournament_result` - Match results
- `sport_result_field` - Dynamic sport-specific fields
- `sport_result_value` - Result values

**Team Management:**
- `sports-team` - Team member assignments
- `injury_report` - Injury tracking

**Other:**
- `lost_found` - Lost and found items
- `lost_found_images` - Item images
- `remember_tokens` - Session management

## 🛠️ Technology Stack

### Backend
- **PHP 8.0+** - Server-side programming
- **MySQL 8.0+** - Database management
- **PDO** - Database abstraction layer
- **Custom MVC Framework** - Lightweight routing and architecture

### Frontend
- **HTML5** - Markup
- **CSS3** - Styling
- **JavaScript (Vanilla)** - Client-side interactivity
- **AJAX** - Asynchronous data fetching

### Server
- **Apache** - Web server
- **WAMP/XAMPP** - Local development environment

## 📋 Prerequisites

- **PHP** >= 8.0
- **MySQL** >= 8.0
- **Apache** with mod_rewrite enabled
- **WAMP/XAMPP** or similar local server environment

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/CCWizma99/UOC-Sports.git
cd UOC-Sports
```

### 2. Database Setup
1. Start your MySQL server (via WAMP/XAMPP)
2. Create a new database named `uoc-sports`
3. Import the database schema:
   ```bash
   mysql -u root -p uoc-sports < SQL_FILES/Main.sql
   ```

### 3. Configure Database Connection
Edit `config/config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'uoc-sports');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Configure Base Path
Edit `core/Router.php` and update the base path to match your project location:
```php
$basePath = '/uoc-sports/public';
```

### 5. Start the Server
1. Place the project in your web server directory (e.g., `C:\wamp64\www\`)
2. Start Apache and MySQL
3. Access the application at: `http://localhost/uoc-sports/public/`

## 👥 User Roles & Access

| Role | Type Code | Description |
|------|-----------|-------------|
| **Admin** | ADMIN | Full system access, user management, content management |
| **Student** | STUDENT | Enroll in sports, reserve facilities/equipment, view news |
| **Captain** | CAPTAIN | Team management, attendance tracking, practice scheduling |
| **Coach** | COACH | Team schedules, injury reporting, communication |
| **Sport Manager** | SPT | Budget management, expense tracking, schedules |
| **Equipment Manager** | EQP | Equipment inventory, reservation management |
| **Registrar** | REG | Verify students, staff, and bookings |
| **Public** | PUBLIC | Limited access, view public content |

## 🔑 Default Credentials

After importing the database, you can use these sample accounts:

**Admin Account:**
- Email: `chamal.admin@uocs.com`
- Password: (Set during first login)

**Student Account:**
- Email: `chamal2@gmail.com`
- Password: (Set during registration)

*Note: Most accounts in the sample data require password setup on first login.*

## 📱 Key Functionalities

### Routing System
The application uses a custom router (`core/Router.php`) that supports:
- GET and POST requests
- Dynamic route parameters (e.g., `/post/{id}`)
- Controller@method action mapping

### API Endpoints
RESTful API endpoints for AJAX operations:
- `/api/attendance/*` - Attendance management
- `/api/budget/*` - Budget operations
- `/api/equipment/*` - Equipment operations
- `/api/facility/*` - Facility operations
- `/api/post/*` - Post operations
- `/api/sport/*` - Sport operations

### File Upload Management
- Profile images stored in `app/internal/profile_img/`
- Post images stored in `public/images/posts/`
- Transaction proofs stored with unique identifiers

## 🔒 Security Features

- **Password Hashing**: BCrypt password hashing
- **Session Management**: PHP sessions with remember tokens
- **SQL Injection Prevention**: Prepared statements with PDO
- **Role-Based Access Control**: Route-level access control
- **Input Validation**: Server-side validation

## 📊 Database Features

- **Dynamic Sport Fields**: Customizable result fields per sport
- **Attendance Tracking**: Comprehensive attendance with percentages
- **Budget Monitoring**: Real-time budget vs. spending tracking
- **Facility Pricing**: Dynamic pricing based on facility type and time
- **Equipment Availability**: Real-time stock and usability tracking

## 🎨 UI Components

The system includes responsive UI components for:
- News feed with image carousel
- Facility reservation calendar
- Equipment search and booking
- Attendance marking interface
- Budget tracking dashboards
- Profile management
- Comment system

## 🔄 Recent Updates

Based on conversation history, recent improvements include:
- Fixed enrolled sports display for Captains and Students
- Implemented captain attendance marking system
- Updated equipment model to use `usable` column
- Implemented enable/disable commenting for posts
- Added sport enrollment feature for students
- Standardized UI styles across reservation pages

## 🤝 Contributing

This is a university project. For contributions or issues, please contact the development team.

## 📄 License

This project is developed for the University of Colombo Sports Management.

## 📞 Support

For inquiries or support, use the Contact Us page within the application or reach out to the sports administration office.

---

**Developed for University of Colombo Sports Management**
