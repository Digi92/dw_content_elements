<?php
namespace Denkwerk\DwContentElements\Xclass;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * Contains CASE class object.
 *
 * @author Xavier Perseguers <typo3@perseguers.ch>
 * @author Steffen Kamper <steffen@typo3.org>
 */
class CaseContentObject extends \TYPO3\CMS\Frontend\ContentObject\AbstractContentObject {

	/**
	 * Rendering the cObject, CASE
	 *
	 * @param array $conf Array of TypoScript properties
	 * @return string Output
	 */
	public function render($conf = array()) {

		if (!empty($conf['if.']) && !$this->cObj->checkIf($conf['if.'])) {
			return '';
		}

		$setCurrent = isset($conf['setCurrent.']) ? $this->cObj->stdWrap($conf['setCurrent'], $conf['setCurrent.']) : $conf['setCurrent'];
		if ($setCurrent) {
			$this->cObj->data[$this->cObj->currentValKey] = $setCurrent;
		}
		$key = isset($conf['key.']) ? $this->cObj->stdWrap($conf['key'], $conf['key.']) : $conf['key'];

		/**
		 * ============================================HOTFIX BEGIN===================================================
		 *
		 * Only a hotfix for the bug: Missing rendering configuration for the content elements
		 * Die Rendering Definition sollte unter $GLOBALS['TSFE']->tmpl->setup['tt_content.'] stehen.
		 * Es kann unter nicht geklärten bedingungen vorkommen das diese Konfiguration nicht im Cache ist oder geladen wird
		 *
		 * @ToDo: Remove Hotfix or refactor
		 */
		if(array_key_exists($key,$conf) === false && empty($key) === false){

			//Get all config files of the content elements
			$path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
			$contentElements = $path->getAllDirFiles(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements_source') . '/Configuration/Elements');

			//If it is a content element of the extension dw_content_elements
			if(isset($contentElements[ucfirst($key)])) {

				//Set missing configuration
				$conf[$key] = 'USER';
				$conf[$key. '.'] = array(
					'userFunc' => 'TYPO3\CMS\Extbase\Core\Bootstrap->run',
					'extensionName' => 'DwContentElementsSource',
					'pluginName' => 'ContentRenderer',
					'vendorName' => 'Denkwerk',
					'switchableControllerActions.' => array(
						'Elements.' => array(
							1 => 'render'
						)
					)
				);

				//Set the missing rendering configuration in the global template service variable
				$GLOBALS['TSFE']->tmpl->setup['tt_content.'] = $conf;
			}
		}
		//=============================================HOTFIX ENDE====================================================

		$key = strlen($conf[$key]) ? $key : 'default';
		$name = $conf[$key];

		$theValue = $this->cObj->cObjGetSingle($name, $conf[$key . '.'], $key);
		if (isset($conf['stdWrap.'])) {
			$theValue = $this->cObj->stdWrap($theValue, $conf['stdWrap.']);
		}
		return $theValue;
	}

}
