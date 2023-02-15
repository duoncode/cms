SELECT
    p.content,
    pt.classname
FROM
    conia.pages p
JOIN conia.types t
    ON pt.type = p.type
WHERE
    p.deleted IS NULL
    AND p.published = true;
