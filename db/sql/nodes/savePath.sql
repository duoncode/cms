INSERT INTO conia.urlpaths (
    node,
    path,
    locale
)
SELECT
    :uid,
    type,
    :published,
    :locked,
    :hidden,
    :editor,
    :editor,
    :content
FROM
    conia.types t
WHERE
    t.slug = :type

ON CONFLICT (uid) DO

UPDATE SET
    published = :published,
    locked = :locked,
    hidden = :hidden,
    editor = :editor,
    content = :content
WHERE
    conia.nodes.uid = :uid;
