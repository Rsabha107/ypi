# Quick Setup Guide - Catering & Uniform Roles

## Prerequisites
- Application code deployed
- Database access
- Laravel Tinker access OR MySQL client access

## Step-by-Step Setup

### Step 1: Create the Catering and Uniform Roles in Database

**Option A: Using Laravel Tinker (Recommended)**
```bash
php artisan tinker
```
Then run:
```php
// Create the Catering role
$cateringRole = Spatie\Permission\Models\Role::create(['name' => 'Catering', 'guard_name' => 'web']);

// Create the Uniform role
$uniformRole = Spatie\Permission\Models\Role::create(['name' => 'Uniform', 'guard_name' => 'web']);

// (Optional) Create permissions for Catering
$cateringPermissions = [
    'catering.participant.view',
    'catering.participant.list',
    'catering.participant.details',
    'catering.participant.filter'
];

foreach ($cateringPermissions as $permissionName) {
    $permission = Spatie\Permission\Models\Permission::create([
        'name' => $permissionName,
        'guard_name' => 'web',
        'group_name' => 'Catering'
    ]);
    $cateringRole->givePermissionTo($permission);
}

// (Optional) Create permissions for Uniform
$uniformPermissions = [
    'uniform.participant.view',
    'uniform.participant.list',
    'uniform.participant.details',
    'uniform.participant.filter'
];

foreach ($uniformPermissions as $permissionName) {
    $permission = Spatie\Permission\Models\Permission::create([
        'name' => $permissionName,
        'guard_name' => 'web',
        'group_name' => 'Uniform'
    ]);
    $uniformRole->givePermissionTo($permission);
}

echo "Catering and Uniform roles created successfully!";
exit;
```

**Option B: Using SQL Script**
```bash
mysql -u your_username -p your_database < database/sql/create_catering_role.sql
```

### Step 2: Assign Roles to Users

**Option A: Using Laravel Tinker**
```bash
php artisan tinker
```
Then run:
```php
// Assign Catering role
$cateringUser = App\Models\User::where('email', 'catering@example.com')->first();
$cateringUser->assignRole('Catering');

// Assign Uniform role
$uniformUser = App\Models\User::where('email', 'uniform@example.com')->first();
$uniformUser->assignRole('Uniform');

echo "Roles assigned successfully";
exit;
```

**Option B: Using SQL**
```sql
-- Get role IDs
SET @catering_role_id = (SELECT id FROM roles WHERE name = 'Catering' LIMIT 1);
SET @uniform_role_id = (SELECT id FROM roles WHERE name = 'Uniform' LIMIT 1);

-- Assign Catering role (replace with actual user ID)
SET @catering_user_id = 5;
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (@catering_role_id, 'App\\Models\\User', @catering_user_id);

-- Assign Uniform role (replace with actual user ID)
SET @uniform_user_id = 6;
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (@uniform_role_id, 'App\\Models\\User', @uniform_user_id);
```

### Step 3: Clear Application Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Test Access

#### For Catering Role:
1. **Login as Catering User**
2. **Navigate to**: `https://your-domain.com/ypi/catering/participant`
3. **Test Features**:
   - ✅ Can view participant list
   - ✅ Can filter by event
   - ✅ Can see dietary information (allergies, health issues)
   - ✅ Can export dietary information to Excel
   - ❌ Cannot edit participants

#### For Uniform Role:
1. **Login as Uniform User**
2. **Navigate to**: `https://your-domain.com/ypi/uniform/participant`
3. **Test Features**:
   - ✅ Can view participant list
   - ✅ Can filter by event
   - ✅ Can see uniform sizes (pants, jersey, jacket, shoe)
   - ✅ Can export uniform sizes to Excel
   - ❌ Cannot edit participants

### Step 5: Update Navigation (Optional)

If your layout uses role-based sidebars, update the main layout file:

**File**: `resources/views/layouts/app.blade.php` (or similar)

Add conditions for both roles:
```blade
@hasrole('SuperAdmin')
    @include('ypi.admin.body.sidebar')
@elsehasrole('Customer')
    @include('ypi.customer.body.sidebar')
@elsehasrole('Catering')
    @include('ypi.catering.body.sidebar')
@elsehasrole('Uniform')
    @include('ypi.uniform.body.sidebar')
@endhasrole
```

## Creating New Users for Roles

### Create Catering User
```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Catering Staff',
    'email' => 'catering@yourcompany.com',
    'password' => bcrypt('YourSecurePassword123!'),
    'email_verified_at' => now(),
]);
$user->assignRole('Catering');
echo "Catering user created!";
exit;
```

### Create Uniform User
```bash
php artisan tinker
```

```php
$user = App\Models\User::create([
    'name' => 'Uniform Staff',
    'email' => 'uniform@yourcompany.com',
    'password' => bcrypt('YourSecurePassword123!'),
    'email_verified_at' => now(),
]);
$user->assignRole('Uniform');
echo "Uniform user created!";
exit;
```

## Verification Checklist

### Catering Role:
- [ ] Catering role created in database
- [ ] User assigned Catering role
- [ ] Can access `/ypi/catering/participant`
- [ ] Can view dietary information columns
- [ ] Can export dietary info to Excel
- [ ] Cannot access `/ypi/admin/participant` (403 error)

### Uniform Role:
- [ ] Uniform role created in database
- [ ] User assigned Uniform role
- [ ] Can access `/ypi/uniform/participant`
- [ ] Can view uniform size columns
- [ ] Can export uniform sizes to Excel
- [ ] Cannot access `/ypi/admin/participant` (403 error)

## Troubleshooting

### Issue: 404 Not Found
**Solution**: Clear route cache
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: 403 Forbidden
**Solution**: 
1. Verify role is assigned:
```php
php artisan tinker
$user = App\Models\User::find(YOUR_USER_ID);
echo $user->roles->pluck('name');
exit;
```

2. Clear permission cache:
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

### Issue: Blank Page or Errors
**Solution**: Check logs
```bash
tail -f storage/logs/laravel.log
```

## Support

For issues:
1. Check application logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Review CATERING_ROLE_README.md for detailed documentation
4. Contact development team

---

**Last Updated**: {{ now()->format('Y-m-d') }}
