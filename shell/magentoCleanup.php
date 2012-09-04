<?php
/**
 * Reset files and directories permissions and clean all the caches
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

require_once 'cleanCache.php';

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