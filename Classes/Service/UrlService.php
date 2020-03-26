<?php

namespace Denkwerk\DwContentElements\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\VersionNumberUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility as GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;

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
     * @param int $currentPageUid Pid of the current page
     * @param string $linkParameter The string from the link wizard
     * @return string
     */
    public function getUrl($currentPageUid, $linkParameter)
    {
        $result = $linkParameter;

        // If the TSFE can't load, we can NOT create a typolink
        $currentPage = BackendUtility::getRecord(
            'pages',
            $currentPageUid
        );
        if ($currentPageUid > 0 &&
            $currentPage['doktype'] <= 200 &&
            $currentPage['hidden'] != 1 &&
            empty($linkParameter) === false &&
            $this->initTSFE($currentPageUid)
        ) {
            /** @var ContentObjectRenderer $cObj */
            $cObj = GeneralUtility::makeInstance(
                ContentObjectRenderer::class
            );
            $result = $cObj->typolink_URL(array('parameter' => $linkParameter));
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
    public function initTSFE($id = 1, $typeNum = 0)
    {
        $hasTsTemplate = false;
        $rootLine = BackendUtility::BEgetRootLine($id);

        // Check root line pages if there is an sysTemplate with configuration. We need this for initialize the TSFE.
        if (empty($rootLine) === false &&
            is_array($rootLine)
        ) {
            foreach ($rootLine as $page) {
//                $row = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
//                    'uid',
//                    'sys_template',
//                    'pid=' . (int)$page['uid'] . ' AND deleted=0 AND hidden=0',
//                    'sorting',
//                    1
//                );

                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('sys_template');
                $row = $queryBuilder
                    ->select('uid')
                    ->from('sys_template')
                    ->where('pid=' . (int)$page['uid'] . ' AND deleted=0 AND hidden=0')
                    ->groupBy('sorting')
                    ->setMaxResults(1)
                    ->execute()
                    ->fetch();

                if (isset($row[0])) {
                    $hasTsTemplate = true;
                    break;
                }
            }
        }

        if ($hasTsTemplate) {
            EidUtility::initTCA();
            if (!is_object($GLOBALS['TT'])) {
                $GLOBALS['TT'] = new TimeTracker;
                $GLOBALS['TT']->start();
            }

            $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
                TypoScriptFrontendController::class,
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

            if (ExtensionManagementUtility::isLoaded('realurl')) {
                $host = BackendUtility::firstDomainRecord($rootLine);
                $_SERVER['HTTP_HOST'] = $host;
            }
        }

        return $hasTsTemplate;
    }
}
