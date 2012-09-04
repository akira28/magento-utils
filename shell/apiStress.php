<?php
/**
 * Script to test methods of the Magento API
 * 
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License
 * version 2.1 as published by the Free Software Foundation.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details at
 * http://www.gnu.org/copyleft/lgpl.html
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @author      Andrea De Pirro <andrea.depirro@yameveo.com>
 * @version     1
 */
/**
  customer.list
  customer.create
  customer.info
  customer.update
  customer.delete
  customer_group.list
  customer_address.list
  customer_address.create
  customer_address.info
  customer_address.update
  customer_address.delete
  sales_order.list
  sales_order.info
  directory_country.list
  directory_region.list
  catalog_product_attribute_media.list
  catalog_product_link.list
  catalog_product_attribute.options
  cart.create
  cart_product.remove
  cart_product.add
  cart_product.list
  cart.info
  cart.totals
  cart.order
  cart.license
  cart_product.update
  cart_customer.set
  cart_customer.addresses
  cart_shipping.method
  cart_shipping.list
  cart_payment.method
  cart_payment.list
  cart_coupon.add
 */
$method = $_GET['method'];
$url = $_SERVER['SERVER_NAME'];
$proxy = new SoapClient("http://$url/api/soap/?wsdl");
$sessionId = $proxy->login('apiuser', 'apipassword');
$arrAddresses = array(
            array(
                "mode" => "shipping",
                "firstname" => "testFirstname",
                "lastname" => "testLastname",
                "company" => "testCompany",
                "street" => "testStreet",
                "city" => "Barcelona",
                "region" => "Barcelona",
                "postcode" => "08005",
                "country_id" => "ES",
                "telephone" => "0123456789",
                "fax" => "0123456789",
                "is_default_shipping" => 0,
                "is_default_billing" => 0
            ),
            array(
                "mode" => "billing",
                "firstname" => "testFirstname",
                "lastname" => "testLastname",
                "company" => "testCompany",
                "street" => "testStreet",
                "city" => "Barcelona",
                "region" => "Barcelona",
                "postcode" => "08005",
                "country_id" => "ES",
                "telephone" => "0123456789",
                "fax" => "0123456789",
                "is_default_shipping" => 0,
                "is_default_billing" => 0
            ),
        );
$customerAsGuest = array(
            "firstname" => "testFirstname",
            "lastname" => "testLastName",
            "email" => "testmail@example.org",
            "gender" => "male",
            "dob" => "10/10/80",
            "website_id" => "1",
            "store_id" => "1",
            "mode" => "guest"
        );
$productId = '9709';
//$time_start = microtime(true);
switch ($method) {
    case 'cartcreate': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        var_dump($shoppingCartId);
        break;
    case 'customergrouplist': // OK
        $groupList = $proxy->call($sessionId, 'customer_group.list');
        var_dump($groupList);
        break;
    case 'cartpaymentlist': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $paymentList = $proxy->call($sessionId, "cart_payment.list", array($shoppingCartId));
        var_dump($paymentList);
        break;
    case 'cartpaymentmethod': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        
        $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        $shippingMethod = 'flatrate_flatrate';
        $resultShippingMethod = $proxy->call($sessionId, "cart_shipping.method", array($shoppingCartId, $shippingMethod));
        $paymentMethod = array(
            "method" => "sermepa",
            "cc_type" => "VI",
            "cc_owner" => "Andrea Pallo",
            "cc_number" => "4548812049400004",
            "cc_exp_year" => "2012",
            "cc_exp_month" => "12",
            "cc_cid" => "123",
        );
        $resultPaymentMethod = $proxy->call($sessionId, "cart_payment.method", array($shoppingCartId, $paymentMethod));
        var_dump($resultPaymentMethod);
        break;
    case 'cartshippinglist': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        $resultShippingMethods = $proxy->call($sessionId, "cart_shipping.list", array($shoppingCartId, 1));
        var_dump($resultShippingMethods);
        break;
    case 'cartshippingmethod': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        
        $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        $shippingMethod = 'flatrate_flatrate';
        $resultShippingMethod = $proxy->call($sessionId, "cart_shipping.method", array($shoppingCartId, $shippingMethod));
        var_dump($resultShippingMethod);
        break;
    case 'cartcouponadd': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        
        $resultCustomerSet = $proxy->call($sessionId, 'cart_customer.set', array($shoppingCartId, $customerAsGuest));
        
        $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        $shippingMethod = 'flatrate_flatrate';
        $resultShippingMethod = $proxy->call($sessionId, "cart_shipping.method", array($shoppingCartId, $shippingMethod));
        $resultCartCouponAdd = $proxy->call($sessionId, "cart_coupon.add", array($shoppingCartId, 'COUPON'));
        var_dump($resultCartCouponAdd);
        break;
    case 'cartorder': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        
        $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        $resultShippingMethods = $proxy->call($sessionId, "cart_shipping.list", array($shoppingCartId, 1));
        $shippingMethod = 'flatrate_flatrate';
        $resultShippingMethod = $proxy->call($sessionId, "cart_shipping.method", array($shoppingCartId, $shippingMethod));
        $paymentMethod = array(
            "method" => "sermepa",
            "cc_type" => "VI",
            "cc_owner" => "Andrea Pallo",
            "cc_number" => "4548812049400004",
            "cc_exp_year" => "2012",
            "cc_exp_month" => "12",
            "cc_cid" => "123",
        );
        $shoppingCartLicenses = $proxy->call($sessionId, "cart.license", array($shoppingCartId));

        $licenseForOrderCreation = null;
        if (count($shoppingCartLicenses)) {
            $licenseForOrderCreation = array();
            foreach ($shoppingCartLicenses as $license) {
                $licenseForOrderCreation[] = $license['agreement_id'];
            }
        }
        $resultOrder = $proxy->call($sessionId, "cart.order", array($shoppingCartId, null, $licenseForOrderCreation, $paymentMethod));

        var_dump($resultOrder);
        break;
    case 'cartcustomeraddresses': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        
        $resultCustomerAddresses = $proxy->call($sessionId, "cart_customer.addresses", array($shoppingCartId, $arrAddresses));
        var_dump($resultCustomerAddresses);
        break;
    case 'cartcustomerset': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $resultCustomerSet = $proxy->call($sessionId, 'cart_customer.set', array($shoppingCartId, $customerAsGuest));
        var_dump($resultCustomerSet);
        break;
    case 'cartlicense': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $shoppingCartLicenses = $proxy->call($sessionId, "cart.license", array($shoppingCartId));
        var_dump($shoppingCartLicenses);
        break;
    case 'cartinfo': //OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $shoppingCartInfo = $proxy->call($sessionId, 'cart.info', array($shoppingCartId));
        var_dump($shoppingCartInfo);
        break;
    case 'customerinfo': // OK
        $customerInfo = $proxy->call($sessionId, 'customer.info', array('19940'));
        var_dump($customerInfo);
        break;
    case 'customeraddresslist': // OK
        $customerInfo = $proxy->call($sessionId, 'customer_address.list', array('19940'));
        var_dump($customerInfo);
        break;
    case 'customeraddresscreate': // OK
        $rand = rand(1, 10000);
        $customerId = $proxy->call($sessionId, 'customer.create', array(array(
            'email' => "customer-mail$rand@example.org", 
            'firstname' => "Dough$rand", 
            'lastname' => "Deeks$rand", 
            'password' => "password$rand", 
            'website_id' => 1, 
            'store_id' => 1, 
            'group_id' => 1)));
        $customerAddressResult = $proxy->call($sessionId, 'customer_address.create', array('19940', array('firstname' => 'John', 'lastname' => 'Doe', 'street' => array('Street line 1', 'Streer line 2'), 'city' => 'Weaverville', 'country_id' => 'US', 'region' => 'Texas', 'region_id' => 3, 'postcode' => '96093', 'telephone' => '530-623-2513', 'is_default_billing' => FALSE, 'is_default_shipping' => FALSE)));
        var_dump($customerAddressResult);
        break;
    case 'customeraddressinfo': // OK
        $customerInfo = $proxy->call($sessionId, 'customer_address.info', array('200'));
        var_dump($customerInfo);
        break;
    case 'customeraddressupdate': // OK
        $customerAddressResult = $proxy->call($sessionId, 'customer_address.update', array('200', array('firstname' => 'John', 'lastname' => 'Doe', 'street' => array('Street line 1', 'Streer line 2'), 'city' => 'Weaverville', 'country_id' => 'US', 'region' => 'Texas', 'region_id' => 3, 'postcode' => '96093', 'telephone' => '530-623-2513', 'is_default_billing' => FALSE, 'is_default_shipping' => FALSE)));
        var_dump($customerAddressResult);
        break;
    case 'customeraddressdelete': // OK
        
        $rand = rand(1, 10000);
        $customerId = $proxy->call($sessionId, 'customer.create', array(array(
            'email' => "customer-mail$rand@example.org", 
            'firstname' => "Dough$rand", 
            'lastname' => "Deeks$rand", 
            'password' => "password$rand", 
            'website_id' => 1, 
            'store_id' => 1, 
            'group_id' => 1)));
        $customerAddressID = $proxy->call($sessionId, 'customer_address.create', array('19940', array('firstname' => 'John', 'lastname' => 'Doe', 'street' => array('Street line 1', 'Streer line 2'), 'city' => 'Weaverville', 'country_id' => 'US', 'region' => 'Texas', 'region_id' => 3, 'postcode' => '96093', 'telephone' => '530-623-2513', 'is_default_billing' => FALSE, 'is_default_shipping' => FALSE)));
        var_dump($customerAddressID);
        $customerAddressResult = $proxy->call($sessionId, 'customer_address.delete', $customerAddressID);
        var_dump($customerAddressResult);
        break;
    case 'customerupdate': // OK
        $rand = rand(1, 10000);
        $customerId = $proxy->call($sessionId, 'customer.update', array('customerId' => '20305', 'customerData' => array(
            'email' => "customer-mail$rand@example.org", 
            'firstname' => "Dough$rand", 
            'lastname' => "Deeks$rand", 
            'password' => "password$rand", 
            'website_id' => 1, 
            'store_id' => 1, 
            'group_id' => 1)));
        var_dump($customerId);
        break;
    case 'customerlist': // OK
        $complexFilter = array('filter' => array(
            'store_id' => 1,
            'website_id' => 1,
            'email' => "b42c081a99b0c3e9b3e4d6cf6b83ccf8@example.com"
        ));
        $customerList = $proxy->call($sessionId, 'customer.list', $complexFilter);
        var_dump($customerList);
        break;
    case 'customercreate': // OK
        $rand = rand(1, 10000);
        $customerId = $proxy->call($sessionId, 'customer.create', array(array(
            'email' => "customer-mail$rand@example.org", 
            'firstname' => "Dough$rand", 
            'lastname' => "Deeks$rand", 
            'password' => "password$rand",
            'website_id' => 1, 
            'store_id' => 1,
            'group_id' => 1), 'source' => 'gino'));
        var_dump($customerId);
        break;
    case 'customerdelete': // OK
        $rand = rand(1, 10000);
        $customerId = $proxy->call($sessionId, 'customer.create', array(array(
            'email' => "customer-maildel$rand@example.org", 
            'firstname' => "Dough$rand", 
            'lastname' => "Deeks$rand", 
            'password' => "password$rand", 
            'website_id' => 1, 
            'store_id' => 1, 
            'group_id' => 1)));
        $deleteResult = $proxy->call($sessionId, 'customer.delete', $customerId);
        var_dump($deleteResult);
        break;
    case 'cartproductadd': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $resultCartProductAdd = $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        var_dump($resultCartProductAdd);
        break;
    case 'cartproductupdate': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        $arrProductsUp = array(
            array(
                "product_id" => $productId,
                "qty" => 3
            ),
        );
        $resultCartProductUpdate = $proxy->call($sessionId, "cart_product.update", array($shoppingCartId, $arrProductsUp));
        var_dump($resultCartProductUpdate);
        break;
    case 'cartproductlist': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        $resultCartProductList = $proxy->call($sessionId, "cart_product.list", array($shoppingCartId));
        var_dump($resultCartProductList);
        break;
    case 'cartproductremove': // OK
        $shoppingCartId = $proxy->call($sessionId, 'cart.create', array('1'));
        $arrProducts = array(
            array(
                "product_id" => $productId,
                "qty" => 1
            )
        );
        $proxy->call($sessionId, "cart_product.add", array($shoppingCartId, $arrProducts));
        $resultCartProductRemove = $proxy->call($sessionId, "cart_product.remove", array($shoppingCartId, $arrProducts));
        var_dump($resultCartProductRemove);
        break;
    case 'directorycountrylist': // OK
        $countryList = $proxy->call($sessionId, 'directory_country.list');
        var_dump($countryList);
        break;
    case 'directoryregionlist': // OK
        $regionList = $proxy->call($sessionId, 'directory_region.list', array('ES'));
        var_dump($regionList);
        break;
    case 'salesorderlist': // OK 
        $filter = array('filter' => array('increment_id' => '400006032'));
        $salesOrderList = $proxy->call($sessionId, 'order.list', $filter);
        var_dump($salesOrderList);
        break;
    case 'salesorderinfo': // OK
        $salesOrderList = $proxy->call($sessionId, 'order.info', '100007501');
        var_dump($salesOrderList);
        break;
    case 'catalogproductattributemedialist': // OK
        $attributeMediaList = $proxy->call($sessionId, 'catalog_product_attribute_media.list', array('2699'));
        var_dump($attributeMediaList);
        break;
    case 'catalogproductattributeoptions': // OK
        $attributeOptions = $proxy->call($sessionId, 'catalog_product_attribute.options', array('size_so_uni'));
        var_dump($attributeOptions);
        break;
    case 'catalogproductlinklist': // OK
        $linkList = $proxy->call($sessionId, 'catalog_product_link.list', array('cross_sell', '7179'));
        var_dump($linkList);
        break;
}
