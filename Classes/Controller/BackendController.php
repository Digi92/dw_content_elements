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

/**
 * BackendController
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * First load action, will display information about the creation of a content element
     * or send the user to the right step of create the source extension
     *
     * @return void
     */
    public function indexAction()
    {

        //Source Extension Installiert
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('dw_content_elements_source') === false) {
            //Source Extension Ordner existiert
            if (!is_dir(PATH_typo3conf . 'ext/dw_content_elements_source')) {
                $this->forward('createSourceExt');
            } else {
                $this->forward('loadSourceExt');
            }
        }
    }

    /**
     * Action for the create of the source extension
     *
     * @return void
     */
    public function createSourceExtAction()
    {

        if ($this->request->hasArgument('createSourceExt')) {
            $success = false;

            /**
             * @var \Denkwerk\DwContentElements\Service\FileService $fileService
             */
            $fileService = $this->objectManager->get(
                'Denkwerk\\DwContentElements\\Service\\FileService'
            );
            $fileService->setSourceExtensionDirectory(PATH_typo3conf . 'ext/dw_content_elements_source/');
            $success = $fileService->createSourceExt();

            if ($success) {
                $this->forward(
                    'loadSourceExt',
                    'Backend',
                    null,
                    array(
                        'hasCreatedSourceExt' => true
                    )
                );
            } else {
                $this->view->assign('createFail', true);
            }
        }
    }

    /**
     * Action for the info to install the source extension at the extension manager
     *
     * @return void
     */
    public function loadSourceExtAction()
    {
        $hasCreatedSourceExt = false;

        if ($this->request->hasArgument('hasCreatedSourceExt')) {
            $hasCreatedSourceExt = $this->request->getArgument('hasCreatedSourceExt');
        }
        $this->view->assign('hasCreatedSourceExt', $hasCreatedSourceExt);
    }
}
