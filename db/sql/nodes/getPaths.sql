SELECT
    path,
    locale,
    inactive
FROM
    conia.urlpaths
WHERE
    node = :node;
