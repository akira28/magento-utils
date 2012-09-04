# @author      Andrea De Pirro <andrea.depirro@yameveo.com>

UPDATE `customer_entity` SET `email` = CONCAT ( md5(`email`) ,"@", "example.com");
UPDATE `sales_flat_order` SET `customer_email` = CONCAT ( SUBSTR(md5(`customer_email`),6) ,"@", "example.com") , `customer_firstname` = md5(`customer_firstname`) , `customer_lastname` = md5(`customer_lastname`);
UPDATE `sales_flat_order_address` SET `email` = CONCAT ( SUBSTR(md5(`email`),6) ,"@", "example.com") , `firstname` = md5(`firstname`) , `lastname` = md5(`lastname`);
UPDATE `sales_flat_quote` SET `customer_email` = CONCAT ( SUBSTR(md5(`customer_email`),6) ,"@", "example.com") , `customer_firstname` = md5(`customer_firstname`) , `customer_lastname` = md5(`customer_lastname`);
UPDATE `sales_flat_quote_address` SET `email` = CONCAT ( SUBSTR(md5(`email`),6) ,"@", "example.com") , `firstname` = md5(`firstname`) , `lastname` = md5(`lastname`);