# @author ryaan-anthony
# https://gist.github.com/ryaan-anthony/6290973
# Usage:
# For 9 categories and 99.999 products, run the code:
# mysql > call build_catalog(10,100000);

delimiter ;;
drop procedure if exists build_catalog;;
create procedure build_catalog(IN categories INT, IN products INT)
begin
SET @category_count = 1;
SET @CATNAMEPREFIX = "Category ";
SET @CATURLKEYPREFIX = "cat-";
SET @CATURLPATHPREFIX = "catpath-";
SET @ROOTCATEGORY = 2;
SET @INCLUDEINMENU = 1;
SET @ISACTIVE = 1;
SELECT @category_entity_type_id := entity_type_id from eav_entity_type where entity_type_code = 'catalog_category';
SELECT @category_attribute_set_id := attribute_set_id from eav_attribute_set where attribute_set_name = 'Default' and entity_type_id = @category_entity_type_id;
SELECT @include_in_menu := attribute_id from eav_attribute where attribute_code = 'include_in_menu' and entity_type_id = @category_entity_type_id;
SELECT @is_active := attribute_id from eav_attribute where attribute_code = 'is_active' and entity_type_id = @category_entity_type_id;
SELECT @category_name_id := attribute_id from eav_attribute where attribute_code = 'name' and entity_type_id = @category_entity_type_id;
SELECT @category_url_key_id := attribute_id from eav_attribute where attribute_code = 'url_key' and entity_type_id = @category_entity_type_id;
SELECT @category_url_path_id := attribute_id from eav_attribute where attribute_code = 'url_path' and entity_type_id = @category_entity_type_id;
WHILE @category_count < categories DO
SELECT @category_entity_id := AUTO_INCREMENT from information_schema.tables where table_name = 'catalog_category_entity';
INSERT INTO catalog_category_entity (entity_type_id,attribute_set_id,parent_id,created_at,updated_at,path,position,level,children_count) VALUES (@category_entity_type_id,@category_attribute_set_id,@ROOTCATEGORY,NOW(),NOW(),concat("1/2/", @category_entity_id),1,2,0);
INSERT INTO catalog_category_entity_int (entity_type_id,attribute_id,entity_id,value) VALUES(@category_entity_type_id,@include_in_menu,@category_entity_id,@INCLUDEINMENU);
INSERT INTO catalog_category_entity_int (entity_type_id,attribute_id,entity_id,value) VALUES(@category_entity_type_id,@is_active,@category_entity_id,@ISACTIVE);
INSERT INTO catalog_category_entity_varchar (entity_type_id,attribute_id,entity_id,value) VALUES(@category_entity_type_id,@category_url_key_id,@category_entity_id,concat(@CATURLKEYPREFIX, @category_count));
INSERT INTO catalog_category_entity_varchar (entity_type_id,attribute_id,entity_id,value) VALUES(@category_entity_type_id,@category_url_path_id,@category_entity_id,concat(@CATURLPATHPREFIX, @category_count));
INSERT INTO catalog_category_entity_varchar (entity_type_id,attribute_id,entity_id,value) VALUES(@category_entity_type_id,@category_name_id,@category_entity_id,concat(@CATNAMEPREFIX, @category_count));
SET @category_count = @category_count + 1;
END WHILE;
 
SET @product_count = 1;
SET @NAMEPREFIX = "Test Product ";
SET @URLKEYPREFIX = "key-";
SET @URLPATHPREFIX = "path-";
SET @SKUPREFIX = "sku-";
SET @VISIBILITY = 4;
SET @STATUS = 1;
SET @TAXCLASS = 2;
SET @MAXPRICE = 100;
SET @MAXWEIGHT = 20;
SET @WEBSITE = 1;
SET @STOREID = 1;
SET @QTY = 999;
SET @DESCRIPTION = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
SET @SHORTDESCRIPTION = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";
SELECT @entity_type_id := entity_type_id from eav_entity_type where entity_type_code = 'catalog_product';
SELECT @attribute_set_id := attribute_set_id from eav_attribute_set where attribute_set_name = 'Default' and entity_type_id = @entity_type_id;
SELECT @visibility_id := attribute_id from eav_attribute where attribute_code = 'visibility' and entity_type_id = @entity_type_id;
SELECT @status_id := attribute_id from eav_attribute where attribute_code = 'status' and entity_type_id = @entity_type_id;
SELECT @taxclass_id := attribute_id from eav_attribute where attribute_code = 'tax_class_id' and entity_type_id = @entity_type_id;
SELECT @description_id := attribute_id from eav_attribute where attribute_code = 'description' and entity_type_id = @entity_type_id;
SELECT @short_description_id := attribute_id from eav_attribute where attribute_code = 'short_description' and entity_type_id = @entity_type_id;
SELECT @price_id := attribute_id from eav_attribute where attribute_code = 'price' and entity_type_id = @entity_type_id;
SELECT @weight_id := attribute_id from eav_attribute where attribute_code = 'weight' and entity_type_id = @entity_type_id;
SELECT @name_id := attribute_id from eav_attribute where attribute_code = 'name' and entity_type_id = @entity_type_id;
SELECT @url_key_id := attribute_id from eav_attribute where attribute_code = 'url_key' and entity_type_id = @entity_type_id;
SELECT @url_path_id := attribute_id from eav_attribute where attribute_code = 'url_path' and entity_type_id = @entity_type_id;
 
WHILE @product_count < products DO
INSERT INTO catalog_product_entity (entity_type_id,attribute_set_id,type_id,sku,created_at,updated_at) VALUES (@entity_type_id,@attribute_set_id,"simple",concat(@SKUPREFIX, @product_count),NOW(),NOW());
SELECT @entity_id := entity_id from catalog_product_entity order by entity_id desc limit 1;
INSERT INTO catalog_product_entity_int (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@visibility_id,@entity_id,@VISIBILITY);
INSERT INTO catalog_product_entity_int (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@status_id,@entity_id,@STATUS);
INSERT INTO catalog_product_entity_int (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@taxclass_id,@entity_id,@TAXCLASS);
INSERT INTO catalog_product_entity_text (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@description_id,@entity_id,@DESCRIPTION);
INSERT INTO catalog_product_entity_text (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@short_description_id,@entity_id,@SHORTDESCRIPTION);
INSERT INTO catalog_product_entity_decimal (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@price_id,@entity_id,ROUND(RAND() * @MAXPRICE,2));
INSERT INTO catalog_product_entity_decimal (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@weight_id,@entity_id,ROUND(RAND() * @MAXWEIGHT,2));
INSERT INTO catalog_product_entity_varchar (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@url_key_id,@entity_id,concat(@URLKEYPREFIX, @product_count));
INSERT INTO catalog_product_entity_varchar (entity_type_id,attribute_id,store_id,entity_id,value) VALUES(@entity_type_id,@url_path_id,@STOREID,@entity_id,concat(@URLPATHPREFIX, @product_count));
INSERT INTO catalog_product_entity_varchar (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@url_path_id,@entity_id,concat(@URLPATHPREFIX, @product_count));
INSERT INTO catalog_product_entity_varchar (entity_type_id,attribute_id,entity_id,value) VALUES(@entity_type_id,@name_id,@entity_id,concat(@NAMEPREFIX, @product_count));
INSERT INTO catalog_category_product (category_id,product_id,position) VALUES((SELECT entity_id FROM catalog_category_entity WHERE parent_id = @ROOTCATEGORY ORDER BY RAND() LIMIT 1),@entity_id,1);
SELECT @last_id := category_id from catalog_category_product order by product_id desc limit 1;
INSERT INTO catalog_category_product_index (category_id,product_id,position,is_parent,visibility) VALUES(@last_id,@entity_id,1,1,4);
INSERT INTO cataloginventory_stock_item (product_id,stock_id,qty,is_in_stock) VALUES (@entity_id,1,@QTY,1);
INSERT INTO cataloginventory_stock_status (product_id,website_id,stock_id,qty,stock_status) VALUES (@entity_id,@WEBSITE,1,@QTY,1);
INSERT INTO catalog_product_website (product_id,website_id) VALUES (@entity_id,@WEBSITE);
SET @product_count = @product_count + 1;
END WHILE;
end
;;
