# Company Settings Module & Sub-User Authentication Troubleshooting

## Company Settings Module Location

### Route Information
- **URL**: `/employer/company-settings`
- **Route Name**: `employer.company-settings`
- **HTTP Method**: GET
- **Access**: All employer users (main employer and sub-users)
- **Middleware**: `employer.user`, `employer.approved`, `employer.role:admin,recruiter,viewer`

### File Locations
1. **Controller**: `app/Http/Controllers/Employer/PagesController.php`
   - Method: `companySettings(Request $request)`
   
2. **View**: `resources/views/employer/company-settings.blade.php`

3. **Routes**: `routes/web.php` (line ~216)
   ```php
   Route::get('/company-settings', [EmployerPagesController::class, 'companySettings'])
       ->middleware('employer.role:admin,recruiter,viewer')
       ->name('company-settings');
   ```

---

## Sub-User Authentication Flow

### Authentication Process
The authentication is handled by a dual-guard system in `app/Http/Requests/Auth/LoginRequest.php`:

1. **First Attempt**: Try to authenticate with the `web` guard (regular users)
2. **Second Attempt**: Try to authenticate with the `employer_sub_user` guard
3. **Validation**: Check if sub-user is active and employer is approved
4. **Success**: Redirect to employer dashboard

### Authentication Guards Configuration
Location: `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'employer_sub_user' => [
        'driver' => 'session',
        'provider' => 'employer_sub_users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'employer_sub_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\EmployerSubUser::class,
    ],
],
```

---

## Common Sub-User Authentication Issues & Solutions

### Issue 1: Table Not Found Error
**Error**: `SQLSTATE[42S02]: Base table or view not found: 'employer_sub_users'`

**Solution**: Run the migration
```bash
php artisan migrate
```

The migration file: `database/migrations/2026_01_27_000001_create_employer_sub_users_table.php`

---

### Issue 2: "Your account is inactive or not approved"
**Cause**: Either the sub-user status is 'inactive' OR the main employer status is not 'approved'

**Solution**:
1. Check sub-user status in database:
   ```sql
   SELECT id, name, email, status, employer_id FROM employer_sub_users WHERE email = 'subuser@example.com';
   ```
   - Status should be `'active'`

2. Check employer status:
   ```sql
   SELECT id, company_name, status FROM employers WHERE id = [employer_id];
   ```
   - Status should be `'approved'`

3. To fix sub-user status:
   ```sql
   UPDATE employer_sub_users SET status = 'active' WHERE email = 'subuser@example.com';
   ```

4. To approve employer (as admin):
   - Go to Admin panel → Employers
   - Click "Approve" on the employer

---

### Issue 3: Password Not Working
**Cause**: Password might not be hashed correctly

**Check**: The `EmployerSubUser` model should have password casting:
```php
protected function casts(): array
{
    return [
        'password' => 'hashed',
    ];
}
```

**Solution**: If password isn't hashed, update it:
```bash
php artisan tinker
```
```php
$subUser = App\Models\EmployerSubUser::where('email', 'subuser@example.com')->first();
$subUser->password = 'new-password';
$subUser->save();
```

---

### Issue 4: Session/Cookie Issues
**Cause**: Session guard not working properly

**Solution**:
1. Clear application cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. Clear browser cookies and try again

3. Check `.env` file for session configuration:
   ```env
   SESSION_DRIVER=database
   SESSION_LIFETIME=120
   ```

4. Make sure session table exists:
   ```bash
   php artisan session:table
   php artisan migrate
   ```

---

## Debugging Steps

### Step 1: Verify Sub-User Exists
```sql
SELECT * FROM employer_sub_users WHERE email = 'your-subuser@email.com';
```

Check:
- ✅ Record exists
- ✅ `status` = 'active'
- ✅ `employer_id` is set
- ✅ `password` is hashed (starts with $2y$)

### Step 2: Verify Employer Status
```sql
SELECT * FROM employers WHERE id = [employer_id_from_above];
```

Check:
- ✅ `status` = 'approved'

### Step 3: Test Authentication Manually
In `php artisan tinker`:
```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Get sub-user
$subUser = App\Models\EmployerSubUser::where('email', 'subuser@example.com')->first();

// Check if exists
dump($subUser);

// Check password hash
dump($subUser->password);

// Test password
Hash::check('your-password', $subUser->password); // Should return true

// Try authentication
Auth::guard('employer_sub_user')->attempt(['email' => 'subuser@example.com', 'password' => 'your-password']);
```

### Step 4: Enable Detailed Error Logging
In `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Then check `storage/logs/laravel.log` for detailed error messages.

---

## How Sub-Users Are Created

### Via UI (Main Employer Only)
1. Main employer logs in
2. Navigate to `/employer/sub-users`
3. Click "Create Sub-User"
4. Fill form and submit

### Programmatically
```php
use App\Models\EmployerSubUser;
use App\Enums\EmployerSubUserRole;

EmployerSubUser::create([
    'employer_id' => 1, // Your employer ID
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password123', // Will be auto-hashed
    'role' => EmployerSubUserRole::Recruiter->value,
    'status' => 'active',
]);
```

**Important**: The password will be automatically hashed if the model has `'password' => 'hashed'` in the casts.

---

## Middleware Flow for Employer Routes

When a sub-user accesses employer routes:

1. **`employer.user` Middleware** (`EnsureEmployerUser.php`)
   - Checks if authenticated via 'web' guard (main employer) or 'employer_sub_user' guard
   - Sets request attributes:
     - `employer` - The Employer model
     - `employer_user` - The authenticated user (User or EmployerSubUser)
     - `employer_owner` - Boolean (true for main employer, false for sub-users)

2. **`employer.approved` Middleware** (`EnsureEmployerApproved.php`)
   - Checks if employer status is 'approved'
   - Redirects to pending page if not approved

3. **`employer.role` Middleware** (`EnsureEmployerRole.php`)
   - For main employer: Always passes (has all permissions)
   - For sub-users: Checks if their role matches allowed roles
   - Example: `employer.role:admin,recruiter` allows admin and recruiter roles only

---

## Testing Sub-User Access

### Create Test Sub-User
```bash
php artisan tinker
```
```php
$employer = App\Models\Employer::first(); // Or find by ID

App\Models\EmployerSubUser::create([
    'employer_id' => $employer->id,
    'name' => 'Test Sub-User',
    'email' => 'test-subuser@example.com',
    'password' => 'password',
    'role' => 'recruiter',
    'status' => 'active',
]);
```

### Test Login
1. Go to login page
2. Use sub-user credentials:
   - Email: `test-subuser@example.com`
   - Password: `password`
3. Should redirect to `/employer/dashboard`

---

## Quick Fix Checklist

If sub-user login is failing, check these in order:

- [ ] Run `php artisan migrate` to ensure table exists
- [ ] Verify employer status is 'approved'
- [ ] Verify sub-user status is 'active'
- [ ] Clear cache: `php artisan cache:clear && php artisan config:clear`
- [ ] Check password is hashed in database
- [ ] Try creating a new test sub-user with known password
- [ ] Check error logs in `storage/logs/laravel.log`
- [ ] Verify SESSION_DRIVER in .env is set correctly
- [ ] Test with browser private/incognito window

---

## Related Files

### Core Authentication Files
- `app/Http/Requests/Auth/LoginRequest.php` - Login logic
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Login controller
- `config/auth.php` - Auth configuration

### Middleware Files
- `app/Http/Middleware/EnsureEmployerUser.php` - Employer/sub-user detection
- `app/Http/Middleware/EnsureEmployerApproved.php` - Approval check
- `app/Http/Middleware/EnsureEmployerRole.php` - Role-based access

### Model Files
- `app/Models/EmployerSubUser.php` - Sub-user model
- `app/Models/Employer.php` - Main employer model

### Controller Files
- `app/Http/Controllers/Employer/SubUserController.php` - Sub-user CRUD operations
