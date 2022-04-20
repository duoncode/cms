INSERT INTO conia.loginsessions
    (hash, uid, expires)
VALUES
    (:hash, :user, (:expires)::timestamptz)

ON CONFLICT (uid) DO

UPDATE SET
    expires = (:expires)::timestamptz,
    hash = :hash;
