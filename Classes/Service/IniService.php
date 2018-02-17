<?php

namespace Denkwerk\DwContentElements\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Sascha Zander <sascha.zander@denkwerk.com>
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

use Denkwerk\DwContentElements\Utility\Paths;
use Denkwerk\DwContentElements\Utility\TypoScriptParser;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IniService
 * @package Denkwerk\DwContentElements\Service
 */
class IniService
{
    /**
     * @var \Denkwerk\DwContentElements\Utility\TypoScriptParser $tsParser
     */
    private $tsParser;

    /**
     * IniService constructor.
     */
    public function __construct()
    {
        $this->tsParser = GeneralUtility::makeInstance(
            TypoScriptParser::class
        );
    }

    /**
     * Returns the configuration from the file set by configuration file
     *
     * @param string $configFile Relative Path to TYPO3-DocumentRoot (PATH_site)
     * @return array|bool
     */
    public function loadConfig($configFile)
    {
        $result = false;
        $configFileAbsPath = Paths::concat(
            array(
                realpath(PATH_site),
                realpath($configFile),
            )
        );

        if (is_file($configFileAbsPath) === true) {
            $result = $this->tsParser->parseTypoScriptFile($configFileAbsPath);
            $result['configFileAbsPath'] = $configFileAbsPath;
        }

        return $result;
    }

    /**
     * Load the configuration of all content elements and return this as array
     *
     * @param string $provider The name of the provider extension
     * @param array $providerConfig The configuration array of the provider extension
     * @return array
     */
    public function loadAllContentElementsConfig($provider, array $providerConfig)
    {
        // Load all content elements config files
        $contentElements = $this->loadAllContentElementsConfigFiles($provider, $providerConfig);

        // Load the content element configuration for all elements
        if (is_array($contentElements) &&
            empty($contentElements) === false
        ) {
            foreach ($contentElements as $key => $element) {
                // Override the element name with the element configuration
                $contentElements[$key] = $this->loadConfig($element);
            }
        }

        return $contentElements;
    }

    /**
     * Load the configuration files of all content elements and return this as array
     *
     * @param string $provider The name of the provider extension
     * @param array $providerConfig The configuration array of the provider extension
     * @return array
     */
    public function loadAllContentElementsConfigFiles($provider, array $providerConfig)
    {
        // Build content element config path
        $elementsPath = '/Configuration/Elements';
        if (isset($providerConfig['elementsPath']) &&
            !empty($providerConfig['elementsPath'])
        ) {
            $elementsPath = $providerConfig['elementsPath'];
        }

        // Get all content element config files
        /** @var Paths $paths */
        $paths = GeneralUtility::makeInstance(Paths::class);
        $contentElements = $paths->getAllDirFiles(
            ExtensionManagementUtility::extPath($provider) . $elementsPath
        );

        return $contentElements;
    }
}
