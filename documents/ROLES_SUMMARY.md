# Read-Only Roles Summary - Catering & Uniform

## Overview
Two read-only roles have been implemented for the YPI application:
1. **Catering Role** - View dietary information (food allergies, health issues)
2. **Uniform Role** - View uniform size information (pants, jersey, jacket, shoe sizes)

Both roles provide read-only access to approved participants only.

---

## Catering Role

### Purpose
Provides catering staff with access to dietary requirements and health information for meal planning and food preparation.

### Access URL
`/ypi/catering/participant`

### Features
- ✅ View participant list (approved only)
- ✅ Filter by event
- ✅ View dietary information:
  - Food allergies (Yes/No)
  - Allergy type (Peanuts, Shellfish, etc.)
  - Allergy details (specific information)
  - Health issues (Yes/No)
  - Health issue details
- ✅ Export dietary information to Excel
- ✅ Click participant name for full details modal
- ✅ View QID images
- ❌ Cannot edit, create, or delete participants

### Export File
- **Filename**: `dietary_information_YYYY-MM-DD_HHmmss.xlsx` or `dietary_EventName_YYYY-MM-DD_HHmmss.xlsx`
- **Contains**: Name, QID, DOB, Gender, Event, Venue, Match, Food Allergies, Allergy Details, Health Issues, Health Issue Details

### Files Created
- Controller: `app/Http/Controllers/Ypi/Catering/CateringController.php`
- Export: `app/Exports/CateringDietaryExport.php`
- View: `resources/views/ypi/catering/participant/list.blade.php`
- Component: `resources/views/components/ypi/catering/participant-card.blade.php`
- JavaScript: `public/assets/js/pages/ypi/catering/participant.js`
- Sidebar: `resources/views/ypi/catering/body/sidebar.blade.php`

### Routes
```
GET  /ypi/catering/participant                     - List page
GET  /ypi/catering/participant/list                - AJAX data
GET  /ypi/catering/participant/{id}/details        - AJAX details
POST /ypi/catering/participant/filter/set          - Set event filter
POST /ypi/catering/participant/filter/clear        - Clear filter
GET  /ypi/catering/participant/export/dietary      - Export to Excel
```

---

## Uniform Role

### Purpose
Provides uniform department staff with access to clothing size information for ordering and distribution of uniforms.

### Access URL
`/ypi/uniform/participant`

### Features
- ✅ View participant list (approved only)
- ✅ Filter by event
- ✅ View uniform size information:
  - Pants size
  - Jersey size
  - Jacket size
  - Shoe size
- ✅ Export uniform sizes to Excel
- ✅ Click participant name for full details modal
- ✅ View QID images
- ❌ Cannot edit, create, or delete participants

### Export File
- **Filename**: `uniform_sizes_YYYY-MM-DD_HHmmss.xlsx` or `uniform_EventName_YYYY-MM-DD_HHmmss.xlsx`
- **Contains**: Name, QID, DOB, Gender, Event, Venue, Match, Pants Size, Jersey Size, Jacket Size, Shoe Size

### Files Created
- Controller: `app/Http/Controllers/Ypi/Uniform/UniformController.php`
- Export: `app/Exports/UniformSizesExport.php`
- View: `resources/views/ypi/uniform/participant/list.blade.php`
- Component: `resources/views/components/ypi/uniform/participant-card.blade.php`
- JavaScript: `public/assets/js/pages/ypi/uniform/participant.js`
- Sidebar: `resources/views/ypi/uniform/body/sidebar.blade.php`

### Routes
```
GET  /ypi/uniform/participant                      - List page
GET  /ypi/uniform/participant/list                 - AJAX data
GET  /ypi/uniform/participant/{id}/details         - AJAX details
POST /ypi/uniform/participant/filter/set           - Set event filter
POST /ypi/uniform/participant/filter/clear         - Clear filter
GET  /ypi/uniform/participant/export/sizes         - Export to Excel
```

---

## Quick Setup

### 1. Create Roles via Tinker
```bash
php artisan tinker
```
```php
// Create roles
Spatie\Permission\Models\Role::create(['name' => 'Catering', 'guard_name' => 'web']);
Spatie\Permission\Models\Role::create(['name' => 'Uniform', 'guard_name' => 'web']);
exit;
```

### 2. Assign Roles to Users
```bash
php artisan tinker
```
```php
// Assign Catering
$user = App\Models\User::where('email', 'catering@example.com')->first();
$user->assignRole('Catering');

// Assign Uniform
$user = App\Models\User::where('email', 'uniform@example.com')->first();
$user->assignRole('Uniform');
exit;
```

### 3. Clear Cache
```bash
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Comparison Table

| Feature | Catering Role | Uniform Role |
|---------|---------------|--------------|
| **View Participants** | ✅ | ✅ |
| **Filter by Event** | ✅ | ✅ |
| **View Dietary Info** | ✅ (visible columns) | ❌ (hidden columns) |
| **View Uniform Sizes** | ❌ (hidden columns) | ✅ (visible columns) |
| **Export to Excel** | ✅ Dietary Info | ✅ Uniform Sizes |
| **Export Filename** | `dietary_*` | `uniform_*` |
| **Participant Details Modal** | ✅ | ✅ |
| **QID Image Modal** | ✅ | ✅ |
| **Edit Participants** | ❌ | ❌ |
| **Delete Participants** | ❌ | ❌ |
| **Create Participants** | ❌ | ❌ |
| **Access Admin Routes** | ❌ | ❌ |

---

## Common Features (Both Roles)

### Security
- Route middleware: `role:Catering` or `role:Uniform`
- Shows only **approved participants**
- Read-only access enforced at controller level
- No edit/delete buttons in UI
- Cannot access admin routes (403 error)

### UI Features
- Event filter with session storage
- Search functionality
- Sortable columns
- Server-side pagination
- Bootstrap Table with responsive design
- Participant details modal
- QID image modal
- Visual filter indicator (red dot when filtered)

### Technical Stack
- Laravel Controllers with Eloquent ORM
- Maatwebsite Excel Export
- Bootstrap 5 UI Components
- jQuery AJAX
- Bootstrap Table Plugin
- Spatie Laravel Permission

---

## Database Setup

Run the SQL script:
```bash
mysql -u username -p database < database/sql/create_catering_role.sql
```

Or use Laravel Tinker as shown in Quick Setup section above.

---

## Security Notes

1. **Role-Based Access Control**: Only users with assigned role can access routes
2. **Approved Only**: Both roles only see participants with "Approved" status
3. **No Write Operations**: Controllers have no create/update/delete methods
4. **CSRF Protection**: All POST requests require CSRF token
5. **Session-Based Filtering**: Filters stored server-side in session

---

## Troubleshooting

### 404 Error
```bash
php artisan route:clear
php artisan route:cache
```

### 403 Forbidden
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

### Verify Role Assignment
```bash
php artisan tinker
$user = App\Models\User::find(USER_ID);
echo $user->roles->pluck('name');
exit;
```

---

## Documentation Files

1. `documents/CATERING_ROLE_README.md` - Detailed Catering documentation
2. `documents/CATERING_QUICK_SETUP.md` - Quick setup for both roles
3. `documents/ROLES_SUMMARY.md` - This file
4. `database/sql/create_catering_role.sql` - SQL setup script

---

**Last Updated**: May 24, 2026
