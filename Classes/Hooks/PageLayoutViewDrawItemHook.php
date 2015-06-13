<?php
class PageLayoutViewDrawItemHook implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface {
    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param	tx_cms_layout	$parentObject:  Calling parent object
     * @param	boolean         $drawItem:      Whether to draw the item using the default functionalities
     * @param	string	        $headerContent: Header content
     * @param	string	        $itemContent:   Item content
     * @param	array		$row:           Record row of tt_content
     * @return	void
     */
     public function preProcess(\TYPO3\CMS\Backend\View\PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {
         global $TCA;
        $contentElements = array();
        include(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements_source') . 'setup_content_elements.php');

        if(in_array($row['CType'], $contentElements) === true) {
            $drawItem = FALSE;

            $headerContent = '<p style="padding:0;margin:0;"><strong>' . $TCA['tt_content']['types'][$row['CType']]['tx_dw_content_elements_title'] . '</strong><p>';

            $itemContent = '<table>';

            /**
             * @todo: Das muss dynamisch gelöst werden.
             */
            if(strlen($row['header']) > 0) {
                $itemContent .= $this->drawRow('Header', $row['header']);
            }
            if(strlen($row['subheader']) > 0) {
                $itemContent .= $this->drawRow('Subheader', $row['subheader']);
            }
            if(strlen($row['bodytext']) > 0) {
                $itemContent .= $this->drawRow('Bodytext', $row['bodytext']);
            }

            $hideCols = array('tx_dwc_iframe_width', 'tx_dwc_iframe_height', 'tx_dwc_iframe_class', 'tx_dwc_iframe_frameborder', 'tx_dwc_iframe_scrolling', 'tx_dwc_iframe_id');
            foreach($row AS $key => $value) {
                if(in_array($key, $hideCols) === false && strpos($key, 'tx_dwc_') !== false && preg_match('/tx_dwc.*image.*/', $key) === 0) {
                    if(strlen($value) > 0 && $value !== '0' && $value !== '0.00') {
                        $itemContent .= $this->drawRow($GLOBALS['LANG']->sL($GLOBALS['TCA']['tt_content']['columns'][$key]['label']), $value);
                    }
                }
            }

            $itemContent .= '</table>';

        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return string
     */
    private function drawRow($key, $value) {
        $key = strlen($key) > 0 ? str_replace(':', '', $key) . ':' : '';
        return '<tr><th style="text-align: left;text-wrap: none;width:100px;">' . $key . '</th><td>' . $this->trim_text($value, 100) . '</td></tr>';
    }

    /**
     * trims text to a space then adds ellipses if desired
     * @param string $input text to trim
     * @param int $length in characters to trim to
     * @param bool $ellipses if ellipses (...) are to be added
     * @param bool $strip_html if html tags are to be stripped
     * @return string
     */
    private function trim_text($input, $length, $ellipses = true, $strip_html = true) {
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

}
?>