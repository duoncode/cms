SELECT
    n.content,
    t.classname
FROM
    conia.nodes n
JOIN conia.types t
    ON t.type = n.type
WHERE
    n.deleted IS NULL
    AND n.published = true;
