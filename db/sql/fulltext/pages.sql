SELECT
    p.content,
    pt.classname
FROM
    conia.pages p
JOIN conia.pagetypes pt
    ON pt.pagetype = p.pagetype
WHERE
    p.deleted IS NULL
    AND p.published = true;
