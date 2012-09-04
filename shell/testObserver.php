<?php
/**
 * Magento Compiler Shell Script
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
 * @category    Mage
 * @package     Mage_Shell
 * @author      Andrea De Pirro <andrea.depirro@yameveo.com>
 */

require_once 'abstract.php';

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
