WITH RECURSIVE nav AS (
   SELECT
       menu,
       array[displayorder] AS tree,
       1 AS level,
       item,
       parent,
       data
   FROM
       conia.menuitems
   WHERE
       parent IS NULL
       AND menu = :menu

   UNION ALL

   SELECT
       m.menu,
       tree || m.displayorder AS tree,
       nav.level + 1 AS level,
       m.item,
       m.parent,
       m.data
   FROM
       conia.menuitems m
   JOIN
           nav ON m.parent = nav.item
)
SELECT
    menu,
    item,
    tree,
    level,
    data
FROM
    nav
ORDER BY
    menu,
    tree,
    item;
