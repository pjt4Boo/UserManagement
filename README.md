# User Management System

A comprehensive Laravel 11 User Management application with role-based authorization, PostgreSQL ENUM types, soft deletes, and complete audit logging.

## Features

### 1. User CRUD Operations
- ✅ Create new users with email, name, role, and status
- ✅ View user details and user list (paginated, 15 per page)
- ✅ Edit user information (name, email, role, status)
- ✅ Deactivate/activate users (without permanent deletion)
- ✅ Permanently delete users (admin-only with verification)

### 2. Roles & Authorization
- **Two Roles:** Admin and User
- **Admin Permissions:**
  - Manage all users (create, read, update, delete)
  - View audit logs
  - Deactivate/activate users
- **User Permissions:**
  - View own profile
  - Cannot manage other users
- **Implementation:** Laravel Policies (UserPolicy) - no role checks in controllers

### 3. PostgreSQL Requirements
- **ENUM Types:** Two custom PostgreSQL ENUM types ensure type safety:
  - `role_enum`: 'admin' | 'user'
  - `status_enum`: 'active' | 'inactive'
- **Why ENUMS?** 
  - **Type Safety:** Database constrains values at schema level, preventing invalid data
  - **Performance:** Stored as efficient 4-byte integers internally
  - **Consistency:** Guarantees valid values across all queries
  - **Alternative:** CHECK constraints work, but ENUMs are cleaner for fixed value sets

### 4. Web Pages & UI
- **Login Page:** Email/password authentication with registration link
- **Register Page:** Self-service user registration (creates users with 'user' role automatically)
- **User List:** Paginated table with name, email, role, status, action buttons
- **Create User Form:** Admin-only form with name, email, password, role, status fields
- **Edit User Form:** Modify user information with email uniqueness validation
- **User Detail:** View full user profile with action buttons
- **Audit Logs:** Complete change history for each user

### 5. UI & Responsive Design
- **Framework:** Tailwind CSS v4 with dark mode support
- **Components:** Reusable Blade components (buttons, inputs, layouts)
- **Responsive:** Mobile-first design - works on all screen sizes
- **States:** Loading indicators, empty states, validation messages

### 6. Data Validation
- **Form Requests:** Centralized validation logic
  - CreateUserRequest: name, email (unique), role, status, password
  - UpdateUserRequest: name, email (unique), role, status
- **Error Messages:** Clear validation feedback with field-level errors

### 7. Soft Deletes
- ✅ Users marked as deleted, not permanently removed
- ✅ Preserved data maintained in database
- ✅ Can be restored if needed
- ✅ Soft-deleted users excluded from normal queries

### 8. Audit Logging
- **Automatic Tracking:** All user changes captured via Auditable trait
- **Captured Actions:** created, updated, deactivated, deleted
- **Logged Data:** Actor (who made change), timestamp, field changes (before/after)
- **Audit Log Page:** View complete change history for each user

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php (with AuthorizesRequests trait)
│   │   ├── UserController.php (CRUD + authorization)
│   │   └── Auth/ (7 authentication controllers)
│   └── Requests/
│       ├── CreateUserRequest.php
│       └── UpdateUserRequest.php
├── Models/
│   ├── User.php (SoftDeletes, Auditable)
│   └── AuditLog.php
├── Policies/
│   └── UserPolicy.php (Authorization rules)
└── Traits/
    └── Auditable.php (Auto-logs changes)

database/
├── migrations/
│   ├── create_users_table.php
│   ├── update_users_table_add_role_status.php (ENUM types)
│   ├── create_jobs_table.php
│   ├── create_cache_table.php
│   └── create_audit_logs_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── auth/ (login, register, password reset, email verification)
│   ├── users/ (list, create, edit, show, audit-logs)
│   ├── components/ (reusable Blade components)
│   ├── layouts/ (app, guest, navigation)
│   └── welcome.blade.php
└── css/app.css

routes/
├── web.php (user management routes)
└── auth.php (authentication routes)

tests/
├── Feature/
│   ├── Auth/ (registration, login, email verification tests)
│   └── Users/ (CRUD operations, authorization tests)
└── Unit/
```

## Installation & Setup

```bash
# 1. Clone and install dependencies
composer install
npm install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Configure PostgreSQL
# Edit .env with your PostgreSQL database credentials:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=user_management
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# 4. Run migrations (creates ENUM types and tables)
php artisan migrate

# 5. Start development server
php artisan serve

# 6. Access application
# Visit: http://localhost:8000
```

## Default Test Account

After running migrations:
- **Email:** test@example.com
- **Password:** password

Use this account to log in and test the application.

## Routes

### Public Routes
- `GET /` - Welcome page
- `GET /login` - Login form
- `POST /login` - Login submission
- `GET /register` - Registration form
- `POST /register` - Registration submission

### Protected Routes (Auth Required)
- `GET /users` - List all users (paginated)
- `GET /users/create` - Create user form
- `POST /users` - Store new user
- `GET /users/{id}` - View user details
- `GET /users/{id}/edit` - Edit user form
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user (hard delete)
- `POST /users/{id}/deactivate` - Deactivate user
- `POST /users/{id}/activate` - Activate user
- `GET /users/{id}/audit-logs` - View user change history

## Testing

```bash
# Run feature and unit tests
php artisan test

# Run specific test
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Run with coverage
php artisan test --coverage
```

## Architecture Decisions

### Why Policies Over Role Checks?
- **Separation of Concerns:** Authorization logic isolated from controllers
- **Reusability:** Policies used across controllers, tests, templates
- **Testability:** Easy to unit test authorization rules
- **Maintainability:** Changes to permissions in one place

### Why Auditable Trait?
- **Single Responsibility:** Trait encapsulates audit logging logic
- **Reusability:** Can be applied to other models needing audit trails
- **Automatic:** Changes captured via Eloquent events, no manual logging needed

### Why Form Requests?
- **Validation Rules:** Business logic rules defined in specialized class
- **Error Messages:** Consistent validation feedback across application
- **Authorization:** Can check permissions before processing request

## Security Features

- ✅ Password hashing (bcrypt)
- ✅ CSRF protection on all forms
- ✅ Role-based authorization checks
- ✅ Email uniqueness validation
- ✅ Soft deletes prevent accidental permanent data loss
- ✅ Audit trail for compliance and debugging

## Future Enhancements

- [ ] Additional roles (Manager, Operator, Viewer)
- [ ] Bulk user import/export
- [ ] Email notifications for user actions
- [ ] Two-factor authentication (2FA)
- [ ] User activity timeline
- [ ] Advanced filtering and search
- [ ] API endpoints with token authentication
- [ ] Permission-based access control (RBAC) instead of just roles

## License

MIT
