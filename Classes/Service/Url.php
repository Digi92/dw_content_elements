<?php

namespace Denkwerk\DwContentElements\Service;

use \TYPO3\CMS\Core\Utility\GeneralUtility as GeneralUtility;

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
 * Url
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Url
{

    /**
     * Create link for frontend pages
     *
     * @param int $currentPageUid Pid of the current page
     * @param string $linkParameter The string from the link wizard
     * @return string
     */
    public function getUrl($currentPageUid, $linkParameter)
    {
        $result = $linkParameter;

        // if we load tsfe in backend context in 7.6, we will get an empty require.js file and therefore
        // some js functions in page module like drag'n'drop (gridelements) won't work anymore.
        // for now, we just disable the tsfe initalization
        if(
            VersionNumberUtility::convertVersionNumberToInteger(
                VersionNumberUtility::getNumericTypo3Version()
            )
            <
            VersionNumberUtility::convertVersionNumberToInteger(
                '7.6.0'
            )
        ) {
            // If the TSFE can't load, we can NOT create a typolink
            $currentPage = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord(
                'pages',
                $currentPageUid
            );
            if ($currentPageUid > 0 &&
                $currentPage['doktype'] <= 200 &&
                $currentPage['hidden'] != 1 &&
                empty($linkParameter) === false &&
                $this->initTSFE($currentPageUid)
            ) {
                /** @var $cObj \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer */
                $cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                    'TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer'
                );
                $result = $cObj->typolink_URL(array('parameter' => $linkParameter));
            }
        }

        return $result;
    }

    /**
     * Load the $GLOBALS['TSFE'] for the given page. Is need to created typolinks
     *
     * @param int $id Id of the current page
     * @param int $typeNum
     * @return bool
     */
    private function initTSFE($id = 1, $typeNum = 0)
    {
        $hasTsTemplate = false;
        $rootline = \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($id);

        // Check the rootline pages if there is an sysTemplate with configuration. We need this for initialize the TSFE.
        if (empty($rootline) === false &&
            is_array($rootline)
        ) {
            foreach ($rootline as $page) {
                $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid',
                    'sys_template',
                    'pid=' . (int)$page['uid'] . ' AND deleted=0 AND hidden=0',
                    'sorting',
                    1
                );
                if (isset($row[0])) {
                    $hasTsTemplate = true;
                    break;
                }
            }
        }

        if ($hasTsTemplate) {
            \TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
            if (!is_object($GLOBALS['TT'])) {
                $GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
                $GLOBALS['TT']->start();
            }

            $GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
                'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
                $GLOBALS['TYPO3_CONF_VARS'],
                $id,
                $typeNum
            );
            $GLOBALS['TSFE']->connectToDB();
            $GLOBALS['TSFE']->initFEuser();

            if (TYPO3_MODE === 'BE') {
                $GLOBALS['TSFE']->initializeBackendUser();
            }

            $GLOBALS['TSFE']->determineId();
            $GLOBALS['TSFE']->initTemplate();
            $GLOBALS['TSFE']->getConfigArray();

            if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
                $host = \TYPO3\CMS\Backend\Utility\BackendUtility::firstDomainRecord($rootline);
                $_SERVER['HTTP_HOST'] = $host;
            }
        }

        return $hasTsTemplate;
    }
}
