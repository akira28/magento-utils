# @author      Andrea De Pirro <andrea.depirro@yameveo.com>

UPDATE `customer_entity`
SET    `email` = CONCAT (MD5(`email`), "@", "example.com");

UPDATE `sales_flat_order`
SET    `customer_email` = CONCAT (SUBSTR(MD5(`customer_email`), 6), "@",
                          "example.com")
       ,
       `customer_firstname` = MD5(`customer_firstname`),
       `customer_lastname` = MD5(`customer_lastname`);

UPDATE `sales_flat_order_address`
SET    `email` = CONCAT (SUBSTR(MD5(`email`), 6), "@", "example.com"),
       `firstname` = MD5(`firstname`),
       `lastname` = MD5(`lastname`);

UPDATE `sales_flat_quote`
SET    `customer_email` = CONCAT (SUBSTR(MD5(`customer_email`), 6), "@",
                          "example.com")
       ,
       `customer_firstname` = MD5(`customer_firstname`),
       `customer_lastname` = MD5(`customer_lastname`);

UPDATE `sales_flat_quote_address`
SET    `email` = CONCAT (SUBSTR(MD5(`email`), 6), "@", "example.com"),
       `firstname` = MD5(`firstname`),
       `lastname` = MD5(`lastname`);  `firstname` = md5(`firstname`) , `lastname` = md5(`lastname`);