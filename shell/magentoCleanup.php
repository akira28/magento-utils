<?php

require_once 'cleanCache.php';

/**
 * Reset files and directories permissions and clean all the caches
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Andrea De Pirro <andrea.depirro@yameveo.com>
 * @version     1
 */
class Yameveo_Shell_Cleanup extends Yameveo_Shell_CleanCache
{
    /**
     * Function to set file permissions to 0644 and folder permissions to 0755
     * @param string $dir
     * @param string $dirModes
     * @param string $fileModes
     */
    private function fixPermissions($dir, $dirModes = 0755, $fileModes = 0644, $shModes = 0775)
    {
        echo "Setting all folder permissions to 755" . PHP_EOL;
        echo "Setting all file permissions to 644" . PHP_EOL;
        echo "Setting all sh scripts permissions to 775" . PHP_EOL;
        $d = new RecursiveDirectoryIterator($dir);
        foreach (new RecursiveIteratorIterator($d, 1) as $path) {
            if ($path->isDir()) // directory
                chmod($path, $dirModes);
            elseif (is_file($path) && strpos($path, '.sh')) // sh scripts
                chmod($path, $shModes);
            elseif (is_file($path)) // files
                chmod($path, $fileModes);
        }
    }
   
    /**
     * Run script
     *
     */
    public function run()
    {
        $baseDir = Mage::getBaseDir();
        $this->fixPermissions($baseDir);
        echo "Setting 'mage' permissions to 550" . PHP_EOL;
        chmod($baseDir . DIRECTORY_SEPARATOR . "mage", 0550);
        echo "Setting 'lib/PEAR' permissions to 550" . PHP_EOL;
        chmod($baseDir . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "PEAR", 0550);
        $this->cleanFiles();
    }

}

$shell = new Yameveo_Shell_Cleanup();
$shell->run();