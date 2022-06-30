SELECT
    *
FROM
    conia.urls u
    INNER JOIN conia.pages p USING(page)
    INNER JOIN conia.pagetypes pt USING(pagetype)
WHERE
    u.url = :url;
