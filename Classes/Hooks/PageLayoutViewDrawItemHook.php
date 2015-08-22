<?php

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
 *
 * @package dw_content_elements
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class PageLayoutViewDrawItemHook implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface {

	/**
	 * Preprocesses the preview rendering of a content element.
	 *
	 * @param	\TYPO3\CMS\Backend\View\PageLayoutView 	$parentObject:  Calling parent object
	 * @param	boolean         $drawItem:      Whether to draw the item using the default functionalities
	 * @param	string	        $headerContent: Header content
	 * @param	string	        $itemContent:   Item content
	 * @param	array		$row:           Record row of tt_content
	 * @return	void
	 */
	public function preProcess(\TYPO3\CMS\Backend\View\PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {

		//Get all config files
		$path = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\DwContentElements\Utility\Pathes');
		$contentElements = $path->getAllDirFiles(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dw_content_elements_source') . '/Configuration/Elements');

		if(isset($contentElements[ucfirst($row['CType'])])) {
			$drawItem = FALSE;
			$itemContent = '<table style="font-size: 11px;">';

			//Load element config
			$elementConfig = \Denkwerk\DwContentElements\Service\Ini::getInstance()
				->setConfigFile($contentElements[ucfirst($row['CType'])])
				->loadConfig();

			//Get all showitem fields
			$fields = self::getMainFields($elementConfig['fields'], 'tt_content', $row);

			//Set content element title
			$headerContent = '<b>' . $elementConfig['title'] . '</b><br><br>';

			//Set preview for the showitem fields
			$count = 0;
			foreach($fields as $field) {
				$itemContent .= self::renderFieldPreview($field['name'], $row, (isset($field['label']) && empty($field['label']) === false ? $field['label'] : $parentObject->itemLabels[$field['name']]));

				$count++;
				if($count >= 10){
					$itemContent .= '<td><b>...</b></td>';
					break;
				}
			}
			$itemContent .= '</table>';
		}

	}

	/**
	 * Render a preview for the giving field
	 *
	 * @param $fieldName
	 * @param $row
	 * @param string $itemLabels
	 * @param string $fieldTable
	 * @return string
	 */
	public function renderFieldPreview($fieldName, $row, $itemLabels = '', $fieldTable = 'tt_content') {
		$filedContent = '';
		$fieldValue = $row[$fieldName];

		if(isset($fieldName) && isset($fieldValue)) {
			$filedContent .= '<tr>';
			$fieldConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getTcaFieldConfiguration($fieldTable, $fieldName);

			switch ($fieldConfig['type']) {
				case "input":

					//If field is has a link wizard
					if(isset($fieldConfig['wizards']['link'])) {
						$urlService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Denkwerk\\DwContentElements\\Service\\Url');
						$fieldValue = $urlService->setPageUid($fieldValue)->getUrl();
					}

					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td><td>' . strip_tags((string)$fieldValue) . '</td>';
					break;
				case "text":
					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td><td>' . substr(strip_tags((string)$fieldValue), 0, 50) . (strlen((string)$fieldValue) > 50 ? '...' : '') . '</td>';
					break;
				case "check":
					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td><td>' . ((bool)$fieldValue ? '&#10004;' : '&#10008;'). '</td>';
					break;
				case "radio":
					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td><td>' . (string)$fieldValue . '</td>';
					break;
				case "select":
					//TODO: Wrong preview if the select use mm!!!
					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td>';
					$items = array();

					//Get all items
					$formEngine = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Form\\FormEngine');
					$items = $formEngine->getSelectItems('tt_content', $fieldName, $row, array('fieldConf' => $GLOBALS['TCA'][$fieldTable]['columns'][$fieldName], 'fieldTSConfig' => NULL));

					switch($fieldConfig['renderMode']) {
						case "checkbox":
							$value = explode(',', $fieldValue);
							foreach($items as $item) {
								$filedContent .= '<td>' . $item[0] . '</td><td>' . (in_array($item[1], $value) ? '&#10004;' : '&#10008;'). '</td>';
							}
							break;
						case "singlebox":
							$value = explode(',', $fieldValue);
							foreach($items as $item) {
								$filedContent .= '<td>' . $item[0] . '</td><td>' . (in_array($item[1], $value) ? '&#10004;' : '&#10008;'). '</td>';
							}
							break;
						default:
							foreach($items as $item) {
								if($item[1] === $fieldValue) {
									$filedContent .= '<td>' .$item[0] . ' &#10004;</td>';
								}
							}
					}

					break;
				case "group":
					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td><td>' . (string)$fieldValue . '</td>';
					break;
				case "inline":
					$filedContent .= '<td style="padding-right: 5px;"><b>' . $itemLabels . '</b></td>';
					if(isset($fieldConfig['foreign_table']) && $fieldConfig['foreign_table'] === 'sys_file_reference'){
						//TODO: Need to add function to set the file title
						$filePreview = \TYPO3\CMS\Backend\Utility\BackendUtility::thumbCode($row, $fieldTable, $fieldName, $GLOBALS['BACK_PATH'], '', NULL, 0, '', '', TRUE);
						$filedContent .= '<td>';
						$filedContent .= ($filePreview != '' ? $filePreview : 'No File');
						$filedContent .= '</td>';
					} else {

						if(isset($fieldConfig['foreign_table'])) {
							$contentObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
							$contentObj->data = $row;
							$count = 0;
							$filedContent .= '<td style="padding-right: 5px;">';
							foreach(\Denkwerk\DwContentElements\Service\IrreService::getRelations($contentObj, $fieldConfig['foreign_table']) as $item) {
								$irreItemLabel = $item[$GLOBALS['TCA'][$fieldConfig['foreign_table']]['ctrl']['label']];
								$filedContent .= ($irreItemLabel != '' ? substr((string)$irreItemLabel, 0, 25) . (strlen((string)$irreItemLabel) > 25 ? '...' : '') : \TYPO3\CMS\Backend\Utility\BackendUtility::getNoRecordTitle(TRUE));
								$filedContent .= '</br>';

								$count++;
								if($count >= 5){
									$filedContent .= '...';
									break;
								}

							}
							$filedContent .= '</td>';

						}

					}

					break;
				default:
					$filedContent .= '<td style="padding-right: 5px;"><b/>' . $itemLabels . '</b></td><td>' . $fieldValue . '</td>';
			}
			$filedContent .= '</tr>';
		}

		return $filedContent;
	}

	/**
	 * Created an array list with all fields from the showitem string
	 * Function base from TYPO3\CMS\Backend\Form\FormEngine function "getMainFields" line:864
	 *
	 * @param $itemList
	 * @param $table
	 * @param array $row
	 * @return array
	 */
	public function getMainFields($itemList, $table, array $row) {
		$result = array();

		$fieldsArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $itemList, TRUE);

		// Set fields and replace palettes and divs
		foreach ($fieldsArray as $fieldInfo) {

			// Exploding subparts of the field configuration:
			$field = self::explodeSingleFieldShowItemConfiguration($fieldInfo);

			if (!in_array($field['fieldName'], $excludeElements=array())) {

				// If field exist add this to result
				if ($GLOBALS['TCA'][$table]['columns'][$field['fieldName']]) {

					//Translate the label
					if($field['fieldLabel'] !== NULL && strpos($field['fieldLabel'], "LLL:EXT:") !== false){
						$field['fieldLabel'] = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($field['fieldLabel'], '');
					}

					array_push($result, array('name' => $field['fieldName'], 'label' => $field['fieldLabel']));

				}
				elseif ($field['fieldName'] == '--div--') {
					// Do nothing!
				}
				elseif ($field['fieldName'] == '--palette--') {

					if ($field['paletteName']) {
						// Load the palette TCEform elements
						if ($GLOBALS['TCA'][$table] && is_array($GLOBALS['TCA'][$table]['palettes'][$field['paletteName']])) {
							$palettesItemList = $GLOBALS['TCA'][$table]['palettes'][$field['paletteName']]['showitem'];
							if ($palettesItemList) {
								// Call the palette showitem with the function "getMainFields" and add the result to $result
								foreach(self::getMainFields($palettesItemList, 'tt_content', $row) as $field){
									array_push($result, $field);
								}
							}
						}
					}

				}
			}
		}
		return $result;
	}


	/**
	 * A single field of TCA 'types' 'showitem' can have four semicolon separated configuration options:
	 *   fieldName: Name of the field to be found in TCA 'columns' section
	 *   fieldLabel: An alternative field label
	 *   paletteName: Name of a palette to be found in TCA 'palettes' section that is rendered after this field
	 *   extra: Special configuration options of this field
	 *
	 * @param string $field Semicolon separated field configuration
	 * @throws \RuntimeException
	 * @return array
	 */
	protected function explodeSingleFieldShowItemConfiguration($field) {
		$fieldArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $field);
		if (empty($fieldArray[0])) {
			throw new \RuntimeException('Field must not be empty', 1426448465);
		}
		return array(
			'fieldName' => $fieldArray[0],
			'fieldLabel' => $fieldArray[1] ?: NULL,
			'paletteName' => $fieldArray[2] ?: NULL,
		);
	}
}
