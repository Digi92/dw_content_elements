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
            $providers = array();

            // get configurations from localconf
            $configurations = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements'];

            if (isset($configurations['provider']) && count($configurations['provider'])) {
                foreach ($configurations['provider'] as $extKey => $config) {
                    if(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extKey) && is_array($config)) {
                        $providers[$extKey] = $config;
                    }
                }
            } else if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {
                $providers['dw_content_elements_source'] = array(
                    'pluginName' => 'ContentRenderer',
                    'controllerActions' => array('Elements' => 'render'),
                    'namespace' => 'Denkwerk.DwContentElementsSource'
                );
            }

            foreach ($providers as $provider => $providerConf) {

                // build elements path
                $elementsPath = (isset($providerConfig['elementsPath']) && !empty($providerConf['elementsPath'])) ?
                    $providerConfig['elementsPath'] :
                    '/Configuration/Elements';

    			$contentElements = $path->getAllDirFiles(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($provider) . $elementsPath);

    			//If it is a content element of the extension dw_content_elements
    			if(isset($contentElements[ucfirst($key)])) {

                    $namespaceParts = explode('.', $providerConf['namespace'], 2);
                    $controllerActions = array();

                    foreach ($providerConf['controllerActions'] as $controller => $actions) {
                        $actionsArray = explode(',', $actions);
                        $controllerActions[$controller . '.'] = array();
                        foreach ($actionsArray as $index => $action) {
                            $controllerActions[$controller . '.'][$index + 1] = $action;
                        }
                    }

    				//Set missing configuration
    				$conf[$key] = 'USER';
    				$conf[$key. '.'] = array(
    					'userFunc' => 'TYPO3\CMS\Extbase\Core\Bootstrap->run',
    					'extensionName' => $namespaceParts[1],
    					'pluginName' => $providerConf['pluginName'],
    					'vendorName' => $namespaceParts[0],
    					'switchableControllerActions.' => $controllerActions
    				);

    				//Set the missing rendering configuration in the global template service variable
    				$GLOBALS['TSFE']->tmpl->setup['tt_content.'] = $conf;
    			}
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
