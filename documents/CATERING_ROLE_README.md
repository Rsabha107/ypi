# Catering Role - Read-Only Access Implementation

## Overview
This implementation provides read-only access to participant information for users with the **Catering** role. Users can view participant details, dietary requirements, and medical information but cannot create, edit, or delete any data.

## Files Created/Modified

### 1. Controller
- **File**: `app/Http/Controllers/Ypi/Catering/CateringController.php`
- **Purpose**: Handles all read-only operations for Catering role
- **Methods**:
  - `index()` - Display participant list page
  - `list()` - AJAX endpoint for participant data
  - `detail($id)` - Display participant detail page
  - `getParticipantDetails($id)` - AJAX endpoint for participant details modal
  - `setFilter()` - Set event filter in session
  - `clearFilter()` - Clear event filter

### 2. Routes
- **File**: `routes/web.php`
- **Middleware**: `auth`, `otp`, `XssSanitizer`, `role:Catering`, `prevent-back-history`, `auth.session`
- **Routes**:
  ```
  GET  /ypi/catering/participant                      - List page
  GET  /ypi/catering/participant/list                 - AJAX list data
  GET  /ypi/catering/participant/detail/{id}          - Detail page
  GET  /ypi/catering/participant/{id}/details         - AJAX detail data
  POST /ypi/catering/participant/filter/set           - Set filter
  POST /ypi/catering/participant/filter/clear         - Clear filter
  ```

### 3. Views
- **File**: `resources/views/ypi/catering/participant/list.blade.php`
- **Purpose**: Main participant list page with modals
- **Features**:
  - Participant table with server-side pagination
  - Event filter offcanvas
  - QID image modal
  - Participant details modal

### 4. Component
- **File**: `resources/views/components/ypi/catering/participant-card.blade.php`
- **Purpose**: Bootstrap table component for participant list
- **Features**:
  - Sortable columns
  - Search functionality
  - Export to CSV/Excel/PDF
  - Column visibility toggles
  - No action buttons (read-only)

### 5. JavaScript
- **File**: `public/assets/js/pages/ypi/catering/participant.js`
- **Purpose**: Handle client-side interactions
- **Features**:
  - QID image preview modal
  - Participant details modal (AJAX)
  - Filter form handling

### 6. Database Setup
- **File**: `database/sql/create_catering_role.sql`
- **Purpose**: SQL script to create Catering role and permissions

## Installation Steps

### 1. Run the SQL Script
Execute the SQL script to create the Catering role:
```sql
-- Option A: Via MySQL client
mysql -u your_username -p your_database < database/sql/create_catering_role.sql

-- Option B: Via Laravel Tinker
php artisan tinker
> DB::unprepared(file_get_contents('database/sql/create_catering_role.sql'));
```

### 2. Assign Role to User
Via Tinker:
```php
php artisan tinker
$user = \App\Models\User::find(YOUR_USER_ID);
$user->assignRole('Catering');
```

Via SQL:
```sql
-- Replace YOUR_USER_ID with actual user ID
SET @role_id = (SELECT id FROM roles WHERE name = 'Catering' LIMIT 1);
SET @user_id = YOUR_USER_ID;
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (@role_id, 'App\\Models\\User', @user_id);
```

### 3. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Usage

### Accessing the System
1. Login with a user that has the **Catering** role
2. Navigate to: `/ypi/catering/participant`
3. You'll see a read-only view of all participants

### Features Available to Catering Role

#### ✅ Can Do:
- View participant list
- Search participants
- Filter participants by event
- View participant details (click on name)
- View QID images (click on QID number)
- Export data to CSV/Excel/PDF
- View dietary requirements (allergies)
- View medical information (health issues)
- View size information (clothing sizes)
- Sort and paginate data

#### ❌ Cannot Do:
- Create new participants
- Edit participant information
- Delete participants
- Change participant status
- Upload documents
- Assign venues or matches
- Access admin settings
- Access other admin features

## Access Control

The role is enforced at multiple levels:

1. **Route Middleware**: `role:Catering`
   - Users without Catering role cannot access routes
   
2. **Controller Logic**: Read-only methods only
   - No create, update, or delete methods
   
3. **View Layer**: No action buttons
   - No edit/delete buttons displayed
   
4. **Database Permissions**: (Optional)
   - Spatie permissions for granular control

## Testing

### Test User Access
1. Create a test user or use existing user
2. Assign Catering role:
   ```php
   $user = User::where('email', 'test@example.com')->first();
   $user->assignRole('Catering');
   ```
3. Login as that user
4. Navigate to `/ypi/catering/participant`
5. Verify:
   - Can see participant list
   - Can view details
   - Cannot see edit/delete buttons
   - Cannot access admin routes

### Verify Security
Try accessing admin routes as Catering user:
- `/ypi/admin/participant` - Should be blocked (403)
- `/ypi/setting/event` - Should be blocked (403)

## Troubleshooting

### Issue: "403 Forbidden" or "Role not found"
**Solution**: 
- Verify role was created: `SELECT * FROM roles WHERE name = 'Catering';`
- Verify user has role: Check `model_has_roles` table
- Clear cache: `php artisan cache:clear`

### Issue: "Route not found"
**Solution**:
- Check routes are registered: `php artisan route:list | grep catering`
- Clear route cache: `php artisan route:clear`

### Issue: View not loading
**Solution**:
- Check view file exists in correct path
- Clear view cache: `php artisan view:clear`
- Check for blade syntax errors in views

### Issue: No data showing in table
**Solution**:
- Check browser console for JavaScript errors
- Verify AJAX endpoint: `/ypi/catering/participant/list`
- Check session filter is not hiding data
- Test directly: Open `/ypi/catering/participant/list` in browser

## Security Considerations

1. **Role-Based Access Control**: Only users with Catering role can access
2. **No Write Operations**: Controller has no create/update/delete methods
3. **Session-Based Filtering**: Filters are stored in session (server-side)
4. **CSRF Protection**: All POST requests require CSRF token
5. **Authorization**: Uses Laravel's middleware for route protection

## Future Enhancements

Potential improvements:
- Add export filters (export only filtered data)
- Add print-friendly view
- Add dietary requirements summary report
- Add venue-based filtering
- Add match-based filtering
- Add date range filtering
- Email dietary requirements to catering vendor

## Support

For issues or questions, contact the development team or refer to:
- Laravel Spatie Permission docs: https://spatie.be/docs/laravel-permission
- Bootstrap Table docs: https://bootstrap-table.com/
- Application documentation
