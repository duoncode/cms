DELETE FROM conia.loginsessions
WHERE usr IN (
    SELECT u.usr FROM conia.users WHERE uid = :user
);
