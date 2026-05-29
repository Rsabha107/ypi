-- =====================================================
-- YPI Catering and Uniform Roles Setup
-- =====================================================
-- This script creates the Catering and Uniform roles with read-only permissions
-- Catering: for viewing participant dietary information
-- Uniform: for viewing participant uniform sizes
-- 
-- Run this script after deploying the code changes.
-- =====================================================

-- Step 1: Create the Catering role using Spatie Permission
INSERT INTO roles (name, guard_name, created_at, updated_at)
VALUES ('Catering', 'web', NOW(), NOW());

-- Step 1b: Create the Uniform role using Spatie Permission
INSERT INTO roles (name, guard_name, created_at, updated_at)
VALUES ('Uniform', 'web', NOW(), NOW());

-- Step 2: (Optional) Create specific permissions for Catering role
-- If you want granular control, create permissions first:
INSERT INTO permissions (name, guard_name, group_name, created_at, updated_at)
VALUES 
('catering.participant.view', 'web', 'Catering', NOW(), NOW()),
('catering.participant.list', 'web', 'Catering', NOW(), NOW()),
('catering.participant.details', 'web', 'Catering', NOW(), NOW()),
('catering.participant.filter', 'web', 'Catering', NOW(), NOW());

-- Step 2b: Create specific permissions for Uniform role
INSERT INTO permissions (name, guard_name, group_name, created_at, updated_at)
VALUES 
('uniform.participant.view', 'web', 'Uniform', NOW(), NOW()),
('uniform.participant.list', 'web', 'Uniform', NOW(), NOW()),
('uniform.participant.details', 'web', 'Uniform', NOW(), NOW()),
('uniform.participant.filter', 'web', 'Uniform', NOW(), NOW());

-- Step 3: Assign permissions to Catering role
-- Get the role ID first, then assign permissions
SET @catering_role_id = (SELECT id FROM roles WHERE name = 'Catering' LIMIT 1);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, @catering_role_id
FROM permissions p
WHERE p.name IN (
    'catering.participant.view',
    'catering.participant.list',
    'catering.participant.details',
    'catering.participant.filter'
);

-- Step 3b: Assign permissions to Uniform role
SET @uniform_role_id = (SELECT id FROM roles WHERE name = 'Uniform' LIMIT 1);

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, @uniform_role_id
FROM permissions p
WHERE p.name IN (
    'uniform.participant.view',
    'uniform.participant.list',
    'uniform.participant.details',
    'uniform.participant.filter'
);

-- =====================================================
-- Assign Roles to Users
-- =====================================================
-- Replace <user_id> with the actual user ID you want to assign the role to
-- 
-- Example for Catering: 
-- SET @user_id = 5;  -- Replace with actual user ID
-- INSERT INTO model_has_roles (role_id, model_type, model_id)
-- VALUES (@catering_role_id, 'App\\Models\\User', @user_id);
--
-- Example for Uniform: 
-- SET @user_id = 6;  -- Replace with actual user ID
-- INSERT INTO model_has_roles (role_id, model_type, model_id)
-- VALUES (@uniform_role_id, 'App\\Models\\User', @user_id);

-- =====================================================
-- Verification Queries
-- =====================================================
-- Check if roles were created
SELECT * FROM roles WHERE name IN ('Catering', 'Uniform');

-- Check if permissions were created
SELECT * FROM permissions WHERE group_name IN ('Catering', 'Uniform');

-- Check role permissions for Catering
SELECT r.name as role_name, p.name as permission_name
FROM roles r
JOIN role_has_permissions rhp ON r.id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id
WHERE r.name = 'Catering';

-- Check role permissions for Uniform
SELECT r.name as role_name, p.name as permission_name
FROM roles r
JOIN role_has_permissions rhp ON r.id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id
WHERE r.name = 'Uniform';

-- Check users with Catering role
SELECT u.id, u.name, u.email, r.name as role_name
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'Catering' AND mhr.model_type = 'App\\Models\\User';

-- Check users with Uniform role
SELECT u.id, u.name, u.email, r.name as role_name
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE r.name = 'Uniform' AND mhr.model_type = 'App\\Models\\User';
