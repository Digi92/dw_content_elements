<?php

namespace Denkwerk\DwContentElements\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Sascha Zander <sascha.zander@denkwerk.com>
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

use Denkwerk\DwContentElements\Service\IniProviderService;
use Denkwerk\DwContentElements\Service\IniService;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Class InjectorService
 * @package Denkwerk\DwContentElements\Service
 */
class InjectorService
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
     */
    public function __construct()
    {
        $this->iniService = GeneralUtility::makeInstance(IniService::class);
        $this->iniProviderService = GeneralUtility::makeInstance(IniProviderService::class);
    }

    /**
     * Injects TCA
     * Call this in Configuration/TCA/Overrides/tt_content.php
     *
     * @return void
     */
    public function injectTca()
    {
        // Load all provider configurations as array
        $providers = $this->iniProviderService->loadProvider();

        if (count($providers) > 0) {
            foreach ($providers as $provider => $providerConfig) {
                // Set own optgroup on the ctype select
                $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = array(
                    0 => $providerConfig['pluginCategory'],
                    1 => '--div--',
                );

                // Load all content elements configurations
                $contentElements = $this->iniService->loadAllContentElementsConfig($provider, $providerConfig);

                // Add all content elements to wizards
                if (is_array($contentElements) &&
                    empty($contentElements) === false
                ) {
                    foreach ($contentElements as $key => $elementConfig) {
                        if (isset($elementConfig['title']) &&
                            isset($elementConfig['fields'])
                        ) {
                            // Add element plugin
                            ExtensionManagementUtility::addPlugin(
                                array(
                                    $elementConfig['title'],
                                    lcfirst($key),
                                ),
                                'CType',
                                $provider
                            );

                            // Set element showitem
                            if (isset($elementConfig['overWriteShowitem']) &&
                                (bool)$elementConfig['overWriteShowitem'] === true) {
                                $showItem = trim((string)$elementConfig['fields'], ',');
                            } else {
                                $showItem = 'sys_language_uid, l10n_parent, l10n_diffsource, l18n_parent,'
                                    . 'l18n_diffsource, CType, colPos, --palette--;Headline,'
                                    . trim((string)$elementConfig['fields'], ',') . ',
                                    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:'
                                    . 'pages.palettes.visibility;hiddenonly,
                                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:' .
                                    'pages.palettes.access;access';
                            }
                            $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] = $showItem;
                            $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['tx_dw_content_elements_title'] =
                                (string)$elementConfig['title'];

                            // Add tab extends and if the palette "dwcAdditionalFields" exists add the fields of it
                            $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] .= ',
                                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xml:'
                                . 'pages.tabs.extended,
                                --palette--;LLL:EXT:dw_content_elements/Resources/Private/Language/locallang_db.xlf:'
                                . 'palettes.dwcAdditionalFields;dwcAdditionalFields';

                            // Fix for the extension GridElements. GridElements needs in all elements the
                            // fields "tx_gridelements_container,tx_gridelements_columns"
                            if (ExtensionManagementUtility::isLoaded('gridelements')) {
                                $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] .=
                                    ',tx_gridelements_container,tx_gridelements_columns';
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Injects plugin configuration
     * Call this in ext_localconf.php
     *
     * @return void
     */
    public function injectTypoScripConfiguration()
    {
        // Load all provider configurations as array
        $providers = $this->iniProviderService->loadProvider();

        if (count($providers) > 0) {
            foreach ($providers as $provider => $providerConfig) {
                // Generate camelcase version of the provider
                $providerNameCamelCase = $this->getCamelCaseProviderName($provider);

                // Load all content elements configurations
                $contentElements = $this->iniService->loadAllContentElementsConfig(
                    $provider,
                    $providerConfig
                );

                // Add rendering typoscript config of the content elements
                $this->addRenderingConfigForElements(
                    $contentElements,
                    $providerNameCamelCase,
                    $providerConfig['controllerActionClass'] ?? ''
                );

                // Add content elements to the content elements wizard
                if (isset($providerConfig['addElementsToWizard']) &&
                    (bool)$providerConfig['addElementsToWizard'] === true
                ) {
                    $this->addElementsToWizard(
                        $contentElements,
                        $providerNameCamelCase,
                        $providerConfig['elementWizardTabTitle']
                    );
                }
            }
        }
    }

    /**
     * Add rendering typoscript config of the content elements
     *
     * @param $contentElements
     * @param string $providerNameCamelCase
     * @param string $controllerActionClass
     * @return void
     */
    public function addRenderingConfigForElements(
        $contentElements,
        string $providerNameCamelCase,
        string $controllerActionClass = ''
    ) {
        $typoScript = '[GLOBAL] ';

        // Add rendering to all content elements
        if (is_array($contentElements) &&
            empty($contentElements) === false
        ) {
            foreach ($contentElements as $key => $elementConfig) {
                if (isset($elementConfig['title']) &&
                    isset($elementConfig['fields'])
                ) {
                    // Set content element rendering typoScript
                    $typoScript .= "\n
                        tt_content {\n
                            " . lcfirst($key) . " =< lib.contentElement\n
                            " . lcfirst($key) . " {\n
                                templateName = " . ucfirst($key) . "\n" .
                                (
                                    trim($controllerActionClass) !== '' ?
                                    "dataProcessing.10 = Denkwerk\DwContentElements\DataProcessing\ContentElementActionProcessor\n
                                    dataProcessing.10 {\n
                                        controllerActionClass = " . $controllerActionClass . "\n
                                    }" :
                                    ""
                                ) . "

                        }\n
                    }";
                }
            }
        }

        //Add rendering typoScript
        ExtensionManagementUtility::addTypoScript(
            $providerNameCamelCase,
            'setup',
            $typoScript,
            43
        );
    }

    /**
     * Add the content elements to the new elements wizard
     *
     * @param array $contentElements
     * @param $providerNameCamelCase
     * @param $wizardTabTitle
     * @return void
     */
    public function addElementsToWizard(array $contentElements, $providerNameCamelCase, $wizardTabTitle)
    {
        // Add new wizards tab
        ExtensionManagementUtility::addPageTSConfig(
            'mod.wizards.newContentElement.wizardItems.' . $providerNameCamelCase . ' {
                header = ' . $wizardTabTitle . '
                show = *
            }'
        );

        // Add all content elements to wizards
        if (is_array($contentElements) &&
            empty($contentElements) === false
        ) {
            foreach ($contentElements as $key => $elementConfig) {
                if (isset($elementConfig['title']) &&
                    isset($elementConfig['fields'])
                ) {
                    // Fallback icon
                    $iconIdentifier = 'content-textpic';

                    // Registration the content element icon, if set
                    if (isset($elementConfig['icon']) &&
                        $elementConfig['icon'] !== ''
                    ) {
                        $iconIdentifier = 'dwc-' . lcfirst($key);
                    }

                    // Add the icon to the content element config
                    $icon = 'iconIdentifier = ' . $iconIdentifier;

                    // Set content element wizardItems
                    ExtensionManagementUtility::addPageTSConfig(
                        'mod.wizards.newContentElement.wizardItems.' .
                        $providerNameCamelCase . '.elements.' . lcfirst($key) . ' {
                                ' . $icon . '
                                title = ' . (string)$elementConfig['title'] . '
                                description = ' . (isset($elementConfig['description']) ?
                                    (string)$elementConfig['description'] : '') . '
                                tt_content_defValues.CType = ' . lcfirst($key) . '
                            }'
                    );
                }
            }
        }
    }

    /**
     * Generate camelcase version of the provider name
     *
     * @param string $providerName
     * @return null|string
     */
    private function getCamelCaseProviderName($providerName)
    {
        return preg_replace_callback(
            '/_([a-z])/',
            function ($c) {
                return strtoupper($c[1]);
            },
            $providerName
        );
    }

    /**
     * Creates the array with all content element icons for the icon registration in icon.php
     *
     * @return array
     */
    public function generateIconConfig(): array
    {
        $icons = [];

        // Load all provider configurations as array
        $providers = $this->iniProviderService->loadProvider();

        if (count($providers) > 0) {
            foreach ($providers as $provider => $providerConfig) {

                // Add content elements to the content elements wizard
                if (isset($providerConfig['addElementsToWizard']) &&
                    (bool)$providerConfig['addElementsToWizard'] === true
                ) {

                    // Generate camelcase version of the provider
                    $providerNameCamelCase = $this->getCamelCaseProviderName($provider);

                    // Load all content elements configurations
                    $contentElements = $this->iniService->loadAllContentElementsConfig(
                        $provider,
                        $providerConfig
                    );

                    foreach ($contentElements as $key => $elementConfig) {
                        // Registration the content element icon, if set
                        if (isset($elementConfig['icon']) &&
                            $elementConfig['icon'] !== ''
                        ) {
                            $iconProvider = BitmapIconProvider::class;
                            $filePath = $elementConfig['icon'];

                            // If file not exists we need to set a default
                            $realFilePath = GeneralUtility::getFileAbsFileName($elementConfig['icon']);
                            if ($realFilePath === '' ||
                                file_exists($realFilePath) === false
                            ) {
                                $filePath = 'EXT:dw_content_elements/Resources/Public/Icons/content-element-fallback.svg';
                            }

                            // Change icon provider to SVG on SVG icons
                            if (PathUtility::pathinfo($filePath, PATHINFO_EXTENSION) === 'svg') {
                                $iconProvider = SvgIconProvider::class;
                            }

                            $icons['dwc-' . lcfirst($key)] = [
                                'provider' => $iconProvider,
                                'source' => $filePath
                            ];
                        }
                    }
                }
            }
        }

        return $icons;
    }
}
