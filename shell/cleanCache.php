<?php

require_once 'abstract.php';

/**
 * Clean all caches Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Andrea De Pirro <andreadepirro@gmail.com>
 * @version     1
 */
class Yameveo_Shell_CleanCache extends Mage_Shell_Abstract
{

   
     protected function cleanImageCache()
    {
        try {
            echo "Cleaning image cache... ";
            ;
            flush();
            echo Mage::getModel('catalog/product_image')->clearCache();
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

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
            ;
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
            ;
            flush();
            echo Mage::app()->getCacheInstance()->clean() ? "[OK]" : "[ERROR]";
            echo PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }
    }

    protected function cleanPhisicalCache()
    {
        // @todo verify these functions
        try {
            echo "Cleaning physical files:" . PHP_EOL;
            ;
            flush();
            echo "Cache... ";
            $this->_rrmdirContent(Mage::getBaseDir('cache'));
            echo "[OK]" . PHP_EOL;
            echo "Full page cache... ";
            $this->_rrmdirContent(Mage::getBaseDir('var') . '/full_page_cache');
            echo "[OK]" . PHP_EOL;
            echo "Minify cache... ";
            $this->_rrmdirContent(Mage::getBaseDir('var') . '/minifycache');
            echo "[OK]" . PHP_EOL;
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

    /**
     * Run script
     *
     */
    public function run()
    {
        ini_set("display_errors", 1);
        Mage::app('admin')->setUseSessionInUrl(false);
        Mage::getConfig()->init();

        $this->cleanImageCache();
        $this->cleanDataCache();
        $this->cleanStoredCache();
        $this->cleanMergedJSCSS();
        $this->cleanPhisicalCache();

        if (function_exists('accelerator_reset')) {
            $this->cleanAccelerator();
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
        Usage:  php cleanAllCaches.php
USAGE;
    }

}

$shell = new Yameveo_Shell_CleanCache();
$shell->run();