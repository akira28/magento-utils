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
class Yameveo_Shell_Cleancache extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        ini_set("display_errors", 1);
        Mage::app('admin')->setUseSessionInUrl(false);
        Mage::getConfig()->init();
        $types = Mage::app()->getCacheInstance()->getTypes();

        try {
            echo "Cleaning image cache... ";
            flush();
            echo Mage::getModel('catalog/product_image')->clearCache();
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }

        try {
            echo "Cleaning data cache..." . PHP_EOL;
            flush();
            foreach ($types as $type => $data) {
                echo "Removing $type ... ";
                echo Mage::app()->getCacheInstance()->clean($data["tags"]) ? "[OK]" : "[ERROR]";
                echo PHP_EOL;
            }
            echo PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }

        try {
            echo "Cleaning stored cache... ";
            flush();
            echo Mage::app()->getCacheInstance()->clean() ? "[OK]" : "[ERROR]";
            echo PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }

        try {
            echo "Cleaning merged JS/CSS...";
            flush();
            Mage::getModel('core/design_package')->cleanMergedJsCss();
            Mage::dispatchEvent('clean_media_cache_after');
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }

        try {
            echo "Cleaning physical files...";
            flush();
            $dir = Mage::getBaseDir('cache');
            $items = array_diff(scandir($dir), array('..', '.'));
            foreach ($items as $item) {
                $path = $dir . DIRECTORY_SEPARATOR . $item;
                is_dir($path) ? $this->_rrmdir($path) : unlink($path);
            }
            echo "[OK]" . PHP_EOL . PHP_EOL;
        } catch (Exception $e) {
            die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
        }

        if (function_exists('accelerator_reset')) {
            try {
                echo "Cleaning accelerator...";
                flush();
                accelerator_reset();
                echo "[OK]" . PHP_EOL . PHP_EOL;
            } catch (Exception $e) {
                die("[ERROR:" . $e->getMessage() . "]" . PHP_EOL);
            }
        }
    }

    /**
     * Remove a directory and all elements contained
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

$shell = new Yameveo_Shell_Cleancache();
$shell->run();