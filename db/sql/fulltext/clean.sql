DELETE FROM conia.fulltext ft
WHERE
    ft.page NOT IN (
        SELECT
            p.page
        FROM
            conia.pages p
        WHERE
            p.deleted IS NULL
            AND p.published = true
    );
