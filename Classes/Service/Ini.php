<?php

namespace Denkwerk\DwContentElements\Service;

    /* * *************************************************************
     *  Copyright notice
     *
     *  (c) 2015 André Laugks <andre.laugks@denkwerk.com>, denkwerk GmbH
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
     * @var
     */
    private $enviroment;

    /**
     * @var \Denkwerk\DwContentElements\Service\Ini|null
     */
    private static $instance = null;

    /**
     * @var \Zend_Config_Ini|null
     */
    private $config = null;

    /**
     * @var
     */
    private $options = array();

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
     * @return null|\Zend_Config_Ini
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @param mixed $enviroment
	 * @return $this
	 */
    public function setEnviroment($enviroment) {
        $this->enviroment = $enviroment;
        return $this;
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
     * @return string
     */
    public function getConfigFile() {
        return $this->configFile;
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->config->get($name, $default);
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->config->get($name);
    }

    /**
     * @return Ini|null
     */
    public function loadConfig() {
        $r = null;
        $this->configFileAbsPath = \Denkwerk\DwContentElements\Utility\Pathes::concat(array(PATH_site, $this->configFile));
        if(is_file($this->configFileAbsPath) === true) {
            $this->config = new \Zend_Config_Ini($this->configFileAbsPath, $this->enviroment, $this->options);
            $r = $this;
        }
        return $r;
    }

    /**
     * @return bool
     */
    public function hasConfigFile() {
        return is_file($this->configFileAbsPath);
    }


}