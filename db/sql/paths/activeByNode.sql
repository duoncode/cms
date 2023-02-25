SELECT
    up.path,
    up.locale
FROM
    conia.urlpaths up
WHERE
    up.node = :node
    AND up.inactive IS NULL;
