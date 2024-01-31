<?php

namespace Denkwerk\DwContentElements\Utility;

/***************************************************************
 *  Copyright notice
 *
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
 ***************************************************************/

use TYPO3\CMS\Core\TypoScript\Parser\TypoScriptParser as CoreTypoScriptParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TypoScriptParser
 * @package Denkwerk\DwContentElements\Utility
 */
class TypoScriptParser
{

    /**
     * Parsing the input TypoScript text piece
     *
     * @param $tsString
     * @return array|bool
     */
    public function parseTypoScript($tsString)
    {
        $result = false;

        if (empty($tsString) === false &&
            is_string($tsString)
        ) {
            /** @var CoreTypoScriptParser $tsParserObject */
            $tsParserObject = GeneralUtility::makeInstance(
                CoreTypoScriptParser::class
            );
            $tsParserObject->parse($tsString);
            $result = $tsParserObject->setup;
        }

        return $result;
    }

    /**
     * Parsing the TypoScript from the given file
     *
     * @param $filePath
     * @return array|bool
     */
    public function parseTypoScriptFile($filePath)
    {
        $result = false;

        if (is_file($filePath) &&
            (
                pathinfo($filePath, PATHINFO_EXTENSION) === 'ts' ||
                pathinfo($filePath, PATHINFO_EXTENSION) === 'typoscript'
            )
        ) {
            $fileContent = file_get_contents($filePath);

            if (empty($fileContent) === false) {
                $result = self::parseTypoScript($fileContent);
            }
        }

        return $result;
    }
}
