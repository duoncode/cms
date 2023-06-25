UPDATE
    conia.nodes
SET
    published = :published,
    locked = :locked,
    hidden = :hidden,
    editor = :editor,
    content = :content
WHERE
    uid = :uid;
