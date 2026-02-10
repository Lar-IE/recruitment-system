# Company Logo Feature Implementation

## Overview
This document outlines the implementation of the Company Logo feature for the Employer account.

## Features Implemented

### 1. Database Changes
- **Migration**: `database/migrations/2026_02_10_000001_add_company_logo_to_employers_table.php`
  - Adds `company_logo` column to the `employers` table
  - Stores the file path to the uploaded logo

### 2. Model Updates
- **Employer Model**: Added `company_logo` to the `$fillable` array

### 3. Controllers
- **CompanyLogoController**: `app/Http/Controllers/Employer/CompanyLogoController.php`
  - `update()`: Handles logo upload/update (main employer only)
  - `destroy()`: Handles logo deletion (main employer only)
  - Both methods check if the user is the main employer (not a sub-user)

### 4. Form Request Validation
- **UpdateCompanyLogoRequest**: `app/Http/Requests/Employer/UpdateCompanyLogoRequest.php`
  - Validates image file type: jpg, jpeg, png, svg
  - Maximum file size: 2MB
  - Custom error messages

### 5. Routes
Added to `routes/web.php`:
- `GET /employer/company-settings` - View company settings page
- `POST /employer/company-logo` - Upload/update logo
- `DELETE /employer/company-logo` - Remove logo

### 6. Views
- **Company Settings Page**: `resources/views/employer/company-settings.blade.php`
  - Shows current logo (if exists)
  - Upload form for main employer
  - View-only mode for sub-users
  - Displays company information
  
- **Job List View**: Updated `resources/views/jobseeker/jobs/index.blade.php`
  - Displays employer logo alongside job title
  - Shows company name
  
- **Job Detail View**: Updated `resources/views/jobseeker/jobs/show.blade.php`
  - Displays employer logo and company information at the top
  - Shows company name and industry

### 7. Controller Updates
- **JobController**: Updated `app/Http/Controllers/Jobseeker/JobController.php`
  - Eager loads employer relationship in both `index()` and `show()` methods
  - Prevents N+1 query issues

## Access Control

### Main Employer
- Can upload company logo
- Can update company logo
- Can delete company logo
- Full access to Company Settings page

### Sub-Users
- Can view company logo
- **Cannot** upload/update/delete logo
- View-only access to Company Settings page
- See appropriate message: "Only the main employer can upload or update the company logo."

## Logo Display Rules

### Job Listings
- Logo displayed next to job title when available
- Company name always shown
- Location and job type information included

### Job Details
- Logo and company name displayed prominently at the top
- Shows company industry if available
- Falls back gracefully if no logo exists

### When Sub-User Posts Job
- Job automatically references the `employer_id`
- Employer logo is always shown (belongs to main employer)
- Works seamlessly for all job posts regardless of who created them

## Technical Implementation Details

### File Storage
- Logos stored in `storage/app/public/logos/` directory
- Uses Laravel's `public` disk
- File paths saved as relative paths in database (e.g., `logos/abc123.jpg`)

### Validation Rules
```php
'company_logo' => [
    'required',
    'image',
    'mimes:jpg,jpeg,png,svg',
    'max:2048', // 2MB in kilobytes
]
```

### Authorization
- Uses existing middleware: `employer.user` and `employer.approved`
- Checks `employer_owner` attribute set by `EnsureEmployerUser` middleware
- Only main employer (not sub-users) can upload/update/delete

## Setup Instructions

### 1. Run the Migration
```bash
php artisan migrate
```

### 2. Create Storage Link (if not already created)
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`, allowing uploaded logos to be accessible via the web.

### 3. Ensure Proper Directory Permissions
Make sure the `storage` directory is writable:
```bash
# On Linux/Mac
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# On Windows with XAMPP, ensure the storage folder has write permissions
```

## Testing Checklist

### Main Employer
- [ ] Can access Company Settings page
- [ ] Can upload a new logo (jpg, png, svg)
- [ ] Can update existing logo
- [ ] Can delete logo
- [ ] Logo appears on all job listings
- [ ] Logo appears on job detail pages
- [ ] Validation works (file size, file type)

### Sub-Users
- [ ] Can access Company Settings page
- [ ] Can view logo but cannot upload/update/delete
- [ ] See appropriate restriction message
- [ ] When posting a job, employer logo displays correctly

### Job Seekers
- [ ] Can see employer logo on job listings
- [ ] Can see employer logo on job detail pages
- [ ] Company name displays correctly
- [ ] No errors when employer has no logo

## Files Created/Modified

### Created Files
1. `database/migrations/2026_02_10_000001_add_company_logo_to_employers_table.php`
2. `app/Http/Controllers/Employer/CompanyLogoController.php`
3. `app/Http/Requests/Employer/UpdateCompanyLogoRequest.php`
4. `resources/views/employer/company-settings.blade.php`

### Modified Files
1. `app/Models/Employer.php`
2. `routes/web.php`
3. `app/Http/Controllers/Employer/PagesController.php`
4. `app/Http/Controllers/Jobseeker/JobController.php`
5. `resources/views/jobseeker/jobs/index.blade.php`
6. `resources/views/jobseeker/jobs/show.blade.php`

## Navigation
To access the Company Settings page, add a navigation link in your employer layout/menu:
```blade
<a href="{{ route('employer.company-settings') }}">Company Settings</a>
```

## Notes
- All jobs reference `employer_id`, ensuring consistency
- Sub-users' job posts automatically show the main employer's logo
- Graceful fallback when no logo exists
- Proper validation prevents oversized or invalid file types
- Old logo files are automatically deleted when updating
- Uses Laravel's built-in file storage system for reliability
