UPDATE catalog_product_entity_int
SET value = '2'
WHERE attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'status' AND entity_type_id = 4)
AND entity_id IN (1,2,3);