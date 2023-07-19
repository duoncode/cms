SELECT
    path,
    locale,
    creator,
    editor,
    created,
    inactive
FROM
    conia.urlpaths
WHERE
    node = :node
    AND inactive IS NULL;
