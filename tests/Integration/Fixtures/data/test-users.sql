-- Test users for integration testing
-- Password for all users: 'password'

-- Test superuser
INSERT INTO cms.users (uid, email, full_name, display_name, pwhash, role)
VALUES (
    'test-superuser',
    'superuser@example.com',
    'Test Superuser',
    'Superuser',
    '$argon2id$v=19$m=65536,t=4,p=1$ZGZuVmhYbTlwZ0g0VjNkSg$xVLvB0L8B9Gm6F8aB5vBxQ0L8B9Gm6F8aB5vBxQ0L8B',
    2  -- superuser role
)
ON CONFLICT (email) DO NOTHING;

-- Test admin user
INSERT INTO cms.users (uid, email, full_name, display_name, pwhash, role)
VALUES (
    'test-admin',
    'admin@example.com',
    'Test Admin',
    'Admin',
    '$argon2id$v=19$m=65536,t=4,p=1$ZGZuVmhYbTlwZ0g0VjNkSg$xVLvB0L8B9Gm6F8aB5vBxQ0L8B9Gm6F8aB5vBxQ0L8B',
    3  -- admin role
)
ON CONFLICT (email) DO NOTHING;

-- Test editor user
INSERT INTO cms.users (uid, email, full_name, display_name, pwhash, role)
VALUES (
    'test-editor',
    'editor@example.com',
    'Test Editor',
    'Editor',
    '$argon2id$v=19$m=65536,t=4,p=1$ZGZuVmhYbTlwZ0g0VjNkSg$xVLvB0L8B9Gm6F8aB5vBxQ0L8B9Gm6F8aB5vBxQ0L8B',
    4  -- editor role
)
ON CONFLICT (email) DO NOTHING;
