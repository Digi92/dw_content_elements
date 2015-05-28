<?php
namespace Denkwerk\DwContentElements\Service;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2014 Sascha Zander <sascha.zander@denkwerk.com>, denkwerk
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
 * IrreService
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class IrreService {

	/**
	 *  Set data for irre tx_dwc_two_column_text
	 *
	 * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj
	 * @param string $tableName
	 * @return array
	 */
	public function getRelations($contentObj, $tableName){
		if($contentObj->data[$tableName] > 0){

			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'*',
				$tableName,
				'foreign_uid = ' . $contentObj->data['uid'] . $contentObj->enableFields($tableName),
				'',
				'sorting'
			);

			$result = array();
			foreach($rows as $row){
				array_push($result, $this->getContentElements($contentObj, $row, $tableName));
			}

		}
		return $result;
	}

    /**
     * Get "tt_content" content elements of the relations if it exist a row "content_elements"
     *
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $contentObj
     * @param array $data
     * @param string $parentTable
     * @return array
     */
    public function getContentElements($contentObj, $data, $parentTable) {

        if(is_array($data)) {

            if($data['content_elements'] != null && empty($data['content_elements']) === false) {
                $elementRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
                    'uid',
                    'tt_content',
                    'foreign_uid = ' . $data['uid'] . $contentObj->enableFields('tt_content') . 'AND parent_table = "' . $parentTable . '"',
                    '',
                    'sorting'
                );
                $contentElements = array();
                foreach($elementRows as $elementRow) {
                    array_push($contentElements, $elementRow['uid']);
                }
                $data['content_elements'] = $contentElements;
            }
        }

        return $data;
    }

}
