<?php

namespace Denkwerk\DwContentElements\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Sascha Zander <sascha.zander@denkwerk.com>, denkwerk
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
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Denkwerk\DwContentElements\Utility\Logger;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class IrreService
 * @package Denkwerk\DwContentElements\Service
 */
class IrreService
{
    /**
     *  Set data for Inline Relational Record Editing entry
     *  If set the repositoryName the function will call the magic function "findByForeignUid"
     *
     * @param ContentObjectRenderer $contentObj
     * @param string $tableName Name of the table
     * @param string $repositoryName Name of the repository if any repository exist. (Optional)
     * @return array
     */
    public static function getRelations($contentObj, $tableName, $repositoryName = '')
    {
        $result = array();

        // Check if field "foreign_uid" exists on table
        //$fieldsInDatabase = $GLOBALS['TYPO3_DB']->admin_get_fields($tableName);
        $fieldsInDatabase = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->getSchemaManager()
            ->listTableColumns($tableName);

        if (empty($fieldsInDatabase) === false &&
            array_key_exists("foreign_uid", $fieldsInDatabase)
        ) {
            // If "$repositoryName" is not set. Get the table data by single select
            if ($contentObj->data[$tableName] > 0 &&
                empty($repositoryName)
            ) {
                $foreignUid = $contentObj->data['uid'];
                if (isset($contentObj->data['_LOCALIZED_UID'])) {
                    $foreignUid = $contentObj->data['_LOCALIZED_UID'];
                }

                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable($tableName);

                $queryBuilder->getRestrictions()
                    ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
                    ->add(GeneralUtility::makeInstance(HiddenRestriction::class))
                    ->add(GeneralUtility::makeInstance(StartTimeRestriction::class))
                    ->add(GeneralUtility::makeInstance(EndTimeRestriction::class));

                $rows = $queryBuilder
                    ->select('*')
                    ->from($tableName)
                    ->where('foreign_uid = ' . $foreignUid)->orderBy('sorting')->executeQuery();

                foreach ($rows as $row) {
                    // Get "tt_content" content elements of the relations if it exist a row "content_elements"
                    array_push($result, self::getContentElements($contentObj, $row, $tableName));
                }
            }

            // Get the IRRE data by the repository magic function "findByForeignUid"
            if ($contentObj->data[$tableName] > 0 &&
                empty($repositoryName) === false
            ) {
                $foreignUid = $contentObj->data['uid'];
                if (isset($contentObj->data['_LOCALIZED_UID'])) {
                    $foreignUid = $contentObj->data['_LOCALIZED_UID'];
                }

                /** @var Repository $repository */
                $repository = GeneralUtility::makeInstance(Repository::class);

                // Get the table data by the given repository
                $rows = $repository->findByForeignUid($foreignUid);

                if (empty($rows) === false) {
                    $result = $rows;
                }
            }
        } else {
            // Write into the sys_log about the missing field
            Logger::simpleErrorLog(
                'DWC: IRRE Service: Column "foreign_uid" not found on table "' . $tableName . '"',
                $tableName,
                $contentObj->data['uid'],
                $contentObj->data['pid']
            );
        }

        return $result;
    }

    /**
     * Get "tt_content" content elements of the relations if it exist a row "content_elements"
     * @deprecated since 1.2 will be removed in 2.0
     * Please use the "getRelations($repositoryName)" function
     *
     * @param ContentObjectRenderer $contentObj
     * @param array $data
     * @param string $parentTable
     * @return array
     */
    public static function getContentElements($contentObj, $data, $parentTable)
    {
        if (is_array($data)
        ) {
            if (isset($data['content_elements']) &&
                $data['content_elements'] != null &&
                empty($data['content_elements']) === false) {
                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('tt_content');

                $queryBuilder->getRestrictions()
                    ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
                    ->add(GeneralUtility::makeInstance(HiddenRestriction::class))
                    ->add(GeneralUtility::makeInstance(StartTimeRestriction::class))
                    ->add(GeneralUtility::makeInstance(EndTimeRestriction::class));

                $elementRows = $queryBuilder
                    ->select('uid')
                    ->from('tt_content')
                    ->where('foreign_uid = ' . $data['uid'])->orderBy('sorting')->executeQuery();

                $contentElements = array();
                foreach ($elementRows as $elementRow) {
                    array_push($contentElements, $elementRow['uid']);
                }
                $data['content_elements'] = $contentElements;
            }
        }

        return $data;
    }
}
