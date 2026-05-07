# College Event Login Issue - FIXED

## Problems Identified

### 1. **Wrong Password**
- The password in `admin_insert.php` was set to `"admin123"`
- You were trying to login with `"admin1234"`
- **Mismatch!**

### 2. **Variable Name Bug**
- Line 6: `$hashed = password_hash("admin123", PASSWORD_DEFAULT);`
- Line 9: `VALUES ('admin', '$hashed_password')` ← Variable doesn't exist!
- This bug likely prevented the admin user from being created in the database

## Solution

I've fixed both issues in your files:

### Fixed admin_insert.php
- Changed password from `"admin123"` to `"admin1234"` ✓
- Fixed variable name from `$hashed` to `$hashed_password` ✓
- Added `visit_count` field initialization ✓
- Added code to delete existing admin before inserting (prevents duplicates) ✓

## How to Fix Your Database

### Option 1: Run the Fixed File
1. Upload the fixed files to your web server
2. Navigate to: `http://your-domain/admin_insert.php`
3. You should see: "Admin user created successfully..."
4. Now try logging in with:
   - **Username:** admin
   - **Password:** admin1234

### Option 2: Run SQL Directly
If you have phpMyAdmin or direct database access, run this SQL:

```sql
-- Delete old admin (if exists)
DELETE FROM admins WHERE username = 'admin';

-- Insert new admin with properly hashed password
-- Note: You'll need to generate the hash using PHP password_hash()
INSERT INTO admins (username, password, visit_count) 
VALUES ('admin', '$2y$10$...your_hashed_password_here...', 0);
```

### Option 3: Use the Special Fixed File
I've also created `admin_insert_fixed.php` which:
- Automatically deletes any existing admin user
- Creates a new admin with the correct credentials
- Sets visit_count to 0

## Login Credentials

After running the fix:
- **Username:** admin
- **Password:** admin1234

## Why It Wasn't Working

1. The login system uses `password_verify()` which compares your plain-text input against the hashed password in the database
2. Because of the bug, either:
   - The admin wasn't created at all (variable mismatch), OR
   - The admin was created with a different password ("admin123" instead of "admin1234")

## Verification Steps

1. Run `admin_insert.php` (or `admin_insert_fixed.php`)
2. Check your `admins` table in the database
3. You should see one row with username='admin'
4. Try logging in with admin/admin1234
5. You should be redirected to dashboard.php ✓

---

**All files have been fixed and are ready to use!**
