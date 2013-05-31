<?php

/**
 * Improved version of Magento Indexer script
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
 * @version     1
 */
require_once 'indexer.php';

class Yameveo_Shell_Indexer extends Mage_Shell_Indexer
{

    /**
     * Returns the actual microtime in seconds
     * @return float seconds 
     */
    protected function chrono()
    {
        list($msec, $sec) = explode(' ', microtime());
        return ((float) $msec + (float) $sec);
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        if ($this->getArg('info')) {
            $processes = $this->_parseIndexerString('all');
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                echo sprintf('%-30s', $process->getIndexerCode());
                echo $process->getIndexer()->getName() . PHP_EOL;
            }
        }
        elseif ($this->getArg('status') || $this->getArg('mode')) {
            if ($this->getArg('status')) {
                $processes = $this->_parseIndexerString($this->getArg('status'));
            }
            else {
                $processes = $this->_parseIndexerString($this->getArg('mode'));
            }
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                $status = 'unknown';
                if ($this->getArg('status')) {
                    switch ($process->getStatus()) {
                        case Mage_Index_Model_Process::STATUS_PENDING:
                            $status = 'Pending';
                            break;
                        case Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX:
                            $status = 'Require Reindex';
                            break;
                        case Mage_Index_Model_Process::STATUS_RUNNING:
                            $status = 'Running';
                            break;
                        default:
                            $status = 'Ready';
                            break;
                    }
                }
                else {
                    switch ($process->getMode()) {
                        case Mage_Index_Model_Process::MODE_REAL_TIME:
                            $status = 'Update on Save';
                            break;
                        case Mage_Index_Model_Process::MODE_MANUAL:
                            $status = 'Manual Update';
                            break;
                    }
                }
                echo sprintf('%-30s ', $process->getIndexer()->getName() . ':') . $status . PHP_EOL;
            }
        }
        elseif ($this->getArg('mode-realtime') || $this->getArg('mode-manual')) {
            if ($this->getArg('mode-realtime')) {
                $mode = Mage_Index_Model_Process::MODE_REAL_TIME;
                $processes = $this->_parseIndexerString($this->getArg('mode-realtime'));
            }
            else {
                $mode = Mage_Index_Model_Process::MODE_MANUAL;
                $processes = $this->_parseIndexerString($this->getArg('mode-manual'));
            }
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                try {
                    $process->setMode($mode)->save();
                    echo $process->getIndexer()->getName() . " index was successfully changed index mode" . PHP_EOL;
                } catch (Mage_Core_Exception $e) {
                    echo $e->getMessage() . "\n";
                } catch (Exception $e) {
                    echo $process->getIndexer()->getName() . " index process unknown error:" . PHP_EOL;
                    echo $e . "\n";
                }
            }
        }
        elseif ($this->getArg('reindex') || $this->getArg('reindexall')) {
            if ($this->getArg('reindex')) {
                $processes = $this->_parseIndexerString($this->getArg('reindex'));
            }
            else {
                $processes = $this->_parseIndexerString('all');
            }
            $totalstart = $this->chrono();
            foreach ($processes as $process) {
                /* @var $process Mage_Index_Model_Process */
                try {

                    echo "Started " . $process->getIndexer()->getName() . " reindexing process"  . PHP_EOL;
                    $start = $this->chrono();
                    $process->reindexEverything();
                    $end = $this->chrono();
                    $chrono = round($end - $start, 3);
                    echo $process->getIndexer()->getName() . " index was rebuilt successfully in $chrono seconds" . PHP_EOL;
                } catch (Mage_Core_Exception $e) {
                    echo $e->getMessage() . "\n";
                } catch (Exception $e) {
                    echo $process->getIndexer()->getName() . " index process unknown error:" . PHP_EOL;
                    echo $e . "\n";
                }
            }
            $totalend = $this->chrono();
            $totalchrono = round($totalend - $totalstart, 3);
            echo PHP_EOL;
            echo "All completed in $totalchrono seconds" . PHP_EOL;
        }
        else {
            echo $this->usageHelp();
        }
    }

}

$shell = new Yameveo_Shell_Indexer();
$shell->run();
