# @author      Andrea De Pirro <andrea.depirro@yameveo.com>

UPDATE 
    catalog_category_entity_int
SET 
    value = 0
WHERE
    attribute_id = (SELECT 
            attribute_id
        FROM
            eav_attribute
        WHERE
            attribute_code = 'is_active') and entity_id = (SELECT DISTINCT
            entity_id
        FROM
            catalog_category_entity_varchar
        WHERE
            attribute_id = (SELECT 
                    attribute_id
                FROM
                    eav_attribute
                WHERE
                    attribute_code = 'url_key' AND entity_type_id = (SELECT * FROM eav_entity_type where entity_type_code = 'catalog_category')) AND value = 'urlkey');