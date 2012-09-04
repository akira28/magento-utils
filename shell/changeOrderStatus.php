<?php
// Close every order before dateTo
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../app/Mage.php');
Mage::app('admin');
$dateTo = '16 June 2012';
$orders = Mage::getModel('sales/order')
        ->getCollection()
        ->addAttributeToFilter('created_at', array('to' => $dateTo, 'date' => true))
        ->addAttributeToFilter('status', 'shipped');
foreach($orders as $order) {
    $order->setStateUnprotected('complete')->setStatus('complete')->save();
}