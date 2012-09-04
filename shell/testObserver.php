<?php

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Yameveo_Shell_Example extends Mage_Shell_Abstract
{

    /**
     * Example observer object
     *
     * @var Yameveo_Example_Model_Observer
     */
    protected $_example;

    /**
     * Get example observer object
     *
     * @return Yameveo_Example_Model_Observer
     */
    protected function _getExample()
    {
        if ($this->_example === null) {
            $this->_example = new Yameveo_Example_Model_Observer();
        }
        return $this->_example;
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        $functions = array('method_1', 'method_2', 'method_3', 'method_4', 'method_5');
        if ($this->getArg('info')) {
            foreach ($functions as $function) {
                echo $function . PHP_EOL;
            }
        }
        elseif ($this->getArg('execute') && in_array($this->getArg('execute'),
                                                     $functions)) {
            return $this->_getExample()->{$this->getArg('execute')}();
        }
        else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php testObserver.php -- [options]

  --execute <function>          Execute given action
  info                          Show allowed functions
  help                          This help

  <function>     php function name

USAGE;
    }

}

$shell = new Yameveo_Shell_Example();
$shell->run();
