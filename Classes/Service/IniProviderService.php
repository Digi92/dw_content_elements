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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class IniProviderService
 * @package Denkwerk\DwContentElements\Service
 */
class IniProviderService
{
    /**
     * Returns the configuration from of the provider extensions
     *
     * @return array
     */
    public function loadProvider()
    {
        // Initialize providers array
        $providers = [];

        // Get global extension configurations
        $configurations = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dw_content_elements'];

        // Load custom provider extensions, if is set
        if (isset($configurations['providers']) &&
            count($configurations['providers'])
        ) {
            foreach ($configurations['providers'] as $extKey => $config) {
                if (ExtensionManagementUtility::isLoaded($extKey) &&
                    is_array($config)
                ) {
                    $providers[$extKey] = $this->mergeConfigurations($config);
                }
            }
        } elseif (ExtensionManagementUtility::isLoaded('dw_content_elements_source')) {
            // Use as fallback the provider extension "dw_content_elements_source", if is load
            $providers['dw_content_elements_source'] = $this->mergeConfigurations(
                array(
                    'pluginName' => 'ContentRenderer',
                    'controllerActions' => array('Elements' => 'render'),
                    'namespace' => 'Denkwerk.DwContentElementsSource',
                    'elementsPath' => '/Configuration/Elements'
                )
            );
        }

        return $providers;
    }

    /**
     * Merge extension configuration with provider configuration
     *
     * @param array $config
     * @return array
     */
    protected function mergeConfigurations(array $config)
    {
        // Extension manager configuration: used as default configuration
        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dw_content_elements']);

        if (is_array($config) &&
            is_array($extensionConfiguration)
        ) {
            // Add/Replace extension configuration with provider configuration
            $config = array_replace($extensionConfiguration, $config);
        }

        return $config;
    }
}
