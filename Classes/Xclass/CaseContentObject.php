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

use Denkwerk\DwContentElements\Service\IniProviderService;
use Denkwerk\DwContentElements\Service\IniService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Contains CASE class object.
 *
 * Class CaseContentObject
 * @package Denkwerk\DwContentElements\Xclass
 */
class CaseContentObject extends AbstractContentObject
{
    /**
     * @var IniService $iniService
     */
    protected $iniService = null;

    /**
     * @var IniProviderService $iniProviderService
     */
    protected $iniProviderService = null;

    /**
     * InjectorService constructor.
     *
     * @param ContentObjectRenderer $cObj
     */
    public function __construct(ContentObjectRenderer $cObj)
    {
        parent::__construct($cObj);
        $this->iniService = GeneralUtility::makeInstance(IniService::class);
        $this->iniProviderService = GeneralUtility::makeInstance(IniProviderService::class);
    }

    /**
     * Rendering the cObject, CASE
     *
     * @param array $conf Array of TypoScript properties
     * @return string Output
     */
    public function render($conf = array())
    {

        if (!empty($conf['if.']) && !$this->cObj->checkIf($conf['if.'])) {
            return '';
        }

        $setCurrent = $this->cObj->stdWrapValue('setCurrent', $conf ?? []);
        if ($setCurrent) {
            $this->cObj->data[$this->cObj->currentValKey] = $setCurrent;
        }
        $key = $this->cObj->stdWrapValue('key', $conf, null);

        /**
         * ============================================HOTFIX BEGIN===================================================
         *
         * Only a hotfix for the bug: Missing rendering configuration for the content elements
         * Die Rendering Definition sollte unter $GLOBALS['TSFE']->tmpl->setup['tt_content.'] stehen.
         * Es kann unter nicht geklärten bedingungen vorkommen das diese Konfiguration nicht im
         * Cache ist oder geladen wird
         *
         * @ToDo: Remove Hotfix or refactor
         */
        if (array_key_exists($key, $conf) === false &&
            empty($key) === false
        ) {
            // Load all provider configurations as array
            $providers = $this->iniProviderService->loadProvider();

            // Load all content elements config files
            if (count($providers) > 0) {
                foreach ($providers as $provider => $providerConfig) {
                    $providerElementsConfigFiles = $this->iniService->loadAllContentElementsConfigFiles(
                        $provider,
                        $providerConfig
                    );

                    //If it is a content element of the extension dw_content_elements
                    if (isset($providerElementsConfigFiles[ucfirst($key)])) {
                        $namespaceParts = explode('.', $providerConfig['namespace'], 2);
                        $controllerActions = array();

                        foreach ($providerConfig['controllerActions'] as $controller => $actions) {
                            $actionsArray = explode(',', $actions);
                            $controllerActions[$controller . '.'] = array();
                            foreach ($actionsArray as $index => $action) {
                                $controllerActions[$controller . '.'][$index + 1] = $action;
                            }
                        }

                        //Set missing configuration
                        $conf[$key] = 'USER';
                        $conf[$key . '.'] = array(
                            'userFunc' => 'TYPO3\CMS\Extbase\Core\Bootstrap->run',
                            'extensionName' => $namespaceParts[1],
                            'pluginName' => $providerConfig['pluginName'],
                            'vendorName' => $namespaceParts[0],
                            'switchableControllerActions.' => $controllerActions,
                        );

                        //Set the missing rendering configuration in the global template service variable
                        $GLOBALS['TSFE']->tmpl->setup['tt_content.'] = $conf;
                    }
                }
            }
        }
        //=============================================HOTFIX ENDE====================================================

        $key = isset($conf[$key]) && (string)$conf[$key] !== '' ? $key : 'default';
        // If no "default" property is available, then an empty string is returned
        if ($key === 'default' && !isset($conf['default'])) {
            $theValue = '';
        } else {
            $theValue = $this->cObj->cObjGetSingle($conf[$key], $conf[$key . '.'] ?? [], $key);
        }
        if (isset($conf['stdWrap.'])) {
            $theValue = $this->cObj->stdWrap($theValue, $conf['stdWrap.']);
        }

        return $theValue;
    }
}
