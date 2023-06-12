INSERT INTO conia.loginsessions
    (hash, usr, expires)
SELECT
    :hash, u.usr, (:expires)::timestamptz
FROM conia.users u
WHERE u.uid = :user

ON CONFLICT (usr) DO

UPDATE SET
    expires = (:expires)::timestamptz,
    hash = :hash;
