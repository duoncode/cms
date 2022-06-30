SELECT
    p.uid,
    pt.name AS type,
    pt.classname,
    p.published,
    p.hidden,
    p.locked,
    coalesce(uc.display, coalesce(uc.username, uc.email)) AS creator,
    coalesce(ue.display, coalesce(ue.username, ue.email)) AS editor,
    p.created,
    p.changed,
    u.url,
    p.content
FROM
    conia.urls u
    INNER JOIN conia.pages p USING(page)
    INNER JOIN conia.pagetypes pt USING(pagetype)
    INNER JOIN conia.users uc ON
        uc.usr = p.creator
    INNER JOIN conia.users ue ON
        ue.usr = p.editor
WHERE
    u.url = :url
    AND p.deleted IS NULL;
