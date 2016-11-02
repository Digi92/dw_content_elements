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
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FileService
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileService
{

    /**
     * Path to the source extension
     *
     * @var string
     */
    protected $sourceExtensionDirectory = '';

    /**
     * Create the source extension from the folder 'Resources/Private/SourceExt/'
     *
     * ToDo: Optimise error handling
     *
     * @return bool
     */
    public function createSourceExt()
    {
        $success = false;

        if (!is_dir(self::getSourceExtensionDirectory())) {
            $success = GeneralUtility::mkdir(self::getSourceExtensionDirectory());
        }

        GeneralUtility::copyDirectory(
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements') .
            'Resources/Private/SourceExt/',
            self::getSourceExtensionDirectory()
        );

        return $success;
    }

    /**
     * Return the Path to the source extension
     *
     * @return string
     */
    public function getSourceExtensionDirectory()
    {
        return $this->sourceExtensionDirectory;
    }

    /**
     * Set the Path to the source extension
     *
     * @param string $sourceExtensionDirectory
     * @return void
     */
    public function setSourceExtensionDirectory($sourceExtensionDirectory)
    {
        $this->sourceExtensionDirectory = $sourceExtensionDirectory;
    }
}
