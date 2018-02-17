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
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
    function __construct()
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
        $extKey = 'dw_content_elements';
        // Load all provider configurations as array
        $providers = $this->iniProviderService->loadProvider();

        if (count($providers) > 0) {
            $typoScript = '[GLOBAL] ';

            foreach ($providers as $provider => $providerConfig) {
                // Generate camelcase version of the provider
                $providerNameCamelCase = $this->getCamelCaseProviderName($provider);

                //Set own optgroup on the ctype select
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

                            //Add element plugin
                            ExtensionManagementUtility::addPlugin(
                                array(
                                    $elementConfig['title'],
                                    lcfirst($key),
                                ),
                                'CType',
                                $provider
                            );

                            //Set element showitem
                            if ((bool)$elementConfig['overWriteShowitem'] === true) {
                                $showItem = trim((string)$elementConfig['fields'], ',');
                            } else {
                                $showItem = 'CType;;4;button;1-1-1, colPos, --palette--;Headline,'
                                    . trim((string)$elementConfig['fields'], ',') . ',
                                    --div--;LLL:EXT:cms/locallang_tca.xlf:pages.tabs.access,
                                    --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.visibility;hiddenonly,
                                    --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.access;access';
                            }
                            $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] = $showItem;
                            $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['tx_dw_content_elements_title'] =
                                (string)$elementConfig['title'];

                            //Add tab extends and if the palette "dwcAdditionalFields" exists add the fields of it
                            $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] .= ',
                                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xml:pages.tabs.extended,
                                --palette--;LLL:EXT:' . $extKey .
                                '/Resources/Private/Language/locallang_db.xlf:palettes.dwcAdditionalFields;dwcAdditionalFields';

                            // Fix for the extension GridElements. GridElements needs in all elements the
                            // fields "tx_gridelements_container,tx_gridelements_columns"
                            if (ExtensionManagementUtility::isLoaded('gridelements')) {
                                $GLOBALS['TCA']['tt_content']['types'][lcfirst($key)]['showitem'] .=
                                    ',tx_gridelements_container,tx_gridelements_columns';
                            }

                            //Set rendering typoScript
                            $typoScript .= "\n
                             tt_content." . lcfirst($key) .
                                " < tt_content.list.20." .
                                strtolower($providerNameCamelCase) . "_" . strtolower($providerConfig['pluginName']) . " \n";

                            foreach ($providerConfig['controllerActions'] as $controller => $actions) {
                                $actionArray = explode(',', $actions);
                                foreach ($actionArray as $index => $action) {
                                    $typoScript .= "tt_content." .
                                        lcfirst($key) . ".switchableControllerActions." .
                                        $controller . "." . ($index + 1) . " = " .
                                        $action . " \n";
                                }
                            }
                        }
                    }
                }
            }

            //Add rendering typoScript
            ExtensionManagementUtility::addTypoScript(
                $extKey,
                'setup',
                $typoScript,
                true
            );
        }
    }

    /**
     * Injects plugin configuration
     * Call this in ext_localconf.php
     *
     * @return void
     */
    public function injectPluginConfiguration()
    {
        // Load all provider configurations as array
        $providers = $this->iniProviderService->loadProvider();

        if (count($providers) > 0) {
            foreach ($providers as $provider => $providerConfig) {

                // Generate camelcase version of the provider
                $providerNameCamelCase = $this->getCamelCaseProviderName($provider);

                // Add extension plugin
                ExtensionUtility::configurePlugin(
                    // unique plugin name
                    $providerConfig['namespace'],
                    $providerConfig['pluginName'],
                    // accessible controller-action-combinations
                    $providerConfig['controllerActions'],
                    // non-cachable controller-action-combinations (they must already be enabled)
                    array()
                );

                // Add content elements to the content elements wizard
                if (isset($providerConfig['addElementsToWizard']) &&
                    (bool)$providerConfig['addElementsToWizard'] === true
                ) {
                    // Add new wizards tab
                    ExtensionManagementUtility::addPageTSConfig(
                        'mod.wizards.newContentElement.wizardItems.' . $providerNameCamelCase . ' {
                            header = ' . $providerConfig['elementWizardTabTitle'] . '
                            show = *
                        }'
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
                                // Fallback icon
                                $iconIdentifier = 'content-textpic';

                                // Registration the content element icon, if set
                                if ($elementConfig['icon']) {
                                    /** @var IconRegistry $iconRegistry */
                                    $iconRegistry = GeneralUtility::makeInstance(
                                        IconRegistry::class
                                    );
                                    $iconIdentifier = 'dwc-' . lcfirst($key);
                                    $iconRegistry->registerIcon(
                                        $iconIdentifier,
                                        BitmapIconProvider::class,
                                        array(
                                            'source' => (string)$elementConfig['icon'],
                                        )
                                    );
                                }

                                // Add the icon to the content element config
                                $icon = 'iconIdentifier = ' . $iconIdentifier;

                                // Set content element wizardItems
                                ExtensionManagementUtility::addPageTSConfig(
                                    'mod.wizards.newContentElement.wizardItems.' .
                                    $providerNameCamelCase . '.elements.' . lcfirst($key) . ' {
                                        ' . $icon . '
                                        title = ' . (string)$elementConfig['title'] . '
                                        description = ' . (string)$elementConfig['description'] . '
                                        tt_content_defValues.CType = ' . lcfirst($key) . '
                                    }'
                                );
                            }
                        }
                    }
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
}
