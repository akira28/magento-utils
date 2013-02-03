<?php
/**
 * Clean all caches Shell Script
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

require_once 'abstract.php';

class Yameveo_Shell_CleanCache extends Mage_Shell_Abstract
{

    /**
     * Cleans image cache using catalog/product_image model.
     *
     */
    protected function cleanImageCache()
    {
        try {
            echo "Cleaning image cache... ";
            flush();
            echo Mage::getModel('catalog/product_image')->clearCache();
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    /**
     * Cleans magento data cache:
     * - config,
     * - layout,
     * - block_html
     * - translate,
     * - collections,
     * - eav,
     * - config_api
     */
    protected function cleanDataCache()
    {
        try {
            echo "Cleaning data cache:" . PHP_EOL;
            flush();
            $types = Mage::app()->getCacheInstance()->getTypes();
            foreach ($types as $type => $data) {
                echo "Removing $type ... ";
                echo Mage::app()->getCacheInstance()->clean($data["tags"]) ? "[OK]" : "[ERROR]";
                echo PHP_EOL;
            }
            echo PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    protected function cleanMergedJSCSS()
    {
        try {
            echo "Cleaning merged JS/CSS... ";
            flush();
            Mage::getModel('core/design_package')->cleanMergedJsCss();
            Mage::dispatchEvent('clean_media_cache_after');
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    protected function cleanStoredCache()
    {
        try {
            echo "Cleaning stored cache... ";
            flush();
            echo Mage::app()->getCacheInstance()->clean() ? "[OK]" : "[ERROR]";
            echo PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    /**
     * Does a rmdir on:
     * - cache,
     * - var/full_page_cache
     * - var/minifycache
     * - session dir
     *
     * @todo verify these functions
     * @todo check var dir for any other cleanable subdirs.
     */
    protected function cleanFiles()
    {
        try {
            echo "Cleaning files:" . PHP_EOL;
            flush();
            echo "Cache... ";
            $this->_rrmdirContent(Mage::getBaseDir('cache'));
            echo "[OK]" . PHP_EOL;
            $full_page_dir = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'full_page_cache';
            if(is_dir($full_page_dir)) {
                echo "Full page cache... ";
                $this->_rrmdirContent($full_page_dir);
                echo "[OK]" . PHP_EOL;
            }
            $minify_dir = Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . 'minifycache';
            if(is_dir($minify_dir)) {
                echo "Minify cache... ";
                $this->_rrmdirContent($minify_dir);
                echo "[OK]" . PHP_EOL;
            }
            echo "Session... ";
            $this->_rrmdirContent(Mage::getBaseDir('session'));
            echo "[OK]" . PHP_EOL;
            echo PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    protected function cleanAccelerator()
    {
        try {
            echo "Cleaning accelerator... ";
            flush();
            accelerator_reset();
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    protected function cleanAll()
    {
        $this->cleanImageCache();
        $this->cleanDataCache();
        $this->cleanStoredCache();
        $this->cleanMergedJSCSS();
        $this->cleanFiles();
        if (function_exists('accelerator_reset')) {
            $this->cleanAccelerator();
        }
    }

    /**
     * Run script
     *
     */
    public function run()
    {
        ini_set("display_errors", 1);
        Mage::app('admin')->setUseSessionInUrl(false);
        Mage::getConfig()->init();
        $caches = array('image', 'data', 'stored', 'js_css', 'files');
        if ($this->getArg('info')) {
            echo 'Allowed caches: ' . PHP_EOL;
            foreach ($caches as $cache) {
                echo "\t" . $cache . PHP_EOL;
            }
            die();
        }

        if ($this->getArg('all')) {
            $this->cleanAll();
            die();
        }

        if ($this->getArg('clean') && in_array($this->getArg('clean'), $caches)) {
            switch ($this->getArg('clean')) {
                case 'image':
                    $this->cleanImageCache();
                    break;
                case 'data':
                    $this->cleanDataCache();
                    break;
                case 'stored':
                    $this->cleanStoredCache();
                    break;
                case 'js_css':
                    $this->cleanMergedJSCSS();
                    break;
                case 'files':
                    $this->cleanFiles();
                    break;
            }
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Removes all elements contained in the given directory
     * @param string $dir directory containing elements to remove
     */
    private function _rrmdirContent($dir)
    {
        $items = array_diff(scandir($dir), array('..', '.'));
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->_rrmdir($path) : unlink($path);
        }
    }

    /**
     * Removes a directory and all elements contained
     * @param string $dir directory to remove
     */
    private function _rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = array_diff(scandir($dir), array('..', '.'));
            foreach ($objects as $object) {
                $path = $dir . DIRECTORY_SEPARATOR . $object;
                is_dir($path) ? $this->_rrmdir($path) : unlink($path);
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php cleanCaches.php -- [options]

    --clean <cache>          Clean <cache>. Any of [image|data|stored|js_css|files]
    all                      Clean all caches
    info                     Show allowed caches
    help                     This help


USAGE;
    }

}

$shell = new Yameveo_Shell_CleanCache();
$shell->run();
