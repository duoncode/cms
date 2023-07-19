UPDATE
    conia.nodes
SET
    deleted = now()
WHERE
    uid = :uid;

UPDATE
    conia.urlpaths
SET
    inactive = now(),
    editor = :editor
WHERE node IN (
    SELECT n.node FROM conia.nodes n WHERE n.uid = :uid
);

