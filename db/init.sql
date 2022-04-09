INSERT INTO conia.userroles (userrole) VALUES ('system'), ('superuser'), ('admin'), ('editor');

INSERT INTO conia.users (
    uid,
    username,
    email,
    display,
    pwhash,
    userrole,
    creator,
    editor
) VALUES (
    '0000000000000',
    'system',
    'system@conia.dev',
    'System',
    '$2y$13$r30g3d99Nf5r4t6L1eDAa.FcMNazGHpwndT0Ak6Bvfhr7SEhaeepC',
    'system',
    1,
    1
);
