<?php

namespace Denkwerk\DwContentElements\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Marcel Wieser <typo3dev@marcel-wieser.de>, denkwerk
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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class BaseController
 * @package Denkwerk\DwContentElements\Controller
 */
class BaseController extends ActionController
{
    /**
     * ReflectionClass
     *
     * @var \ReflectionClass
     */
    protected $classReflection;

    /**
     * ContentObject
     *
     * @var ContentObjectRenderer
     */
    protected $contentObj;

    /**
     * Data object
     *
     * @var mixed
     */
    protected $data;

    /**
     * IrreService
     *
     * @var \Denkwerk\DwContentElements\Service\IrreService
     * @inject
     */
    protected $irreService;

    /**
     * Initializeview
     *
     * @param ViewInterface $view The initializeview
     * @return void
     * @throws \ReflectionException
     */
    protected function initializeView(ViewInterface $view)
    {
        $this->classReflection = new \ReflectionClass($this);
        $this->contentObj = $this->configurationManager->getContentObject();
        $this->data = $this->contentObj->data;
        $view->assign('data', $this->contentObj->data);
    }

    /**
     * This action will render the content element
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function renderAction()
    {
        /**
         * ToDo: More flexible and cleaner implementation needed.
         */
        $this->view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'typo3conf/ext/' . $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/' . substr($this->classReflection->getShortName(), 0, -10) . '/' .
                ucfirst($this->contentObj->data['CType']) . '.' . $this->request->getFormat()
            )
        );

        if ($this->classReflection->hasMethod($this->contentObj->data['CType'] . 'Action')) {
            $this->forward($this->contentObj->data['CType']);
        }
    }
}
