<?php

namespace Denkwerk\DwContentElements\Service;

    /* * *************************************************************
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
     * ************************************************************* */

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Ini {

	/**
	 * @var string
	 */
	private $configFileAbsPath;

    /**
     * @var string
     */
    private $configFile;


    /**
     * @var \Denkwerk\DwContentElements\Service\Ini|null
     */
    private static $instance = null;

    /**
     * @return \Denkwerk\DwContentElements\Service\Ini|null
     */
    static public function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Relative Path to TYPO3-DocumentRoot (PATH_site)
     *
     * @param string $configFile
	 * @return $this
	 */
    public function setConfigFile($configFile) {
        $this->configFile = $configFile;
        return $this;
    }

    /**
	 * Get path to the configuration file
	 *
     * @return string
     */
    public function getConfigFile() {
        return $this->configFile;
    }

	/**
	 * Returns the configuration from the file set by configuration file
	 *
	 * @return array|bool
	 */
    public function loadConfig() {
		$result = false;

        $this->configFileAbsPath = \Denkwerk\DwContentElements\Utility\Pathes::concat(array(PATH_site, $this->configFile));

        if(is_file($this->configFileAbsPath) === true) {

			/**
			 * @var \Denkwerk\DwContentElements\Utility\TypoScriptParser $tsParser
			 */
			$tsParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\\DwContentElements\\Utility\\TypoScriptParser');
			$result = $tsParser->parseTypoScriptFile($this->configFileAbsPath);
			$result['configFileAbsPath'] = $this->configFileAbsPath;

        }

        return $result;
    }

}