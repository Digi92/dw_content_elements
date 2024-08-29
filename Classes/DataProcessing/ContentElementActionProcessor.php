<?php
declare(strict_types=1);

namespace Denkwerk\DwContentElements\DataProcessing;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2023 Sascha Zander <sascha.zander@denkwerk.com>
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
use T3docs\Examples\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Class ContentElementActionProcessor
 * @package Denkwerk\DwContentElements\DataProcessing
 */
class ContentElementActionProcessor implements DataProcessorInterface
{
    /**
     * This DataProcessor checks at the call whether in the setting "controllerActionClass" is set and
     * whether this class has a function which is named the same as the current fluid "templateName".
     * If there is a function with the same name, this is executed and the return value is passed to the view.
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ) {
        // Check possible typescript if condition
        if (isset($processorConfiguration['if.']) &&
            !$cObj->checkIf($processorConfiguration['if.'])
        ) {
            return $processedData;
        }

        // Execute function if class and methode exist
        if (isset($processorConfiguration['controllerActionClass']) &&
            isset($contentObjectConfiguration['templateName']) &&
            isset($processedData['data']) &&
            class_exists(trim($processorConfiguration['controllerActionClass'])) === true &&
            method_exists(
                trim($processorConfiguration['controllerActionClass']),
                lcfirst($contentObjectConfiguration['templateName'])
            ) === true
        ) {
            $controllerActionClass = GeneralUtility::makeInstance(
                trim($processorConfiguration['controllerActionClass'])
            );
            // $elementsController->e1102ThreeColumnTile($contentObj);
            $contentElementVariable = $controllerActionClass->{lcfirst($contentObjectConfiguration['templateName'])}($processedData['data']);

            // Add result to view
            if (is_array($contentElementVariable)) {
                $processedData = $processedData + $contentElementVariable;
            }
        }

        return $processedData;
    }
}
