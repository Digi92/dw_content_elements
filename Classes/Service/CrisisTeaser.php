<?php
namespace Denkwerk\DwContentElements\Service;

class CrisisTeaser {

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var \Denkwerk\DwSiteexport\Service\Log
     */
    protected $logger;

    public function __construct() {
        /**
         * @var $logger \Denkwerk\DwSiteexport\Service\Log
         */
        $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\\DwSiteexport\\Service\\Log');
        $this->logger->init();
    }

    /**
     * @param mixed $path
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = trim($content);
        return $this;
    }

    /**
     * @return \Denkwerk\DwSiteexport\Service\File|null
     */
    public function save() {

        $r = null;

        if(strlen($this->path) > 0 && strlen($this->content) > 0) {

            /**
             * @var $configPages \Denkwerk\DwSiteexport\Service\Config\Page
             */
            $configPages = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\\DwSiteexport\\Service\\Config\\Page');
            $configPages->setPageUid($GLOBALS['TSFE']->id);
            $context = $configPages->getContext();

            if (is_dir(PATH_site) === true && is_writable(PATH_site) === true){
                /**
                 * @var $file \Denkwerk\DwSiteexport\Service\File
                 */
                $file = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\\DwSiteexport\\Service\\File');
                $file->setPathRoot(PATH_site)
                    ->setPath(sprintf($this->path, $context))
                    ->setFileContent($this->content)
                    ->create();

                if(is_file($file->getAbsolutePath()) === true) {
                    $this->logger->log('create file ' . $file->getAbsolutePath());
                }

                if(is_file($file->getAbsolutePath()) === false) {
                    $this->logger->log('can not create file ' . $file->getAbsolutePath(), \Zend_Log::ERR);
                }

                $r = $file;
            }

            if (is_dir(PATH_site) === false || is_writable(PATH_site) === false) {
                $this->logger->log('dir ' . PATH_site . ' for crisis-teaser not exists or is not writable', \Zend_Log::ERR);
            }

        }

        return $r;
    }

}