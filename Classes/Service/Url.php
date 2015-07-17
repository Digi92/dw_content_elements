<?php

namespace Denkwerk\DwContentElements\Service;
use \TYPO3\CMS\Core\Utility\GeneralUtility AS GeneralUtility;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2014 André Laugks <andre.laugks@denkwerk.com>, denkwerk GmbH
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
class Url {

	/**
	 * @var integer
	 */
	protected $pageUid;

	/**
	 * @var integer
	 */
	protected $rootPid;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @param null $rootPid
	 * @param null $pageUid
	 */
	public function __construct($rootPid = null, $pageUid = null) {
		//$this->rootPid = $rootPid;
		//$this->pageUid = $pageUid;
	}

	/**
	 * @return int
	 */
	public function getPageUid() {
		return $this->pageUid;
	}

	/**
	* @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}


	/**
	 * @param integer $pageUid
	 * @return $this
	 */
	public function setPageUid($pageUid) {
		$this->pageUid = $pageUid;
		return $this;
	}

	public function getUrl() {
		$this->initTSFE($this->rootPid);
		/** @var $cObj \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer */
		$cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
		return $cObj->typolink_URL(array('parameter' => $this->pageUid));
	}

	private function initTSFE($id = 1, $typeNum = 0) {
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
		if (!is_object($GLOBALS['TT'])) {
			$GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
			$GLOBALS['TT']->start();
		}
		$GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',  $GLOBALS['TYPO3_CONF_VARS'], $id, $typeNum);
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
			$rootline = \TYPO3\CMS\Backend\Utility\BackendUtility::BEgetRootLine($id);
			$host = \TYPO3\CMS\Backend\Utility\BackendUtility::firstDomainRecord($rootline);
			$_SERVER['HTTP_HOST'] = $host;
		}
	}


}
