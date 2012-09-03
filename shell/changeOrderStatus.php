<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../app/Mage.php');
Mage::app('admin');
// cambiar de estado de 10 dias antes
$orders = Mage::getModel('sales/order')
        ->getCollection()
        ->addAttributeToFilter('created_at', array('to' => '16 June 2012', 'date' => true))
        ->addAttributeToFilter('status', 'shipped');
foreach($orders as $order) {
    $order->setStateUnprotected('complete')->setStatus('complete')->save();
}
$cb_dhl_shipments = Mage::getModel('Cb_Dhl/shipment')
        ->getCollection()
        ->addFieldToFilter('created_at', array('to' => '16 June 2012', 'date' => true))
        ->addFieldToFilter('closed', 0);
foreach($cb_dhl_shipments as $shipment) {
    $shipment->setClosed('1')->setClosedAt(now())->save();
}