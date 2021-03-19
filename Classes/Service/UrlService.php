<?php

namespace Denkwerk\DwContentElements\Service;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 André Laugks <andre.laugks@denkwerk.com>, denkwerk GmbH
 *  (c) 2016 Sascha Zander <sascha.zander@denkwerk.com>, denkwerk GmbH
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
 ************************************************************** */

/**
 * Class UrlService
 * @package Denkwerk\DwContentElements\Service
 */
class UrlService
{
    /**
     * Create link for frontend pages
     *
     * @param string $linkParameter The string from the link wizard
     * @return string
     */
    public function getUrl($linkParameter)
    {
        $result = $linkParameter;

        if (empty($linkParameter) === false) {
            /** @var ContentObjectRenderer $cObj */
            $cObj = GeneralUtility::makeInstance(
                ContentObjectRenderer::class
            );
            $result = $cObj->typolink_URL(array('parameter' => $linkParameter));
        }

        return $result;
    }
}
