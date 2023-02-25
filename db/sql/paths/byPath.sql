SELECT
    up.node,
    up.path,
    up.locale,
    up.inactive
FROM
    conia.urlpaths up
WHERE
    up.path = :path;
