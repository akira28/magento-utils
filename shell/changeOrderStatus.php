<?php
/**
 * Close every order before dateTo
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