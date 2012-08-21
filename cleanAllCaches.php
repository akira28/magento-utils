<?php

require_once 'abstract.php';

/**
 * Clean all caches Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Andrea De Pirro <andreadepirro@gmail.com>
 * @version 1
 */
class Cb_Shell_Cleancache extends Mage_Shell_Abstract
{

    var $_env;

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
            foreach (scandir(Mage::getBaseDir('cache')) as $item) {
                if ($item == '.' || $item == '..')
                    continue;
                $this->_rrmdir(Mage::getBaseDir('cache') . DIRECTORY_SEPARATOR . $item);
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

    private function _rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rrmdir($dir . "/" . $object); else
                        unlink($dir . "/" . $object);
                }
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

$shell = new Cb_Shell_Cleancache();
$shell->run();