<?php

require_once 'cleanCache.php';

/**
 * Reset files and directories permissions and clean all the caches
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Andrea De Pirro <andreadepirro@gmail.com>
 * @version     1
 */
class Yameveo_Shell_Cleanup extends Yameveo_Shell_CleanCache
{
    /**
     * Function to set file permissions to 0644 and folder permissions to 0755
     * @param type $dir
     * @param type $dirModes
     * @param type $fileModes
     */
    private function fixPermissions($dir = "./", $dirModes = 0755, $fileModes = 0644)
    {
        echo "Setting all folder permissions to 755" . PHP_EOL;
        echo "Setting all file permissions to 644" . PHP_EOL;
        $d = new RecursiveDirectoryIterator($dir);
        foreach (new RecursiveIteratorIterator($d, 1) as $path) {
            if ($path->isDir())
                chmod($path, $dirModes);
            else if (is_file($path))
                chmod($path, $fileModes);
        }
    }
   
    /**
     * Run script
     *
     */
    public function run()
    {
        $this->fixPermissions(Mage::getBaseDir());
        echo "Setting mage permissions to 550" . PHP_EOL;
        chmod("mage", 0550);
        echo "Setting var, var/.htaccess, app/etc permissions to o+w";
        // @todo verify these
        chmod(Mage::getBaseDir('var'), 0666 );
        chmod(Mage::getBaseDir('var') . "/.htaccess", 0666 );
        chmod(Mage::getBaseDir('app') . "/etc", 0666 );
        
        // @todo change permissions to sh scripts
        
        $this->cleanPhisicalCache();
    }

}


$shell = new Yameveo_Shell_Cleanup();
$shell->run();