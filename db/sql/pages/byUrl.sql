SELECT
    *
FROM
    conia.urls u
    INNER JOIN conia.pages p USING(page)
WHERE
    u.url = :url;
