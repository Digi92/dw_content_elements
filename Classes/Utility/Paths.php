<?php

namespace Denkwerk\DwContentElements\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 André Laugks <andre.laugks@denkwerk.com>, denkwerk GmbH
 *  (c) 2015 Sascha Zander <sascha.zander@denkwerk.com>
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
 * **************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility as GeneralUtility;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;

/**
 * ToDo: Wie mit Pfaden unter Windows umgehen, c:/, d:/?
 *
 * Class Paths
 * @package Denkwerk\DwContentElements\Utility
 */
class Paths
{

    /**
     * Add a leading slash to the given path
     *
     * @param $path
     * @return string
     */
    private function leadingSlash($path)
    {
        $slash = '/';
        /**
         * Workaround for Windows.
         */
        if (preg_match('/^[a-z]\:/i', $path)) {
            $slash = '';
        }

        return $slash . $path;
    }

    /**
     * Convert folder array to string
     *
     * @param array $list
     * @return string
     */
    public static function convertFolderArrayToString(array $list)
    {
        $list[0] = self::replaceBackSlashToSlash($list[0]);
        $list[1] = self::replaceBackSlashToSlash($list[1]);
        if (strpos($list[1], $list[0]) !== false) {
            $result = $list[1];
        } else {
            $result = implode('/', $list);
        }

        return $result;
    }

    /**
     * Function replace the double backslashes "\\" with a slash "/"
     *
     * @param $path
     * @return mixed
     */
    private static function replaceBackSlashToSlash($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Concat paths to string
     *
     * @param array|string array or string
     * @return string
     */
    public static function concat()
    {
        $paths = array();
        foreach (func_get_args() as $arg) {
            if (is_array($arg) === true) {
                $paths = array_merge($paths, $arg);
            }
            if (is_string($arg) === true) {
                array_push($paths, $arg);
            }
        }

        /** @var Paths $pathsUtility */
        $pathsUtility = GeneralUtility::makeInstance(
            Paths::class
        );

        return $pathsUtility->leadingSlash(
            implode(
                '/',
                GeneralUtility::trimExplode(
                    '/',
                    self::convertFolderArrayToString($paths),
                    true
                )
            )
        );
    }

    /**
     * Return a files recursive as array
     *
     * @param $dir
     * @param array $results
     * @return array
     */
    public static function getAllDirFiles($dir, &$results = array())
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);

            if (is_file($path)) {
                //Set the filename without extension as key
                $results[str_replace(
                    '.' . pathinfo($path, PATHINFO_EXTENSION),
                    '',
                    $value
                )] = $path;
            } elseif (is_dir($path) &&
                !in_array($value, array(".", ".."))
            ) {
                self::getAllDirFiles($path, $results);
            }
        }

        return $results;
    }

    /**
     * Note: This function is a copy from "\TYPO3Fluid\Fluid\View\TemplatePaths->resolveFileInPaths" of version 10.4.30
     * We change the access modifier and remove the fallback for format variable.
     *
     * This function will check in the given paths a file is existing
     *
     * @param array $paths
     * @param string $relativePathAndFilename
     * @param string $format
     * @return string
     * @throws InvalidTemplateResourceException
     */
    public static function resolveFileInPaths(array $paths, $relativePathAndFilename, $format)
    {
        $tried = [];
        // Note about loop: iteration with while + array_pop causes paths to be checked in opposite
        // order, which is intentional. Paths are considered overlays, e.g. adding a path to the
        // array means you want that path checked first.
        while (null !== ($path = array_pop($paths))) {
            $pathAndFilenameWithoutFormat = $path . $relativePathAndFilename;
            $pathAndFilename = $pathAndFilenameWithoutFormat . '.' . $format;
            if (is_file($pathAndFilename)) {
                return $pathAndFilename;
            }
            $tried[] = $pathAndFilename;
            if (is_file($pathAndFilenameWithoutFormat)) {
                return $pathAndFilenameWithoutFormat;
            }
            $tried[] = $pathAndFilenameWithoutFormat;
        }
        throw new InvalidTemplateResourceException(
            'The Fluid template files "' . implode('", "', $tried) . '" could not be loaded.',
            1225709595
        );
    }
}
