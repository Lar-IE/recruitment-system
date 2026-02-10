# Company Settings Integration into Profile

## Summary of Changes

The Company Settings (logo upload feature) has been successfully integrated into the Profile page for employer accounts, eliminating the need for a separate company settings page.

## What Changed

### 1. Profile Controller Updates
**File**: `app/Http/Controllers/ProfileController.php`

- Added employer and sub-user detection in the `edit()` method
- Passes `$employer` and `$isOwner` variables to the view
- Sub-users can now access the profile page and view company settings
- Main employers have full access to upload/update logos

### 2. Profile View Enhancement
**File**: `resources/views/profile/edit.blade.php`

Added two new sections for employer users:

#### Company Logo Section
- Displays current company logo
- Upload/update form (main employer only)
- Remove logo button (main employer only)
- View-only mode for sub-users

#### Company Information Section
- Displays company details (name, email, phone, website, industry, size, address)
- Read-only display for all employer users

### 3. New Partial View
**File**: `resources/views/profile/partials/employer-company-logo-form.blade.php`

Reusable component for the logo upload functionality:
- Handles both main employer and sub-user views
- Shows upload form for main employers
- Shows view-only for sub-users with appropriate message
- Displays current logo if exists
- Includes validation requirements display

### 4. Controller Redirect Updates
**File**: `app/Http/Controllers/Employer/CompanyLogoController.php`

- Changed redirect from `redirect()->back()` to `redirect()->route('profile.edit')`
- Ensures users return to the profile page after upload/delete actions

## How It Works

### For Main Employers
When a main employer clicks "Profile" in the navigation:

1. **Profile Information** - Update name and email
2. **Company Logo** - Upload/update/remove company logo
3. **Company Information** - View company details
4. **Password** - Update password
5. **Delete Account** - Delete account option

### For Sub-Users
When a sub-user clicks "Profile" in the navigation:

1. **Profile Information** - Update name and email
2. **Company Logo** - View company logo (read-only with message)
3. **Company Information** - View company details (read-only)
4. **Password** - Update password
5. **Delete Account** - Delete account option

## Navigation Structure

The navigation dropdown now works as follows:

```
User Dropdown Menu
├── Profile → /profile (Main profile page with company settings)
└── Account Settings → /account-settings (Basic account settings)
```

### Profile Page (`/profile`)
- **Employers**: Name, Email, Company Logo, Company Info, Password, Delete Account
- **Sub-Users**: Name, Email, Company Logo (view), Company Info (view), Password, Delete Account
- **Jobseekers**: Name, Email, Resume/Skills, Password, Delete Account

### Account Settings Page (`/account-settings`)
- Simple page with: Name, Email, Password, Delete Account
- No role-specific sections

## Access Control

### Main Employer
✅ Can view company logo  
✅ Can upload new logo  
✅ Can update existing logo  
✅ Can remove logo  
✅ Can view company information  

### Sub-Users (All Roles)
✅ Can view company logo  
❌ Cannot upload logo (shows message)  
❌ Cannot update logo (shows message)  
❌ Cannot remove logo (button not visible)  
✅ Can view company information  

## Routes

### Profile Routes (Available to All Authenticated Users)
- `GET /profile` → Shows profile page with role-specific sections
- `PATCH /profile` → Update profile information
- `DELETE /profile` → Delete account

### Company Logo Routes (Employer Users Only)
- `POST /employer/company-logo` → Upload/update logo (main employer only)
- `DELETE /employer/company-logo` → Remove logo (main employer only)

### Legacy Route (Still Available)
- `GET /employer/company-settings` → Separate company settings page (optional, can be removed)

## Benefits

1. **Unified Experience**: All profile-related settings in one place
2. **Consistent Navigation**: Users don't need to navigate to multiple pages
3. **Better UX**: Logical grouping of related information
4. **Role-Based Display**: Automatically shows/hides features based on user role
5. **Sub-User Support**: Sub-users can view company information without confusion

## Testing

### Main Employer Test
1. Login as main employer
2. Click "Profile" in dropdown
3. Verify you see:
   - Profile information form
   - Company logo upload section
   - Company information section
   - Password update form
   - Delete account section
4. Upload a logo → Should succeed and redirect back to profile
5. Remove logo → Should succeed and redirect back to profile

### Sub-User Test
1. Login as sub-user
2. Click "Profile" in dropdown
3. Verify you see:
   - Profile information form
   - Company logo (view-only with restriction message)
   - Company information section
   - Password update form
   - Delete account section
4. Try to access `/employer/company-logo` directly → Should get 403 error

## Optional: Remove Separate Company Settings Route

If you want to completely remove the separate company settings page:

1. Remove route from `routes/web.php`:
   ```php
   // Delete these lines:
   Route::get('/company-settings', [EmployerPagesController::class, 'companySettings'])
       ->middleware('employer.role:admin,recruiter,viewer')
       ->name('company-settings');
   ```

2. Remove method from `app/Http/Controllers/Employer/PagesController.php`:
   ```php
   // Delete the companySettings() method
   ```

3. Remove or archive view file:
   - `resources/views/employer/company-settings.blade.php`

## Files Modified

1. `app/Http/Controllers/ProfileController.php` - Added employer/sub-user support
2. `resources/views/profile/edit.blade.php` - Added company settings sections
3. `app/Http/Controllers/Employer/CompanyLogoController.php` - Updated redirects
4. `resources/views/profile/partials/employer-company-logo-form.blade.php` - New partial view

## Files That Can Be Removed (Optional)

1. `resources/views/employer/company-settings.blade.php` - Now redundant
2. Route entry in `routes/web.php` for `/company-settings`
3. Method in `app/Http/Controllers/Employer/PagesController.php`

---

**Note**: The separate company-settings page still exists for backward compatibility. You can keep it or remove it based on your preference.
