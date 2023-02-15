DELETE FROM conia.fulltext ft
WHERE
    ft.node NOT IN (
        SELECT
            n.node
        FROM
            conia.nodes n
        WHERE
            n.deleted IS NULL
            AND n.published = true
    );
