<?php

namespace Denkwerk\DwContentElements\Controller;

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

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use Denkwerk\DwContentElements\Service\FileService;
use Denkwerk\DwContentElements\Service\IniProviderService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Class BackendController
 * @package Denkwerk\DwContentElements\Controller
 */
class BackendController extends ActionController
{
    /**
     * @param ModuleTemplateFactory $moduleTemplateFactory
     */
    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory
    ) {
    }
    
    /**
     * First load action, will display information about the creation of a content element
     * or send the user to the right step of create the source extension
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setTitle('Dw Content Elements: Overview');
    
        /** @var IniProviderService $iniProviderService */
        $iniProviderService = GeneralUtility::makeInstance(IniProviderService::class);

        // Load all provider configurations as array
        $providers = $iniProviderService->loadProvider();

        // Check if source extension is enabled
        if (is_array($providers) &&
            count($providers) === 0
        ) {
            // Check if source extension exists
            if (!is_dir(Environment::getPublicPath() . '/typo3conf/ext/dw_content_elements_source')) {
                return $this->redirect(
                    'createSourceExt',
                    'Backend',
                    'dw_content_elements'
                );
            } else {
                return $this->redirect(
                    'loadSourceExt',
                    'Backend',
                    'dw_content_elements'
                );
            }
        }
        return $moduleTemplate->renderResponse('Index');
    }

    /**
     * Action for the create of the source extension
     *
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function createSourceExtAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setTitle('Dw Content Elements: Create source extension');
        
        if ($this->request->hasArgument('createSourceExt')) {
            /**
             * @var FileService $fileService
             */
            $fileService = GeneralUtility::makeInstance(FileService::class);
            $fileService->setSourceExtensionDirectory(
                Environment::getPublicPath() . '/typo3conf/ext/dw_content_elements_source/'
            );
            $success = $fileService->createSourceExt();

            if ($success) {
                return $this->redirect(
                    'loadSourceExt',
                    'Backend',
                    'dw_content_elements',
                    [
                        'hasCreatedSourceExt' => true,
                    ]
                );
            } else {
                $moduleTemplate->assign('createFail', true);
            }
        }

        return $moduleTemplate->renderResponse('CreateSourceExt');
    }

    /**
     * Action for the info to install the source extension at the extension manager
     *
     * @return void
     * @throws NoSuchArgumentException
     */
    public function loadSourceExtAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setTitle('Dw Content Elements: Load source extension');

        $hasCreatedSourceExt = false;

        if ($this->request->hasArgument('hasCreatedSourceExt')) {
            $hasCreatedSourceExt = $this->request->getArgument('hasCreatedSourceExt');
        }

        $moduleTemplate->assign('hasCreatedSourceExt', $hasCreatedSourceExt);
        return $moduleTemplate->renderResponse('LoadSourceExt');
    }
}
