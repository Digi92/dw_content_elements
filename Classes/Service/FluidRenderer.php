<?php
namespace Denkwerk\DwContentElements\Service;

class FluidRenderer {

    /**
     * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext
     */
    protected $controllerContext;

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext
     */
    public function setControllerContext(\TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext $controllerContext) {
        $this->controllerContext = $controllerContext;
        return $this;
    }

    /**
     * Creates a fluid instance with given template-file and controller-settings
     * @param string $file Path below Template-Root-Path (Resources/Private/Templates/$file)
     * @return \TYPO3\CMS\Fluid\View\StandaloneView Fluid-Template-Renderer
     */
    public function getRenderInstance($template) {

        /**
         * @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager'
         */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        /**
         * @var $configurationManager \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
         */
        $configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\ConfigurationManager');

        /**
         * @var $renderer \TYPO3\CMS\Fluid\View\StandaloneView
         */
        $renderer = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $renderer->setControllerContext($this->controllerContext);
        $conf = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $renderer->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['templateRootPath']) . $template);
        $renderer->setLayoutRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['layoutRootPath']));
        $renderer->setPartialRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($conf['view']['partialRootPath']));
        $renderer->assign('foo', 'bar');
        return $renderer;
    }



}