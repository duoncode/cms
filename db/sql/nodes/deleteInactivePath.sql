DELETE FROM conia.urlpaths
WHERE
    path = :path
    AND inactive IS NOT NULL:
