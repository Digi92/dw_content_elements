<?php

namespace Denkwerk\DwContentElements\Utility;
use \TYPO3\CMS\Core\Utility\GeneralUtility AS GeneralUtility;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2015 André Laugks <andre.laugks@denkwerk.com>, denkwerk GmbH
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

/**
 * ToDo: Wie mit Pfaden unter Windows umgehen, c:/, d:/?
 *
 * Class Pathes
 * @package Denkwerk\DwContentElements\Utility
 */
class Pathes {

    /**
     *
     * @param $path
     * @return string
     */
    private function leadingSlash($path) {
        $slash = '/';
        /**
         * Workaround for Windows.
         */
        if(preg_match('/^[a-z]\:/i', $path)) {
            $slash = '';
        }
        return $slash . $path;
    }

    /**
	 *
     * @param array $list
     * @return string
     */
    public static function convertFolderArrayToString(array $list) {
        return self::replaceBackSlashToSlash(implode('/', $list));
    }

	/**
	 *
	 * @param $path
	 * @return mixed
	 */
	private static function replaceBackSlashToSlash($path) {
		return str_replace('\\', '/', $path);
	}

    /**
     *
     * @param array|string array or string
     * @return string
     */
    public static function concat() {
        $pathes = array();
        foreach(func_get_args() AS $arg) {
            if(is_array($arg) === true){
                $pathes = array_merge($pathes, $arg);
            }
            if(is_string($arg) === true){
                array_push($pathes, $arg);
            }
        }
        return self::leadingSlash(implode('/', GeneralUtility::trimExplode('/', self::convertFolderArrayToString($pathes), true)));
    }

    /**
     * Return a directory and files recursive as array
     *
     * @param string $dir
     * @return array
     */
    public static function dirToArray($dir) {
        $result = array();

        $cdir = scandir($dir);
        foreach ($cdir as $key => $value)
        {
            if (!in_array($value,array(".","..")))
            {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
                {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                }
                else
                {
                    $result[] = $value;
                }
            }
        }
        return $result;
    }

}