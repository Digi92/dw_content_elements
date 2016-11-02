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

/**
 * FluidRenderer
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FluidRenderer
{

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext
     */
    protected $controllerContext;

    /**
     * Set controller context
     *
     * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
     * @return $this
     */
    public function setControllerContext(\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext)
    {
        $this->controllerContext = $controllerContext;
        return $this;
    }

    /**
     * Creates a fluid instance with given template-file and controller-settings
     * @param string $template Path below Template-Root-Path (Resources/Private/Templates/$file)
     * @return \TYPO3\CMS\Fluid\View\StandaloneView Fluid-Template-Renderer
     */
    public function getRenderInstance($template)
    {

        /**
         * @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager'
         */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Object\\ObjectManager'
        );

        /**
         * @var $configurationManager \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
         */
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Configuration\ConfigurationManager'
        );

        /**
         * @var $renderer \TYPO3\CMS\Fluid\View\StandaloneView
         */
        $renderer = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $renderer->setControllerContext($this->controllerContext);
        $conf = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        $renderer->setTemplatePathAndFilename(
            \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['templateRootPath']) . $template
        );
        $renderer->setLayoutRootPath(
            \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['layoutRootPath'])
        );
        $renderer->setPartialRootPath(
            \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['partialRootPath'])
        );
        $renderer->assign('foo', 'bar');
        return $renderer;
    }
}
