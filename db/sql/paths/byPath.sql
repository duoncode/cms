SELECT
    up.node,
    up.path,
    up.locale,
    up.creator,
    up.inactive,
    up.created
FROM
    conia.urlpaths up
WHERE
    up.path = :path;
