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
use Psr\Http\Message\ResponseInterface;
use Denkwerk\DwContentElements\Service\IrreService;
use Denkwerk\DwContentElements\Utility\Paths;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException;
use TYPO3Fluid\Fluid\View\TemplatePaths;

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
     * @var IrreService
     */
    protected $irreService;

    /**
     * @param IrreService $irreService
     */
    public function __construct(IrreService $irreService)
    {
        $this->irreService = $irreService;
    }

    /**
     * Initializeview
     *
     * @return void
     * @throws \ReflectionException
     */
    protected function initializeView($view)
    {
        $this->classReflection = new \ReflectionClass($this);
        // @extensionScannerIgnoreLine
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
    public function renderAction(): ResponseInterface
    {
        // Try to load the action template path
        try {
            /** @var TemplatePaths $templatePathsContext */
            $templatePathsContext = $this->view->getTemplatePaths();

            $actionFilePath = Paths::resolveFileInPaths(
                $templatePathsContext->getTemplateRootPaths(),
                substr($this->classReflection->getShortName(), 0, -10) . '/' .
                ucfirst($this->contentObj->data['CType']),
                $templatePathsContext->getFormat()
            );
        } catch (InvalidTemplateResourceException $error) {
            // Fallback try to load action template path from the templates folder of the extension
            $actionFilePath = GeneralUtility::getFileAbsFileName(
                'typo3conf/ext/' . $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/' . substr($this->classReflection->getShortName(), 0, -10) . '/' .
                ucfirst($this->contentObj->data['CType']) . '.' . $this->request->getFormat()
            );

            if (!is_file($actionFilePath)) {
                throw new InvalidTemplateResourceException(
                    $error->getMessage() . ' Also not found in fallback location "$actionFilePath"',
                    1225709595
                );
            }
        }

        // Set the action template path
        $this->view->setTemplatePathAndFilename($actionFilePath);

        if ($this->classReflection->hasMethod($this->contentObj->data['CType'] . 'Action')) {
            $this->forward($this->contentObj->data['CType']);
        }
        return $this->htmlResponse();
    }

    /**
     * This action will render the content element without cache
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function nonCacheableRenderAction(): ResponseInterface
    {
        $this->renderAction();
        return $this->htmlResponse();
    }

    public function injectIrreService(IrreService $irreService): void
    {
        $this->irreService = $irreService;
    }
}
