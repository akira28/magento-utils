SELECT DISTINCT
    parent_id
FROM
    catalog_product_super_link
WHERE
    product_id IN (1 , 2, 3);